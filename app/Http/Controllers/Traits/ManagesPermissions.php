<?php

namespace App\Http\Controllers\Traits;

use App\Exceptions\AlreadyHasPermissionException;
use App\Permission;
use App\User;
use Exception;

trait ManagesPermissions
{
    /**
     * @param User $user
     * @throws AlreadyHasPermissionException
     */
    protected function checkAdmin(User $user)
    {
        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])) {
            throw new AlreadyHasPermissionException;
        }
    }

    /**
     * Get greater permissions than given one
     *
     * @param int $grantType
     * @return array
     * @throws Exception
     */
    protected function greaterPermissions($grantType)
    {
        if ($grantType === Permission::GRANT_TYPE_FULL) { // rwd
            return Permission::getAcl('delete');
        } elseif ($grantType === Permission::GRANT_TYPE_WRITE) { // rw
            return Permission::getAcl('edit');
        } elseif ($grantType === Permission::GRANT_TYPE_READ) { // r
            return Permission::getAcl('view');
        } else {
            throw new Exception('Invalid permission');
        }
    }

    /**
     * Check if user already has permissions
     *
     * @param int $grantType
     * @param User $user
     * @param int $id
     * @param int $type
     * @throws AlreadyHasPermissionException
     * @throws Exception
     */
    protected function validateDevicePermission($grantType, User $user, $id, $type)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

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

    /**
     * @param int $grantType
     * @param User $user
     * @param int $id
     * @throws AlreadyHasPermissionException
     * @throws Exception
     */
    protected function validateDeviceSectionPermission($grantType, User $user, $id)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_SECTION)
            ->where('resource_id', $id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }
    }

    /**
     * @param int $grantType
     * @param User $user
     * @param int $id
     * @param int $type
     * @throws AlreadyHasPermissionException
     * @throws Exception
     */
    protected function validateIpPermission($grantType, User $user, $id, $type)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_SUBNET)
            ->where('resource_id', $id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_CATEGORY)
            ->where('resource_id', $type)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }
    }

    /**
     * @param int $grantType
     * @param User $user
     * @param int $id
     * @throws AlreadyHasPermissionException
     * @throws Exception
     */
    protected function validateIpCategoryPermission($grantType, User $user, $id)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_CATEGORY)
            ->where('resource_id', $id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }
    }

    /**
     * Delete redundant permissions
     *
     * @param Permission $permission
     */
    protected function deleteRedundantPermissions(Permission $permission)
    {
        $grantType = $permission->grant_type;
        $user = $permission->user;

        if ($grantType === Permission::GRANT_TYPE_FULL) { // rwd
            $toDelete = [Permission::GRANT_TYPE_WRITE, Permission::GRANT_TYPE_READ];
        } elseif ($grantType === Permission::GRANT_TYPE_WRITE) { // rw
            $toDelete = [Permission::GRANT_TYPE_READ];
        }

        if (!empty($toDelete)) {
            $user->permissions()->whereIn('grant_type', $toDelete)
                ->where('resource_type', $permission->resource_type)
                ->where('resource_id', $permission->resource_id)
                ->delete();
        }
    }
}
