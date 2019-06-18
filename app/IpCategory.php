<?php

namespace App;

use App\Scopes\IpCategoryTenant;
use Illuminate\Database\Eloquent\Model;

class IpCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'sort', 'owner_id'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IpCategoryTenant);
    }

    /**
     * Relationship with IpAddress
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ips()
    {
        return $this->hasMany('App\IpAddress', 'category_id');
    }

    /**
     * Relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    /**
     * Returns all permissions referring to this resource
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sharedWith()
    {
        return Permission::with('user')
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_CATEGORY)
            ->where('resource_id', $this->id)
            ->get();
    }

    /**
     * Get all device sections paged for admin
     *
     * @return mixed
     */
    public static function pagedForAdmin()
    {
        return self::orderBy('sort')->orderBy('title')->paginate(30);
    }

    /**
     * Get all device sections ordered
     *
     * @return mixed
     */
    public static function getAll()
    {
        return self::orderBy('sort')->orderBy('title')->get();
    }

    /**
     * Returns whether given user is owner of current resource
     *
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->owner_id === $user->id;
    }
}
