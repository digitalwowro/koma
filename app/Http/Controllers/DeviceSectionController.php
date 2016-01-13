<?php

namespace App\Http\Controllers;

use App\DeviceSection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeviceSectionController extends Controller
{
    /**
     * @var \App\DeviceSection
     */
    private $model;

    public function __construct(DeviceSection $model)
    {
        $this->model = $model;
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
        $this->model->create($request->input());

        return redirect()
            ->route('device-sections.index')
            ->withSuccess('Device section has been added');
    }

    public function edit($id)
    {
        try
        {
            $deviceSection = $this->model->findOrFail($id);

            return view('device-sections.edit', compact('deviceSection'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $deviceSection = $this->model->findOrFail($id);

            $deviceSection->update($request->input());

            return redirect()
                ->route('device-sections.index')
                ->withSuccess('Device section has been updated');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function destroy($id)
    {
        try
        {
            $deviceSection = $this->model->findOrFail($id);

            $deviceSection->delete();

            return redirect()
                ->route('device-sections.index')
                ->withSuccess('Device section has been deleted');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }
}
