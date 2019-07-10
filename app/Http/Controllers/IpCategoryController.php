<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IpCategory;
use Exception;
use Illuminate\Http\Request;

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
}
