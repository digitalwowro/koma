<?php

namespace App\Http\Controllers;

use App\IpCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IpCategoryController extends Controller
{
    /**
     * @var \App\IpCategory
     */
    private $model;

    public function __construct(IpCategory $model)
    {
        $this->model = $model;

        $this->authorize('admin');
    }

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
        try
        {
            $this->model->create($request->input());

            return redirect()
                ->route('ip-categories.index')
                ->withSuccess('IP category has been added');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving IP Category');
        }
    }

    public function edit($id)
    {
        try
        {
            $ipCategory = $this->model->findOrFail($id);

            return view('ip-categories.edit', compact('ipCategory'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find IP Category');
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $ipCategory = $this->model->findOrFail($id);

            $ipCategory->update($request->input());

            return redirect()
                ->route('ip-categories.index')
                ->withSuccess('IP category has been updated');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Could not find IP Category');
        }
    }

    public function destroy($id)
    {
        try
        {
            $ipCategory = $this->model->findOrFail($id);

            $ipCategory->delete();

            return redirect()
                ->route('ip-categories.index')
                ->withSuccess('IP category has been deleted');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find IP Category');
        }
    }
}
