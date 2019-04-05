<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['section_id', 'data'];

    /**
     * Relationship with DeviceSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo('App\DeviceSection', 'section_id');
    }

    /**
     * Relationship with IpAddress
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ips()
    {
        return $this->hasMany('App\IpAddress');
    }

    /**
     * Auto encode the data field
     *
     * @param string $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = dsEncrypt(json_encode($value));
    }

    /**
     * Decode the data field
     *
     * @param string $value
     * @return array
     */
    public function getDataAttribute($value)
    {
        try
        {
            $return = @json_decode(dsDecrypt($value), true);

            if ( ! is_array($return)) return [];

            return $return;
        }
        catch (\Exception $e)
        {
            return [];
        }
    }

}
