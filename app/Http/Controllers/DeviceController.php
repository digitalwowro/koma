<?php

namespace App\Http\Controllers;

use App\Device;
use App\DeviceSection;
use App\Exceptions\AlreadyHasPermissionException;
use App\IpAddress;
use App\Permission;
use App\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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

    /**
     * @var \App\IpAddress
     */
    private $ipAddress;

    public function __construct(Device $model, DeviceSection $deviceSection, IpAddress $ipAddress)
    {
        $this->model = $model;
        $this->deviceSection = $deviceSection;
        $this->ipAddress = $ipAddress;
    }

    public function index($type, $category = null)
    {
        try {
            $deviceSection = $this->deviceSection->findOrFail($type);
            $devices = $deviceSection->devices;

            if ($category) {
                if (!isset($deviceSection->categories[$category])) {
                    return redirect()->route('devices.index', $type);
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

            return view('devices.index', compact('deviceSection', 'devices', 'colspan', 'filters', 'category', 'categoryLabel', 'type'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    public function create($type)
    {
        try {
            $deviceSection = $this->deviceSection->findOrFail($type);

            $this->authorize('create', $deviceSection);

            return view('devices.create', compact('deviceSection'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Could not find device section');
        }
    }

    /**
     * Assign list of IPs to given device
     * Also creates single IPs as needed
     *
     * @param array $ipArray
     * @param \App\Device $device
     * @throws Exception
     */
    private function assignIpsToDevice(array $ipArray, Device $device)
    {
        $idArray = [];

        foreach ($ipArray as $key => $ip) {
            if (is_numeric($ip)) {
                $idArray[] = $ip;
                unset($ipArray[$key]);
            } else {
                if ($existingIp = $this->ipAddress->where('ip', $ipArray)->first()) {
                    $idArray[] = $existingIp->id;
                    unset($ipArray[$key]);
                }
            }
        }

        $ips = $this->ipAddress->whereIn('id', $idArray)->get();

        // preset IPs
        foreach ($ips as $ip) {
            if ($ip->device_id == $device->id) {
                continue;
            }

            if ($ip->assigned()) {
                throw new Exception("IP {$ip->ip} is already assigned!");
            }

            $ip->device_id = $device->id;
            $ip->save();
        }

        // custom IPs
        foreach ($ipArray as $customIp) {
            if (!filter_var($customIp, FILTER_VALIDATE_IP)) {
                throw new Exception("IP {$customIp} is not a valid IP address");
            }

            $this->ipAddress->forceCreate([
                'ip' => $customIp,
                'device_id' => $device->id,
            ]);
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
            $section = $this->deviceSection->findOrFail($type);

            $this->authorize('create', $section);

            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $device = $this->model->create([
                'section_id' => $type,
                'data' => $data,
            ]);

            $this->setCategory($device, $request);

            if (isset($data['ips']) && is_array($data['ips'])) {
                $this->assignIpsToDevice($data['ips'], $device);
            }

            if ($request->user()->cannot('edit', $section)) {
                // if user has permission to create entries but not edit entries, he will no longer be
                // able to access his device, so we'll assign rwd permission for the newly created device

                $request->user()->permissions()->create([
                    'resource_type' => Permission::RESOURCE_TYPE_DEVICES_DEVICE,
                    'resource_id' => $device->id,
                    'grant_type' => Permission::GRANT_TYPE_FULL,
                ]);
            }

            return redirect()
                ->route('devices.index', $type)
                ->withSuccess('Device has been added');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withError('Error saving device');
        }
    }

    public function edit($type, $id)
    {
        try {
            $deviceSection = $this->deviceSection->findOrFail($type);
            $device = $this->model->findOrFail($id);

            $this->authorize('edit', $device);

            return view('devices.edit', compact('deviceSection', 'device'));
        } catch (Exception $e) {
            return redirect()
                ->route('devices.index', $type)
                ->withError('Could not find device');
        }
    }

    public function update($id, Request $request)
    {
        try {
            $data = $request->input();

            unset($data['_token']);
            unset($data['_method']);

            $device = $this->model->findOrFail($id);

            $this->authorize('edit', $device);

            $device->data = $data;

            $this->setCategory($device, $request);

            $device->ips()->update(['device_id' => null]);
            $this->ipAddress->whereNull('device_id')->whereNull('subnet')->delete();

            if (isset($data['ips']) && is_array($data['ips'])) {
                $this->assignIpsToDevice($data['ips'], $device);
            }

            return redirect()
                ->route('devices.index', $device->section_id)
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
            $device = $this->model->findOrFail($id);

            $this->authorize('delete', $device);

            $device->delete();

            return redirect()
                ->back()
                ->withSuccess('Device has been deleted');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withError('Error deleting device: ' . $e->getMessage());
        }
    }

    public function show($type, $id)
    {
        try {
            $deviceSection = $this->deviceSection->findOrFail($type);
            $device = $this->model->findOrFail($id);

            $this->authorize('view', $device);

            return view('devices.show', compact('device', 'deviceSection'));
        } catch (Exception $e) {
            return redirect()
                ->route('devices.index', $type)
                ->withError('Could not find device');
        }
    }

    /**
     * Check if user already has permissions
     *
     * @param int $grantType
     * @param int $user
     * @param int $id
     * @param int $type
     * @throws AlreadyHasPermissionException
     */
    protected function alreadyHasPermissions($grantType, $user, $id, $type)
    {
        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])) {
            throw new AlreadyHasPermissionException;
        }

        if ($grantType === Permission::GRANT_TYPE_FULL) { // rwd
            $greaterPermissions = Permission::getAcl('delete');
        } elseif ($grantType === Permission::GRANT_TYPE_WRITE) { // rw
            $greaterPermissions = Permission::getAcl('edit');
        } elseif ($grantType === Permission::GRANT_TYPE_READ) { // r
            $greaterPermissions = Permission::getAcl('view');
        } else {
            throw new Exception('Invalid permission');
        }

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_DEVICE)
            ->where('resource_id', $id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_SECTION)
            ->where('resource_id', $type)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }
    }

    protected function deleteInferiorPermissions($grantType, $user, $id)
    {
        if ($grantType === Permission::GRANT_TYPE_FULL) { // rwd
            $toDelete = [Permission::GRANT_TYPE_WRITE, Permission::GRANT_TYPE_READ];
        } elseif ($grantType === Permission::GRANT_TYPE_WRITE) { // rw
            $toDelete = [Permission::GRANT_TYPE_READ];
        }

        if (!empty($toDelete)) {
            $user->permissions()->whereIn('grant_type', $toDelete)
                ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_DEVICE)
                ->where('resource_id', $id)
                ->delete();
        }
    }

    /**
     * @param int     $type
     * @param int     $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function share($type, $id, Request $request)
    {
        try {
            $this->authorize('superadmin');

            $this->deviceSection->findOrFail($type);
            $this->model->findOrFail($id);

            $user = User::findOrFail($request->input('user_id'));
            $grantType = intval($request->input('grant_type'));

            $this->alreadyHasPermissions($grantType, $user, $id, $type);

            $user->permissions()->create([
                'resource_type' => Permission::RESOURCE_TYPE_DEVICES_DEVICE,
                'resource_id' => $id,
                'grant_type' => $grantType,
            ]);

            $this->deleteInferiorPermissions($grantType, $user, $id);

            Permission::flushCache();

            return response()->json([
                'success' => true,
            ]);
        } catch (AlreadyHasPermissionException $e) {
            return response()->json([
                'error' => 'User already has access to this device',
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Could not share device',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

}
