<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\IpAddress;
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

            $this->authorize('edit', $deviceSection);

            return view('devices.create', compact('deviceSection'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function store($type, Request $request, IpAddress $ipAddress)
    {
        try
        {
            $section = $this->deviceSection->findOrFail($type);

            $this->authorize('edit', $section);

            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $device = $this->model->create([
                'section_id' => $type,
                'data'       => $data,
            ]);

            if (isset($data['ips']) && is_array($data['ips']))
            {
                $ips = $ipAddress->whereIn('id', $data['ips'])->get();

                foreach ($ips as $ip)
                {
                    if ($ip->device_id == $device->id)
                    {
                        continue;
                    }

                    if ($ip->assigned())
                    {
                        throw new \Exception("IP {\$ip} is already assigned!");
                    }

                    $ip->device_id = $device->id;
                    $ip->save();
                }
            }

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

            $this->authorize('edit', $device);

            return view('devices.edit', compact('deviceSection', 'device'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->route('devices.index', $type)
                ->withError('Could not find device');
        }
    }

    public function update($id, Request $request, IpAddress $ipAddress)
    {
        try
        {
            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $device = $this->model->findOrFail($id);

            $this->authorize('edit', $device);

            $device->data = $data;

            $device->save();

            $device->ips()->update(['device_id' => null]);

            if (isset($data['ips']) && is_array($data['ips']))
            {
                $ips = $ipAddress->whereIn('id', $data['ips'])->get();

                foreach ($ips as $ip)
                {
                    if ($ip->device_id == $device->id)
                    {
                        continue;
                    }

                    if ($ip->assigned())
                    {
                        throw new \Exception("IP {\$ip} is already assigned!");
                    }

                    $ip->device_id = $device->id;
                    $ip->save();
                }
            }

            return redirect()
                ->route('devices.index', $device->section_id)
                ->withSuccess('Device has been updated');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error updating device: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try
        {
            $device = $this->model->findOrFail($id);

            $this->authorize('edit', $device);

            $device->delete();

            return redirect()
                ->back()
                ->withSuccess('Device has been deleted');
        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()
                ->withError('Error deleting device: ' . $e->getMessage());
        }
    }

    public function show($type, $id)
    {
        try
        {
            $deviceSection = $this->deviceSection->findOrFail($type);
            $device = $this->model->findOrFail($id);

            $this->authorize('view', $device);

            return view('devices.show', compact('device', 'deviceSection'));
        }
        catch (\Exception $e)
        {
            return redirect()
                ->route('devices.index', $type)
                ->withError('Could not find device');
        }
    }

}
