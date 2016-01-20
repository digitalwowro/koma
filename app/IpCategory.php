<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IpCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'sort'];

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
}
