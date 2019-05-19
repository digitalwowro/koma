<?php

namespace App\Http\Controllers;

use App\IpCategory;
use App\Permission;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IpCategoryController extends Controller
{
    public function index()
    {
        return view('ip-categories.index');
    }

    public function create()
    {
        return view('ip-categories.create');
    }

    public function store(Request $request)
    {
        try {
            $ipCategory = IpCategory::create($request->input());

            if (!$request->user()->isAdmin()) {
                $request->user()->permissions()->create([
                    'resource_type' => Permission::RESOURCE_TYPE_IP_CATEGORY,
                    'resource_id' => $ipCategory->id,
                    'grant_type' => Permission::GRANT_TYPE_OWNER,
                ]);
            }

            return redirect()
                ->route('ip-categories.index')
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

            return view('ip-categories.edit', compact('ipCategory'));
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

            $ipCategory->update($request->input());

            return redirect()
                ->route('ip-categories.index')
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
                ->route('ip-categories.index')
                ->withSuccess('IP category has been deleted');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find IP Category');
        }
    }
}
