<?php

namespace App;

use App\Exceptions\InvalidSubnetException;
use App\Presenters\IpSubnetPresenter;
use App\Scopes\IpSubnetTenant;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Laracasts\Presenter\PresentableTrait;

class IpSubnet extends Model
{
    use PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = IpSubnetPresenter::class;

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

        static::addGlobalScope(new IpSubnetTenant);
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
        return $this->belongsTo('App\Item');
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

    public function getSubnetAttribute()
    {
        $data = $this->data;

        if (!isset($data['subnet'])) {
            return false;
        }

        list($ip, $mask) = explode('/', $data['subnet']);

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        if (!$mask || !is_numeric($mask)) {
            return false;
        }

        $mask = intval($mask);

        if ($mask < 1 || $mask > 32) {
            return false;
        }

        return $data['subnet'];
    }

    /**
     * Returns all permissions referring to this resource
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sharedWith()
    {
        return Permission::with('user')
            ->where('resource_type', Permission::RESOURCE_TYPE_IP_SUBNET)
            ->where('resource_id', $this->id)
            ->get();
    }

    /**
     * Create a new subnet
     *
     * @param array $data
     * @param int   $categoryId
     * @param int   $createdBy
     * @return IpSubnet
     * @throws InvalidSubnetException
     */
    public static function createSubnet(array $data, $categoryId, int $createdBy = null)
    {
        $subnet = $data['subnet'] ?? '';
        list($ip, $mask) = explode('/', $subnet);

        if (!filter_var($ip, FILTER_VALIDATE_IP) || !is_numeric($mask) || intval($mask) < 1 || intval($mask) > 32) {
            throw new InvalidSubnetException;
        }

        $subnet = self::create([
            'category_id' => $categoryId,
            'created_by' => $createdBy,
        ]);

        EncryptedStore::upsert($subnet, [
            'name' => $data['name'] ?? null,
            'subnet' => $data['subnet'],
        ]);

        return $subnet;
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
     * Returns whether given user is owner of current resource
     *
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        if (!$this->category) {
            return false;
        }

        return $this->category->owner_id === $user->id;
    }

    public function paginatedIps($page, $perPage) : \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $results = [];
        $deviceIds = [];
        $subnet = $this->subnet;
        $assignData = (array) $this->assigned;

        if (!$subnet) {
            return new LengthAwarePaginator([], 0, $perPage, 1);
        }

        list($ip, $mask) = explode('/', $subnet);

        $ipsCount = pow(2, (32 - $mask));

        $start = ip2long($ip);
        $start = $start | pow(2, (32 - $mask)) - pow(2, (32 - $mask)); // convert last (32-$mask) bits to zeroes

        $end = $start + $ipsCount - 1;

        $pageStart = $start + ($page - 1) * $perPage;
        $pageEnd = $pageStart + $perPage - 1;

        if ($pageStart > $end) {
            return new LengthAwarePaginator([], 0, $perPage, 1);
        }

        if ($pageEnd > $end) {
            $pageEnd = $end;
        }

        for ($pos = $pageStart; $pos <= $pageEnd; ++$pos) {
            $ip = long2ip($pos);

            $data = isset($assignData[$ip])
                ? $assignData[$ip]
                : [
                    'reserved' => false,
                    'device_id' => null,
                ];

            if (!empty($data['device_id'])) {
                $deviceIds[] = $data['device_id'];
            }

            $results[] = array_merge($data, compact('ip'));
        }

        if (count($deviceIds)) {
            Item::whereIn('id', $deviceIds)->each(function($device) use (&$results) {
                foreach ($results as $key => $result) {
                    if (!empty($result['device_id']) && $result['device_id'] === $device->id) {
                        $results[$key]['device'] = $device;
                    }
                }
            });
        }

