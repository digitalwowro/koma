<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceSection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'icon', 'sort', 'fields'];

    /**
     * Relationship with DeviceSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function devices()
    {
        return $this->hasMany('App\Device', 'section_id');
    }

    /**
     * Auto encode the fields field
     *
     * @param string $value
     */
    public function setFieldsAttribute($value)
    {
        $this->attributes['fields'] = is_null($value)
            ? null
            : json_encode($value);
    }

    /**
     * Decode the fields field
     *
     * @param string $value
     * @return array
     */
    public function getFieldsAttribute($value)
    {
        if (is_null($value))
        {
            return null;
        }

        $return = @json_decode($value, true);

        if ( ! is_array($return)) return [];

        return $return;
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
