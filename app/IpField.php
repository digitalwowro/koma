<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IpField extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'bindings'];

    /**
     * Auto encode the data field
     *
     * @param string $value
     */
    public function setBindingsAttribute($value)
    {
        $this->attributes['bindings'] = json_encode($value);
    }

    /**
     * Decode the data field
     *
     * @param string $value
     * @return array
     */
    public function getBindingsAttribute($value)
    {
        $return = @json_decode($value, true);

        return is_array($return) ? $return : [];
    }

}
