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
    protected $fillable = ['name', 'email', 'password', 'role', 'profile'];

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

    public function deviceSectionVisible($sectionId)
    {
        if (!isset($this->profile['device_sections']) || !is_array($this->profile['device_sections'])) {
            return true;
        }

        return in_array($sectionId, $this->profile['device_sections']);
    }

    public function ipCategoryVisible($categoryId)
    {
        if (!isset($this->profile['ip_categories']) || !is_array($this->profile['ip_categories'])) {
            return true;
        }

        return in_array($categoryId, $this->profile['ip_categories']);
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
        $toCreate = [];

        foreach ($permissions as $permission) {
            if (!isset($permission['level']) || !isset($permission['id']) && !isset($permission['level']) && !isset($permission['type'])) {
                continue;
            }

            $allowed = in_array($permission['type'], [Permission::RESOURCE_TYPE_DEVICES_SECTION, Permission::RESOURCE_TYPE_IP_CATEGORY])
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

            if (in_array($permission['level'], $allowed)) {
                $toCreate[] = [
                    'resource_type' => $permission['type'],
                    'resource_id' => $permission['id'] ? $permission['id'] : null,
                    'grant_type' => $permission['level'],
                ];
            }
        }

        $this->permissions()->delete();
        $this->permissions()->createMany($toCreate);

        Permission::flushCache();
    }
}
