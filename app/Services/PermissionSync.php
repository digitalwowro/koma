<?php

namespace App\Services;

use App\Device;
use App\EncryptedStore;
use App\IpAddress;
use App\Permission;
use App\User;
use Exception;

class PermissionSync
{
    protected function sanitize(array $permissions) : array
    {
        $results = [];

        foreach ($permissions as $permission) {
            if (!isset($permission['level'], $permission['id'], $permission['type']) || !is_array($permission['level'])) {
                continue;
            }

            $allowed = [
                Permission::GRANT_TYPE_READ,
                Permission::GRANT_TYPE_EDIT,
                Permission::GRANT_TYPE_DELETE,
            ];

            if (in_array($permission['type'], [Permission::RESOURCE_TYPE_DEVICE_SECTION, Permission::RESOURCE_TYPE_IP_CATEGORY])) {
                $allowed[] = Permission::GRANT_TYPE_CREATE;
            }

            $level = array_intersect($allowed, $permission['level']);

            if (count($level)) {
                $results[] = [
                    'resource_type' => $permission['type'],
                    'resource_id' => $permission['id'] ? $permission['id'] : null,
                    'grant_type' => $level,
                ];
            }
        }

        return $results;
    }

    protected function queryFor(array $permissions) : array
    {
        $queries = [];

        foreach ($permissions as $permission) {
            $resourceType = intval($permission['resource_type']);
            $resourceId = intval($permission['resource_id']);

            if ($resourceType === Permission::RESOURCE_TYPE_DEVICE_SECTION) {
                $queries[] = Device::where('section_id', $resourceId);
            } elseif ($resourceType === Permission::RESOURCE_TYPE_DEVICE) {
                $queries[] = Device::where('id', $resourceId);
            } elseif ($resourceType === Permission::RESOURCE_TYPE_IP_CATEGORY) {
                $queries[] = IpAddress::where('category_id', $resourceId);
            } elseif ($resourceType === Permission::RESOURCE_TYPE_IP_SUBNET) {
                $ip = IpAddress::find($resourceId);

                $queries[] = IpAddress::where([
                    'subnet' => $ip->subnet,
                    'category_id' => $ip->category_id,
                ]);
            }
        }

        return $queries;
    }

    public function sync(User $user, array $permissions)
    {
        $permissions = $this->sanitize($permissions);

        $user->permissions()->delete();
        $user->permissions()->createMany($permissions);

        Permission::flushCache();

        $queries = $this->queryFor($permissions);
        EncryptedStore::ensurePermissions($user, $queries);
    }
}
