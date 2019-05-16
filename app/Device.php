<?php

namespace App;

use App\Presenters\DevicePresenter;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class Device extends Model
{
    use PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = DevicePresenter::class;

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
        try {
            $return = @json_decode(dsDecrypt($value), true);

            return is_array($return) ? $return : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function sharedWith()
    {
        return Permission::with('user')
            ->orWhere(function($query) {
                $query
                    ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_DEVICE)
                    ->where('resource_id', $this->id);
            })
            ->orWhere(function($query) {
                $query
                    ->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_SECTION)
                    ->where('resource_id', $this->section_id);
            })
            ->orWhere(function($query) {
                $query->where('resource_type', Permission::RESOURCE_TYPE_DEVICES_FULL);
            })
            ->get();
    }
}
