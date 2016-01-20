<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ip', 'subnet', 'category_id', 'data'];

    /**
     * Relationship with IpCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\IpCategory', 'category_id');
    }

    /**
     * Relationship with Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device()
    {
        return $this->belongsTo('App\Device');
    }

    /**
     * Auto encode the data field
     *
     * @param string $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = encrypt(json_encode($value));
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
            $return = @json_decode(decrypt($value), true);

            if ( ! is_array($return)) return [];

            return $return;
        }
        catch (\Exception $e)
        {
            return [];
        }
    }

    /**
     * Create a new subnet
     *
     * @param string $subnet
     * @param int $categoryId
     */
    public static function createSubnet($subnet, $categoryId)
    {
        list($ip, $mask) = explode('/', $subnet);

        $ipEnc = ip2long($ip);

        // convert last (32-$mask) bits to zeroes
        $currentIp = $ipEnc | pow(2, (32 - $mask)) - pow(2, (32 - $mask));

        for ($pos = 0; $pos < pow(2, (32 - $mask)); ++$pos)
        {
            self::create([
                'ip'          => long2ip($currentIp + $pos),
                'subnet'      => $subnet,
                'category_id' => $categoryId,
            ]);
        }
    }

    /**
     * Get subnets for given category ID
     *
     * @param $categoryId
     * @return \Illuminate\Support\Collection
     */
    public static function getSubnetsFor($categoryId)
    {
        return self::selectRaw('count(*) as count, subnet, category_id')
            ->where('category_id', $categoryId)
            ->groupBy('subnet')
            ->get();
    }

    /**
     * Get number of used IPs in given subnet
     *
     * @param string $subnet
     * @return int
     */
    public static function getFreeForSubnet($subnet)
    {
        return self::where('subnet', $subnet)->whereNull('device_id')->count();
    }

    /**
     * Get all IPs for given subnet
     *
     * @param string $subnet
     * @return \Illuminate\Support\Collection
     */
    public function getIPsForSubnet($subnet)
    {
        return self::where('subnet', $subnet)->orderBy('id')->get();
    }

}
