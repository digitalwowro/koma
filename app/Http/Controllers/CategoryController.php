<?php

namespace App\Http\Controllers;

use App\Category;
use App\Fields\Factory;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected function getFields(Request $request)
    {
        $data = $request->only('title', 'icon', 'fields');

        $data['owner_id'] = $request->user()->id;

        return $data;
    }

    public function index()
    {
        return view('category.index');
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            $this->authorize('view', $category);

            return view('category.show', compact('category'));
        } catch (Exception $e) {
            return redirect()
                ->route('category.index')
                ->withError('Could not find category');
        }
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        try {
            Category::create($this->getFields($request));

            return redirect()
                ->route('category.index')
                ->withSuccess('Category has been added');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving category');
        }
    }

    public function edit($id)
    {
        try {
            $category = Category::findOrFail($id);

            $this->authorize('manage', $category);

            return view('category.edit', compact('category'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find category');
        }
    }

    public function update(Request $request, $id)
    {
        //try {
            $category = Category::findOrFail($id);

            $this->authorize('manage', $category);

            $category->update($this->getFields($request));

            return redirect()
                ->route('category.index')
                ->withSuccess('Category has been updated');
        //} catch (Exception $e) {
        //    return redirect()
        //        ->back()
        //        ->withInput()
        //        ->withError('Could not find category');
        //}
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            $this->authorize('manage', $category);

            $category->delete();

            return redirect()
                ->route('category.index')
                ->withSuccess('Category has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find category');
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
}
