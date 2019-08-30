<?php

namespace App\Services;

use App\Device;
use App\DeviceSection;
use App\EncryptedStore;
use App\Group;
use App\IpSubnet;
use App\IpCategory;
use App\Permission;
use App\User;
use Exception;

class Sharing
{
    /**
     * @param User $user
     * @param mixed $resource
     * @return int
     */
    protected function ensureEncryptedShares(User $user, $resource)
    {
        if ($resource instanceof Device) {
            $data = json_encode($resource->data);

            EncryptedStore::updateOrCreate([
                'user_id' => $user->id,
                'resource_type' => Permission::RESOURCE_TYPE_DEVICE,
                'resource_id' => $resource->id,
            ], [
                'data' => app('encrypt')->encryptForUser($data, $user),
            ]);
        } elseif ($resource instanceof IpSubnet) {
            $data = json_encode($resource->data);

            EncryptedStore::updateOrCreate([
                'user_id' => $user->id,
                'resource_type' => Permission::RESOURCE_TYPE_IP_SUBNET,
                'resource_id' => $resource->id,
            ], [
                'data' => app('encrypt')->encryptForUser($data, $user),
            ]);
        }
    }

    /**
     * @param User|Group                               $grantee
     * @param Device|DeviceSection|IpSubnet|IpCategory $resource
     * @param array                                    $grantType
     * @throws Exception
     */
    public function share($grantee, $resource, array $grantType = []) {
        if (!$grantee instanceof User && !$grantee instanceof Group) {
            throw new Exception('Invalid grantee');
        }

        if ($resource instanceof Device) {
            $resourceType = Permission::RESOURCE_TYPE_DEVICE;
        } elseif ($resource instanceof DeviceSection) {
            $resourceType = Permission::RESOURCE_TYPE_DEVICE_SECTION;
        } elseif ($resource instanceof IpSubnet) {
            $resourceType = Permission::RESOURCE_TYPE_IP_SUBNET;
        } elseif ($resource instanceof IpCategory) {
            $resourceType = Permission::RESOURCE_TYPE_IP_CATEGORY;
        } else {
            throw new Exception('Invalid resource');
        }

        if (!count($grantType)) {
            $grantee->permissions()->where([
                'resource_type' => $resourceType,
                'resource_id' => $resource->id,
            ])->delete();
        } else { // upsert
            $fields = [
                'resource_type' => $resourceType,
                'resource_id' => $resource->id,
            ];

            if ($grantee instanceof User) {
                $fields['user_id'] = $grantee->id;
            } elseif ($grantee instanceof Group) {
                $fields['group_id'] = $grantee->id;
            }

            Permission::updateOrCreate($fields, [
                'grant_type' => $grantType,
            ]);
        }

        if ($grantee instanceof User) {
            $this->ensureEncryptedShares($grantee, $resource);
        } elseif ($grantee instanceof Group) {
            foreach ($grantee->users as $user) {
                $this->ensureEncryptedShares($user, $resource);
            }
        }

        Permission::flushCache();
    }

    public function refreshUserPermissions(User $user)
    {
        $permissions = Permission::allForUser($user);

        foreach ($permissions as $permission) {
            $resource = null;

            switch ($permission['resource_type']) {
                case Permission::RESOURCE_TYPE_DEVICE_SECTION:
                    $resource = DeviceSection::find($permission['resource_id']);
                    break;
                case Permission::RESOURCE_TYPE_DEVICE:
                    $resource = Device::find($permission['resource_id']);
                    break;
                case Permission::RESOURCE_TYPE_IP_CATEGORY:
                    $resource = IpCategory::find($permission['resource_id']);
                    break;
                case Permission::RESOURCE_TYPE_IP_SUBNET:
                    $resource = IpSubnet::find($permission['resource_id']);
                    break;
            }

            if ($resource) {
                $this->ensureEncryptedShares($user, $resource);
            }
        }

        // @todo remove all EncryptedStores not matching Permission::allForUser($user)
    }
}
