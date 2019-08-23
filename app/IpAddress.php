<?php

namespace App;

use App\Exceptions\SubnetTooLargeException;
use App\Scopes\IpAddressTenant;
use Exception;
use Illuminate\Database\Eloquent\Model;

class IpAddress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ip', 'subnet', 'category_id', 'data', 'created_by'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IpAddressTenant);
    }

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
     * Decode the data field
     *
     * @return array
     */
    public function getDataAttribute()
    {
        if (is_null($this->decrypted)) {
            $this->decrypted = EncryptedStore::pull($this);
        }

        return $this->decrypted;
    }



    /**
     * Returns all permissions referring to this resource
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sharedWith()
    {
        $ip = $this->firstInSubnet();

        return Permission::with('user')
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_SUBNET)
            ->where('resource_id', $ip->id)
            ->get();
    }

    /**
     * Create a new subnet
     *
     * @param string $subnet
     * @param int    $categoryId
     * @param int    $createdBy
     * @return IpAddress
     * @throws SubnetTooLargeException
     */
    public static function createSubnet($subnet, $categoryId, int $createdBy = null)
    {
        list($ip, $mask) = explode('/', $subnet);

        $ipEnc = ip2long($ip);
        $first = null;

        // convert last (32-$mask) bits to zeroes
        $currentIp = $ipEnc | pow(2, (32 - $mask)) - pow(2, (32 - $mask));
        $ipsCount = pow(2, (32 - $mask));

        if ($ipsCount > 65536) {
            throw new SubnetTooLargeException;
        }

        for ($pos = 0; $pos < $ipsCount; ++$pos) {
            $created = self::create([
                'ip' => long2ip($currentIp + $pos),
                'subnet' => $subnet,
                'category_id' => $categoryId,
                'created_by' => $createdBy,
            ]);

            if (is_null($first)) {
                $first = $created;
            }
        }

        return $first;
    }

    /**
     * @param $query
     */
    public function scopeHasSubnet($query)
    {
        $query->whereNotNull('subnet');
    }

    /**
     * Returns whether the IP is a custom one, or if it belongs to a
     * predefined IP class
     *
     * @return bool
     */
    public function isCustom()
    {
        return is_null($this->subnet) || is_null($this->category_id);
    }

    /**
     * Get subnets for given category ID
     *
     * @param int|null $categoryId
     * @return \Illuminate\Support\Collection
     */
    public static function getSubnetsFor($categoryId = null)
    {
        $query = self::selectRaw('min(id) as id, count(*) as count, subnet, category_id');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query
            ->hasSubnet()
            ->groupBy('subnet')
            ->orderBy('category_id')
            ->orderBy('subnet')
            ->get();
    }

    public static function allSubnets()
    {
        return self::selectRaw('min(id) as id, subnet, category_id')
            ->hasSubnet()
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
        return self::with('device')
            ->where('subnet', $subnet)
            ->orderBy('id')
            ->get();
    }

    /**
     * Return first IP in subnet
     *
     * @return $this
     */
    public function firstInSubnet()
    {
        try {
            if (!$this->subnet) {
                return $this; // custom IP
            }

            $subnetParts = explode('/', $this->subnet);

            if (array_shift($subnetParts) === $this->ip) {
                return $this;
            }

            return IpAddress::where([
                'category_id' => $this->category_id,
                'subnet' => $this->subnet,
            ])->orderBy('id')->first();
        } catch (Exception $e) {
            // return self
        }

        return $this;
    }

    /**
     * Returns whether the current IP address is assigned
     *
     * @return bool
     */
    public function assigned()
    {
        return $this->device_id ? true : false;
    }

    /**
     * @param \App\IpField $field
     * @return string
     */
    public function getFieldValue(IpField $field)
    {
        if ($this->device_id && $this->device) {
            $deviceType = $this->device->section_id;

            if (isset($field->bindings[$deviceType])) {
                $bindTo = $field->bindings[$deviceType];

                foreach ($this->device->section->fields as $deviceField) {
                    if ($deviceField->getInputName() == $bindTo) {
                        if (method_exists($deviceField, 'customDeviceListContent')) {
                            return $deviceField->customDeviceListContent($this->device);
                        } elseif (isset($this->device->data[$deviceField->getInputName()])) {
                            if (is_array($this->device->data[$deviceField->getInputName()])) {
                                return urlify(implode(', ', $this->device->data[$deviceField->getInputName()]));
                            } else {
                                return urlify($this->device->data[$deviceField->getInputName()]);
                            }
                        } else {
                            return '-';
                        }
                    }
                }
            }
        }

        return '-';
    }

    /**
     * Returns whether given user is owner of current resource
     *
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        $first = $this->firstInSubnet();

        if (!$first->category) {
            return false;
        }

        return $first->category->owner_id === $user->id;
    }

}
