<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\EncryptedStore;
use App\IpSubnet;
use App\Permission;
use App\User;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeviceController extends Controller
{
    public function index($type, $category = null)
    {
        try {
            app('encrypt')->disableExceptions();

            $deviceSection = DeviceSection::findOrFail($type);
            $devices = $deviceSection->devices;

            if ($category) {
                if (!isset($deviceSection->categories[$category])) {
                    return redirect()->route('device.index', $type);
                }

                $devices = $devices->filter(function ($device) use ($category) {
                    return $device->category_id === $category;
                });

                $categoryLabel = $deviceSection->categories[$category];
            }

            $colspan = 1;

            try {
                $filters = json_decode(request()->cookie('device-filters'), true);
            } catch (Exception $e) {
                $filters = [];
            }

            foreach ($deviceSection->fields as $field) {
                if ($field->getType() === 'Status') {
                    $id = $field->getKey();
                    $preselected = $field->getOption('preselected');

                    if ($preselected && !isset($filters[$id])) {
                        $filters[$id] = explode(',', $preselected);
                    }
                }
                if ($field->showInDeviceList()) {
                    $colspan++;
                }
            }

            return view('device.index', compact('deviceSection', 'devices', 'colspan', 'filters', 'category', 'categoryLabel', 'type'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function create($type)
    {
        try {
            app('encrypt')->disableExceptions();

            $deviceSection = DeviceSection::findOrFail($type);

            $this->authorize('create', $deviceSection);

            return view('device.create', compact('deviceSection'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    private function setCategory(Device $device, Request $request, $save = true)
    {
        $categories = $device->section->categories;
        $category = $request->input('category_id');

        if (empty($category)) {
            $device->category_id = null;

            if ($save) {
                $device->save();
            }

            return;
        }

        if (!isset($categories[$category])) {
            throw new Exception('Invalid device category');
        }

        $device->category_id = $category;

        if ($save) {
            $device->save();
        }
    }

    public function store($type, Request $request)
    {
        try {
            $section = DeviceSection::findOrFail($type);

            $this->authorize('create', $section);

            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $device = Device::create([
                'section_id' => $type,
                'created_by' => $request->user()->id,
            ]);

            EncryptedStore::upsert($device, $data);

            $this->setCategory($device, $request);

            $ips = (array) $request->input('ips');
            IpSubnet::assignIps($device->id, $ips, $request->user());

            if ($request->user()->cannot('edit', $section)) {
                // if user has permission to create entries but not edit entries, he will no longer be
                // able to access his device, so we'll assign rwd permission for the newly created device

                $request->user()->permissions()->create([
                    'resource_type' => Permission::RESOURCE_TYPE_DEVICE,
                    'resource_id' => $device->id,
                    'grant_type' => [
                        Permission::GRANT_TYPE_READ,
                        Permission::GRANT_TYPE_EDIT,
                        Permission::GRANT_TYPE_DELETE,
                    ],
                ]);
            }

            return redirect()
                ->route('device.index', $type)
                ->withSuccess('Device has been added');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving device');
        }
    }

    public function edit($id)
    {
        try {
            app('encrypt')->disableExceptions();

            $device = Device::findOrFail($id);
            $deviceSection = $device->section;

            $this->authorize('edit', $device);

            return view('device.edit', compact('deviceSection', 'device'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device');
        }
    }

    public function update($id, Request $request)
    {
        try {
            $device = Device::findOrFail($id);

            $this->authorize('edit', $device);

            $data = $request->input();
            if (isset($data['ips']) && is_array($data['ips'])) {
                $data['ips'] = array_filter($data['ips'], function ($ip) {
                    return filter_var($ip, FILTER_VALIDATE_IP);
                });
            }

            unset($data['_token']);
            unset($data['_method']);
            EncryptedStore::upsert($device, $data);

            $this->setCategory($device, $request);

            $ips = (array) $request->input('ips');
            IpSubnet::assignIps($device->id, $ips, $request->user());

            return redirect()
                ->route('device.index', $device->section_id)
                ->withSuccess('Device has been updated');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error updating device: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $device = Device::findOrFail($id);

            $this->authorize('delete', $device);

            EncryptedStore::destroy($device);

            $device->delete();

            IpSubnet::deviceDestroyed($id);

            return redirect()
                ->back()
                ->withSuccess('Device has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Error deleting device: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            app('encrypt')->disableExceptions();

            $device = Device::findOrFail($id);
            $deviceSection = $device->section;

            $this->authorize('view', $device);

            return view('device.show', compact('device', 'deviceSection'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device');
        }
    }
}
