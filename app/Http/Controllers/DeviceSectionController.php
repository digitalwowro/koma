<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\Fields\Factory;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class DeviceSectionController extends Controller
{
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
        $data['owner_id'] = $request->user()->id;

        return $data;
    }

    public function index()
    {
        return view('device-section.index');
    }

    public function show($id)
    {
        try {
            $deviceSection = DeviceSection::findOrFail($id);

            $this->authorize('view', $deviceSection);

            return view('device-section.show', compact('deviceSection'));
        } catch (Exception $e) {
            return redirect()
                ->route('device-section.index')
                ->withError('Could not find device section');
        }
    }

    public function create()
    {
        return view('device-section.create');
    }

    public function store(Request $request)
    {
        try {
            DeviceSection::create($this->getFields($request));

            return redirect()
                ->route('device-section.index')
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

            return view('device-section.edit', compact('deviceSection'));
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
                ->route('device-section.index')
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
                ->route('device-section.index')
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
}
