<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\Exceptions\AlreadyHasPermissionException;
use App\Fields\Factory;
use App\Http\Controllers\Traits\ManagesPermissions;
use App\Permission;
use App\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeviceSectionController extends Controller
{
    use ManagesPermissions;

    protected function getCategories(Request $request)
    {
        $categories = $request->input('categories');
        $ids = $request->input('categoryid');
        $result = [];

        if (!is_array($categories) || !is_array($ids)) {
            return $result;
        }

        foreach ($categories as $id => $category) {
            if (isset($ids[$id])) {
                $result[$ids[$id]] = $category;
            }
        }

        return $result;
    }

    protected function getFields(Request $request)
    {
        $data = $request->only('title', 'icon', 'fields');

        $data['categories'] = $this->getCategories($request);
        $data['created_by'] = $request->user()->id;

        return $data;
    }

    public function index()
    {
        return view('device-sections.index');
    }

    public function create()
    {
        return view('device-sections.create');
    }

    public function store(Request $request)
    {
        try {
            $deviceSection = DeviceSection::create($this->getFields($request));

            if (!$request->user()->isAdmin()) {
                $request->user()->permissions()->create([
                    'resource_type' => Permission::RESOURCE_TYPE_DEVICES_SECTION,
                    'resource_id' => $deviceSection->id,
                    'grant_type' => Permission::GRANT_TYPE_OWNER,
                ]);
            }

            return redirect()
                ->route('device-sections.index')
                ->withSuccess('Device section has been added');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving device section');
        }
    }

    public function edit($id)
    {
        try {
            $deviceSection = DeviceSection::findOrFail($id);

            $this->authorize('manage', $deviceSection);

            return view('device-sections.edit', compact('deviceSection'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $deviceSection = DeviceSection::findOrFail($id);

            $this->authorize('manage', $deviceSection);

            $deviceSection->update($this->getFields($request));

            $categoryIds = array_keys($deviceSection->categories);

            $invalid = [];

            $deviceSection->devices()
                ->pluck('category_id', 'id')
                ->each(function ($categoryId, $id) use ($categoryIds, &$invalid) {
                    if ($categoryId && !in_array($categoryId, $categoryIds)) {
                        $invalid[] = $id;
                    }
                });

            if (count($invalid)) {
                Device::whereIn('id', $invalid)->update(['category_id' => null]);
            }

            return redirect()
                ->route('device-sections.index')
                ->withSuccess('Device section has been updated');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Could not find device section');
        }
    }

    public function destroy($id)
    {
        try {
            $deviceSection = DeviceSection::findOrFail($id);

            $this->authorize('manage', $deviceSection);

            $deviceSection->delete();

            return redirect()
                ->route('device-sections.index')
                ->withSuccess('Device section has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function getOptions(Request $request)
    {
        try {
            $type = $request->input('type');
            $index = $request->input('index');
            $field = Factory::generate('', 'tmp', $type);

            return $field->renderOptions($index);
        } catch (Exception $e) {
            //
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function share($id, Request $request)
    {
        try {
            $this->authorize('superadmin');

            DeviceSection::findOrFail($id);
            $user = User::findOrFail($request->input('user_id'));
            $grantType = intval($request->input('grant_type'));

            $this->validateDeviceSectionPermission($grantType, $user, $id);

            $permission = $user->permissions()->create([
                'resource_type' => Permission::RESOURCE_TYPE_DEVICES_SECTION,
                'resource_id' => $id,
                'grant_type' => $grantType,
            ]);

            $this->deleteRedundantPermissions($permission);

            Permission::flushCache();

            return response()->json([
                'success' => true,
            ]);
        } catch (AlreadyHasPermissionException $e) {
            return response()->json([
                'error' => 'User already has access to this device section',
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Could not share device section',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
