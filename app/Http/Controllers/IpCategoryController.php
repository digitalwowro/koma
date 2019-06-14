<?php

namespace App\Http\Controllers;

use App\Exceptions\AlreadyHasPermissionException;
use App\IpCategory;
use App\Permission;
use App\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IpCategoryController extends Controller
{
    public function index()
    {
        return view('ip-category.index');
    }

    protected function getFields(Request $request)
    {
        $data = $request->only('title');

        $data['owner_id'] = $request->user()->id;

        return $data;
    }

    public function create()
    {
        return view('ip-category.create');
    }

    public function store(Request $request)
    {
        try {
            $ipCategory = IpCategory::create($this->getFields($request));

            if (!$request->user()->isAdmin()) {
                $request->user()->permissions()->create([
                    'resource_type' => Permission::RESOURCE_TYPE_IP_CATEGORY,
                    'resource_id' => $ipCategory->id,
                    'grant_type' => Permission::GRANT_TYPE_OWNER,
                ]);
            }

            return redirect()
                ->route('ip-category.index')
                ->withSuccess('IP category has been added');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving IP Category');
        }
    }

    public function edit($id)
    {
        try {
            $ipCategory = IpCategory::findOrFail($id);

            $this->authorize('manage', $ipCategory);

            return view('ip-category.edit', compact('ipCategory'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find IP Category');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ipCategory = IpCategory::findOrFail($id);

            $this->authorize('manage', $ipCategory);

            $ipCategory->update($this->getFields($request));

            return redirect()
                ->route('ip-category.index')
                ->withSuccess('IP category has been updated');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Could not find IP Category');
        }
    }

    public function destroy($id)
    {
        try {
            $ipCategory = IpCategory::findOrFail($id);

            $this->authorize('manage', $ipCategory);

            $ipCategory->delete();

            return redirect()
                ->route('ip-category.index')
                ->withSuccess('IP category has been deleted');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find IP Category');
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

            IpCategory::findOrFail($id);
            $user = User::findOrFail($request->input('user_id'));
            $grantType = intval($request->input('grant_type'));

            $this->validateIpCategoryPermission($grantType, $user, $id);

            $permission = $user->permissions()->create([
                'resource_type' => Permission::RESOURCE_TYPE_IP_CATEGORY,
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
                'error' => 'User already has access to this IP category',
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Could not share IP category',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
