<?php

namespace App\Services;

use App\Device;
use App\DeviceSection;
use App\EncryptedShare;
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
    protected function greaterPermissions(array $grantType)
    {
        // @todo this
        //if ($grantType === Permission::GRANT_TYPE_FULL) { // rwd
        //    return Permission::getAcl('delete');
        //} elseif ($grantType === Permission::GRANT_TYPE_WRITE) { // rw
        //    return Permission::getAcl('edit');
        //} elseif ($grantType === Permission::GRANT_TYPE_READ) { // r
        //    return Permission::getAcl('view');
        //} else {
        //    throw new Exception('Invalid permission');
        //}

        return [];
    }

    /**
     * Check if user already has permissions
     *
     * @param User   $user
     * @param Device $device
     * @param array  $grantType
     * @throws AlreadyHasPermissionException
     */
    protected function validateDevicePermission(User $user, Device $device, array $grantType)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_DEVICE)
            ->where('resource_id', $device->id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_SECTION)
            ->where('resource_id', $device->section_id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }
    }

    /**
     * @param User          $user
     * @param DeviceSection $section
     * @param array         $grantType
     * @throws AlreadyHasPermissionException
     */
    protected function validateDeviceSectionPermission(User $user, DeviceSection $section, array $grantType)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_SECTION)
            ->where('resource_id', $section->id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }
    }

    /**
     * @param User      $user
     * @param IpAddress $ipAddress
     * @param array     $grantType
     * @throws AlreadyHasPermissionException
     */
    protected function validateIpPermission(User $user, IpAddress $ipAddress, array $grantType)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_SUBNET)
            ->where('resource_id', $ipAddress->firstInSubnet()->id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_CATEGORY)
            ->where('resource_id', $ipAddress->category_id)
            ->exists();

        if ($exists) {
            throw new AlreadyHasPermissionException;
        }
    }

    /**
     * @param User       $user
     * @param IpCategory $ipCategory
     * @param array      $grantType
     * @throws AlreadyHasPermissionException
     */
    protected function validateIpCategoryPermission(User $user, IpCategory $ipCategory, array $grantType)
    {
        $this->checkAdmin($user);
        $greaterPermissions = $this->greaterPermissions($grantType);

        $exists = $user->permissions()->whereIn('grant_type', $greaterPermissions)
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_CATEGORY)
            ->where('resource_id', $ipCategory->id)
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
        // @todo this

        //$grantType = $permission->grant_type;
        //$user = $permission->user;
        //
        //if ($grantType === Permission::GRANT_TYPE_FULL) { // rwd
        //    $toDelete = [Permission::GRANT_TYPE_WRITE, Permission::GRANT_TYPE_READ];
        //} elseif ($grantType === Permission::GRANT_TYPE_WRITE) { // rw
        //    $toDelete = [Permission::GRANT_TYPE_READ];
        //}
        //
        //if (!empty($toDelete)) {
        //    $user->permissions()->whereIn('grant_type', $toDelete)
        //        ->where('resource_type', $permission->resource_type)
        //        ->where('resource_id', $permission->resource_id)
        //        ->delete();
        //}
    }

    /**
     * @param User $user
     * @param mixed $resource
     */
    protected function ensureEncryptedShares(User $user, $resource)
    {
        if ($resource instanceof Device) {
            $data = json_encode($resource->data);

            EncryptedShare::create([
                'user_id' => $user->id,
                'resource_type' => Permission::RESOURCE_TYPE_DEVICES_DEVICE,
                'resource_id' => $resource->id,
                'data' => app('encrypt')->encryptForUser($data, $user),
            ]);
        } elseif ($resource instanceof DeviceSection) {

        } elseif ($resource instanceof IpAddress) {

        } elseif ($resource instanceof IpCategory) {

        }
    }

    /**
     * @param User $user
     * @param Device|DeviceSection|IpAddress|IpCategory $resource
     * @param array $grantType
     * @throws AlreadyHasPermissionException
     */
    public function share(User $user, $resource, array $grantType) {
        if ($resource instanceof Device) {
            $this->validateDevicePermission($user, $resource, $grantType);
            $resourceType = Permission::RESOURCE_TYPE_DEVICES_DEVICE;
        } elseif ($resource instanceof DeviceSection) {
            $this->validateDeviceSectionPermission($user, $resource, $grantType);
            $resourceType = Permission::RESOURCE_TYPE_DEVICES_SECTION;
        } elseif ($resource instanceof IpAddress) {
            $this->validateIpPermission($user, $resource, $grantType);
            $resourceType = Permission::RESOURCE_TYPE_IP_SUBNET;
        } elseif ($resource instanceof IpCategory) {
            $this->validateIpCategoryPermission($user, $resource, $grantType);
            $resourceType = Permission::RESOURCE_TYPE_IP_CATEGORY;
        } elseif ($resource === 'full') {
            $resourceType = Permission::RESOURCE_TYPE_DEVICES_FULL;
        } else {
            throw new Exception('Invalid resource');
        }

        $permission = $user->permissions()->create([
            'resource_type' => $resourceType,
            'resource_id' => $resource->id,
            'grant_type' => $grantType,
        ]);

        $this->deleteRedundantPermissions($permission);

        $this->ensureEncryptedShares($user, $resource);

        Permission::flushCache();
    }
}
