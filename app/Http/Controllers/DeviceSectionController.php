<?php

namespace App\Http\Controllers;

use App\DeviceSection;
use App\Fields\Factory;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class DeviceSectionController extends Controller
{
    protected function sanitizeCategories(string $json) : string
    {
        try {
            $categories = json_decode($json, true);

            if (!is_array($categories)) {
                throw new Exception('Invalid input');
            }

            return collect($categories)
                ->filter(function ($item) {
                    if (empty($item['id']) || empty($item['text']) || empty($item['parent'])) {
                        return false;
                    }

                    if (preg_match('/^[A-Za-z0-9]{8}$/', $item['id']) === false) {
                        return false;
                    }

                    return true;
                })
                ->map(function ($item) {
                    return [
                        'id' => $item['id'],
                        'text' => $item['text'],
                        'parent' => $item['parent'],
                    ];
                })
                ->toJson();
        } catch (Exception $e) {
            return '[]';
        }
    }

    protected function getFields(Request $request)
    {
        $data = $request->only('title', 'icon', 'fields', 'categories');

        $data['categories'] = $this->sanitizeCategories($data['categories']);
        $data['owner_id'] = $request->user()->id;

        return $data;
    }

    public function index()
    {
        app('encrypt')->disableExceptions();

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
            app('encrypt')->disableExceptions();

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
            app('encrypt')->disableExceptions();

            $type = $request->input('type');
            $index = $request->input('index');
            $field = Factory::generate('', 'tmp', $type);

            return $field->renderOptions($index);
        } catch (Exception $e) {
            //
        }
    }
}