        return new LengthAwarePaginator($results, $ipsCount, $perPage, $page, [
            'path' => '',
        ]);
    }

    public function freeIps() : array
    {
        $results = [];
        $subnet = $this->subnet;
        $assignData = (array) $this->assigned;

        if (!$subnet) {
            return [];
        }

        list($ip, $mask) = explode('/', $subnet);

        $start = ip2long($ip);
        $start = $start | pow(2, (32 - $mask)) - pow(2, (32 - $mask)); // convert last (32-$mask) bits to zeroes

        $ipsCount = pow(2, (32 - $mask));

        for ($pos = 0; $pos < $ipsCount; ++$pos) {
            $ip = long2ip($start + $pos);

            if (!isset($assignData[$ip])) {
                $results[] = $ip;
            }
        }

        return $results;
    }

    public function ipBelongsToSubnet($input)
    {
        $subnet = $this->subnet;
        $input = ip2long($input);

        if (!$subnet) {
            return false;
        }

        list($ip, $mask) = explode('/', $subnet);

        $start = ip2long($ip);
        $start = $start | pow(2, (32 - $mask)) - pow(2, (32 - $mask)); // convert last (32-$mask) bits to zeroes

        $ipsCount = pow(2, (32 - $mask));

        $end = $start + $ipsCount - 1;

        return $input >= $start && $input <= $end;
    }

    /**
     * @param int   $deviceId
     * @param array $inputIps
     * @param User  $user
     */
    public static function assignIps(int $deviceId, array $inputIps, User $user)
    {
        static::each(function ($subnet) use (&$inputIps, $deviceId, $user) {
            if ($user->cannot('update', $subnet)) {
                return;
            }

            $assigned = [];
            $dirty = false;

            if (!empty($subnet->data) && isset($subnet->data['assigned']) && is_array($subnet->data['assigned'])) {
                $assigned = $subnet->data['assigned'];
            }

            foreach ($assigned as $key => $item) {
                if (isset($item['device_id']) && $item['device_id'] === $deviceId) {
                    unset($assigned[$key]);
                    $dirty = true;
                }
            }

            foreach ($inputIps as $inputIp) {
                $exploded = explode('|', $inputIp);

                if (count($exploded) !== 2) {
                    continue;
                }

                list($subnetId, $ipAddr) = $exploded;

                if ((int) $subnetId !== $subnet->id) {
                    continue;
                }

                if ($subnet->ipBelongsToSubnet($ipAddr)) {
                    $assigned[$ipAddr] = [
                        'device_id' => $deviceId,
                    ];

                    $dirty = true;
                }
            }

            if ($dirty) {
                $data = $subnet->data;
                $data['assigned'] = $assigned;

                EncryptedStore::upsert($subnet, $data);
            }
        });
    }

    public function getReserved() : array
    {
        $allIps = [];
        $allReserved = [];
        $subnet = $this->subnet;
        $assignData = (array) $this->assigned;

        if (!$subnet) {
            return [[], []];
        }

        list($ip, $mask) = explode('/', $subnet);

        $ipsCount = pow(2, (32 - $mask));

        $start = ip2long($ip);
        $start = $start | pow(2, (32 - $mask)) - pow(2, (32 - $mask)); // convert last (32-$mask) bits to zeroes

        for ($pos = 0; $pos < $ipsCount; ++$pos) {
            $ip = long2ip($start + $pos);

            if (!empty($assignData[$ip]['device_id'])) {
                // exclude allocated IPs, so they can't be marked as resesrved
                continue;
            }

            $allIps[$ip] = $ip;

            if (!empty($assignData[$ip]['reserved']) && $assignData[$ip]['reserved'] === true) {
                $allReserved[$ip] = $ip;
            }
        }

        return [$allIps, $allReserved];
    }

    public static function deviceDestroyed($deviceId)
    {
        static::each(function($subnet) use ($deviceId) {
            try {
                $data = $subnet->data;
                $assigned = $data['assigned'] ?? [];
                $dirty = false;

                foreach ($assigned as $key => $value) {
                    if (isset($value['device_id']) && $value['device_id'] === (int) $deviceId) {
                        unset($assigned[$key]);
                        $dirty = true;
                    }
                }

                if ($dirty) {
                    $data['assigned'] = $assigned;

                    EncryptedStore::upsert($subnet, $data);
                }
            } catch (Exception $e) {
                // skip subnet
            }
        });
    }
}
