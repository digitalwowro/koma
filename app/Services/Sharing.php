<?php

namespace App\Services;

use App\Device;
use App\DeviceSection;
use App\EncryptedStore;
use App\Exceptions\AlreadyHasPermissionException;
use App\IpAddress;
use App\IpCategory;
use App\Permission;
use App\User;
use Exception;

class Sharing
{
    /**
     * @param User $user
     * @param mixed $resource
     */
    protected function ensureEncryptedShares(User $user, $resource)
    {
        if ($resource instanceof Device) {
            $data = json_encode($resource->data);

            EncryptedStore::create([
                'user_id' => $user->id,
                'resource_type' => Permission::RESOURCE_TYPE_DEVICE,
                'resource_id' => $resource->id,
                'data' => app('encrypt')->encryptForUser($data, $user),
            ]);
        } elseif ($resource instanceof IpAddress) {
            $data = json_encode($resource->data);

            EncryptedStore::create([
                'user_id' => $user->id,
                'resource_type' => Permission::RESOURCE_TYPE_IP_SUBNET,
                'resource_id' => $resource->id,
                'data' => app('encrypt')->encryptForUser($data, $user),
            ]);
        }
    }

    /**
     * @param User $user
     * @param Device|DeviceSection|IpAddress|IpCategory $resource
     * @param array $grantType
     * @throws AlreadyHasPermissionException
     */
    public function share(User $user, $resource, array $grantType = []) {
        if ($resource instanceof Device) {
            $resourceType = Permission::RESOURCE_TYPE_DEVICE;
        } elseif ($resource instanceof DeviceSection) {
            $resourceType = Permission::RESOURCE_TYPE_DEVICE_SECTION;
        } elseif ($resource instanceof IpAddress) {
            $resourceType = Permission::RESOURCE_TYPE_IP_SUBNET;
        } elseif ($resource instanceof IpCategory) {
            $resourceType = Permission::RESOURCE_TYPE_IP_CATEGORY;
        } else {
            throw new Exception('Invalid resource');
        }

        if (!count($grantType)) {
            $user->permissions()->where([
                'resource_type' => $resourceType,
                'resource_id' => $resource->id,
            ])->delete();
        } else { // upsert
            Permission::updateOrCreate([
                'resource_type' => $resourceType,
                'resource_id' => $resource->id,
                'user_id' => $user->id,
            ], [
                'grant_type' => $grantType,
            ]);
        }

        $this->ensureEncryptedShares($user, $resource);

        Permission::flushCache();
    }
}
