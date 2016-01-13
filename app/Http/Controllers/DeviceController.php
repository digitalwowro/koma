<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeviceController extends Controller
{
    /**
     * @var \App\Device
     */
    private $model;

    /**
     * @var \App\DeviceSection
     */
    private $deviceSection;

    public function __construct(Device $model, DeviceSection $deviceSection)
    {
        $this->model = $model;
        $this->deviceSection = $deviceSection;
    }

    public function index($type)
    {
        try
        {
            $deviceSection = $this->deviceSection->findOrFail($type);
            $devices       = $deviceSection->devices;
            $colspan       = 1;

            foreach ($deviceSection->fields as $field)
            {
                if ($field->showInDeviceList())
                {
                    $colspan++;
                }
            }

            return view('devices.index', compact('deviceSection', 'devices', 'colspan'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function create($type)
    {
        try
        {
            $deviceSection = $this->deviceSection->findOrFail($type);

            return view('devices.create', compact('deviceSection'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function store($type, Request $request)
    {
        try
        {
            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $this->model->create([
                'section_id' => $type,
                'data'       => $data,
            ]);

            return redirect()
                ->route('devices.index', $type)
                ->withSuccess('Device has been added');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving device');
        }
    }

    public function edit($type, $id)
    {
        try
        {
            $deviceSection = $this->deviceSection->findOrFail($type);
            $device        = $this->model->findOrFail($id);

            return view('devices.edit', compact('deviceSection', 'device'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device');
        }
    }

    public function update($id, Request $request)
    {
        try
        {
            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $device = $this->model->findOrFail($id);

            $device->data = $data;

            $device->save();

            return redirect()
                ->route('devices.index', $device->section_id)
                ->withSuccess('Device has been updated');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error updating device');
        }
    }

    public function destroy($id)
    {
        try
        {
            $device = $this->model->findOrFail($id);

            $device->delete();

            return redirect()
                ->back()
                ->withSuccess('Device has been deleted');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device');
        }
    }

}
