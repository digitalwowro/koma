<?php

namespace App;

use Hash, Session;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    const ROLE_ADMIN      = 1;
    const ROLE_SUPERADMIN = 2;
    const ROLE_SYSADMIN   = 3;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'role'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'encryption_key'];

    /**
     * Auto encode the data field
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $encryptionKey = request()->cookie('key');
        $this->attributes['password'] = Hash::make($value);
        $this->attributes['encryption_key'] = dsEncrypt($encryptionKey, $value);
    }

    /**
     * Auto encode the data field
     *
     * @param string $value
     */
    public function setProfileAttribute($value)
    {
        $this->attributes['profile'] = json_encode($value);
    }

    /**
     * Decode the data field
     *
     * @param string $value
     * @return array
     */
    public function getProfileAttribute($value)
    {
        $return = @json_decode($value, true);

        return is_array($return) ? $return : [];
    }

    /**
     * Relationship with Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany('App\Permission');
    }

    /**
     * Returns whether current user is superadmin
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->role == $this::ROLE_SUPERADMIN;
    }

    /**
     * Returns whether current user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return in_array($this->role, [$this::ROLE_SUPERADMIN, $this::ROLE_ADMIN]);
    }

    public static function pagedForAdmin()
    {
        return self::orderBy('id')->paginate(30);
    }

    /**
     * @param array $permissions
     */
    public function syncPermissions(array $permissions)
    {
        $this->permissions()->delete();

        foreach ($permissions as $permission) {
            unset($resourceType);
            unset($resourceId);
            unset($grantType);

            if (isset($permission['type'])) {
                switch ($permission['type']) {
                    case 'global':
                        $resourceType = Permission::RESOURCE_TYPE_DEVICES_FULL;
                        break;
                    case 'section':
                        $resourceType = Permission::RESOURCE_TYPE_DEVICES_SECTION;
                        break;
                    case 'device':
                        $resourceType = Permission::RESOURCE_TYPE_DEVICES_DEVICE;
                        break;
                }
            }

            if (isset($permission['id'])) {
                $resourceId = $permission['id'];
            }

            $allowed = $permission['type'] === 'section'
                ? [
                    Permission::GRANT_TYPE_READ,
                    Permission::GRANT_TYPE_WRITE,
                    Permission::GRANT_TYPE_FULL,
                    Permission::GRANT_TYPE_CREATE,
                    Permission::GRANT_TYPE_READ_CREATE,
                    Permission::GRANT_TYPE_OWNER,
                ] : [
                    Permission::GRANT_TYPE_READ,
                    Permission::GRANT_TYPE_WRITE,
                    Permission::GRANT_TYPE_FULL,
                ];

            if (isset($permission['level']) && in_array($permission['level'], $allowed)) {
                $grantType = $permission['level'];
            }

            if (isset($resourceType, $resourceId, $grantType)) {
                $this->permissions()->create([
                    'resource_type' => $resourceType,
                    'resource_id' => $resourceId ? $resourceId : null,
                    'grant_type' => $grantType,
                ]);
            }
        }

        Permission::flushCache();
    }
}
