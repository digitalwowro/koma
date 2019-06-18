<?php

namespace App;

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
    const ROLE_SYSADMIN   = 2;

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
    protected $hidden = ['password', 'remember_token', 'public_key', 'salt'];

    /**
     * Auto encode the data field
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
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
        return true; // @todo still needed?

        /*if (!isset($this->profile['device_sections']) || !is_array($this->profile['device_sections'])) {
            return true;
        }

        return in_array($sectionId, $this->profile['device_sections']);*/
    }

    public function ipCategoryVisible($categoryId)
    {
        return true; // @todo still needed?

        /*if (!isset($this->profile['ip_categories']) || !is_array($this->profile['ip_categories'])) {
            return true;
        }

        return in_array($categoryId, $this->profile['ip_categories']);*/
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
     * Returns whether current user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === $this::ROLE_ADMIN;
    }

    public static function pagedForAdmin()
    {
        return self::orderBy('id')->paginate(30);
    }
}
