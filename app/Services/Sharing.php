<?php

namespace App\Services;

use App\Device;
use App\DeviceSection;
use App\EncryptedStore;
use App\Group;
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

            EncryptedStore::updateOrCreate([
                'user_id' => $user->id,
                'resource_type' => Permission::RESOURCE_TYPE_DEVICE,
                'resource_id' => $resource->id,
            ], [
                'data' => app('encrypt')->encryptForUser($data, $user),
            ]);
        } elseif ($resource instanceof IpAddress) {
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
     * @param User|Group $grantee
     * @param Device|DeviceSection|IpAddress|IpCategory $resource
     * @param array $grantType
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
        } elseif ($resource instanceof IpAddress) {
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
}
