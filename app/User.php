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
        $this->attributes['password'] = Hash::make($value);
        $this->attributes['encryption_key'] = encrypt(Session::get('encryption_key'), $value);
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
}
