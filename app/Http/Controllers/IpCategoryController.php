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
    protected function getFields(Request $request)
    {
        $data = $request->only('title');

        $data['owner_id'] = $request->user()->id;

        return $data;
    }

    public function index()
    {
        return view('ip-category.index');
    }

    public function show($id)
    {
        try {
            $category = IpCategory::findOrFail($id);

            $this->authorize('view', $category);

            return view('ip-category.show', compact('category'));
        } catch (Exception $e) {
            return redirect()
                ->route('ip-category.index')
                ->withError('Could not find IP Category');
        }
    }

    public function create()
    {
        return view('ip-category.create');
    }

    public function store(Request $request)
    {
        try {
            IpCategory::create($this->getFields($request));

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
            $category = IpCategory::findOrFail($id);

            $this->authorize('share', $category);

            $user = User::findOrFail($request->input('user_id'));
            $grantType = $request->input('grant_type', []);

            app('share')->share($user, $category, $grantType);

            if ($request->isXmlHttpRequest()) {
                return response()->json(['success' => true]);
            } else {
                return redirect()->back();
            }
        } catch (AlreadyHasPermissionException $e) {
            return response()->json([
                'error' => 'User already has access to this IP category',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not share IP category',
            ]);
        }
    }
}
