<?php

namespace App;

use App\Presenters\PermissionPresenter;
use Illuminate\Database\Eloquent\Model;
use Cache, Exception;
use Laracasts\Presenter\PresentableTrait;

class Permission extends Model
{
    use PresentableTrait;

    const GRANT_TYPE_NONE   = 0;
    const GRANT_TYPE_READ   = 1;
    const GRANT_TYPE_WRITE  = 2;
    const GRANT_TYPE_DELETE = 4;
    const GRANT_TYPE_CREATE = 8;

    const RESOURCE_TYPE_DEVICES_FULL    = 1;
    const RESOURCE_TYPE_DEVICES_SECTION = 2;
    const RESOURCE_TYPE_DEVICES_DEVICE  = 3;
    const RESOURCE_TYPE_IP_CATEGORY     = 4;
    const RESOURCE_TYPE_IP_SUBNET       = 5;

    private static $cachedPermissions;

    /**
     * @var string
     */
    protected $presenter = PermissionPresenter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['resource_type', 'resource_id', 'resource_type', 'grant_type'];

    private static $acl = [
        'view' => self::GRANT_TYPE_READ,
        'edit' => self::GRANT_TYPE_WRITE,
        'delete' => self::GRANT_TYPE_DELETE,
        'create' => self::GRANT_TYPE_CREATE,

        //'manage' => [
        //    self::GRANT_TYPE_OWNER,
        //],
    ];

    public static function getAcl($action) {
        return self::$acl[$action] ?? [];
    }

    /**
     * Relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public $resource;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function boot()
    {
        Permission::created(function() {
            Permission::flushCache();
        });

        Permission::updated(function() {
            Permission::flushCache();
        });

        Permission::deleted(function() {
            Permission::flushCache();
        });
    }

    /**
     * Auto encode the grant type field
     *
     * @param array $value
     */
    public function setGrantTypeAttribute(array $value)
    {
        $this->attributes['grant_type'] = array_sum($value);
    }

    /**
     * Decode the grant type field
     *
     * @param string $value
     * @return array
     */
    public function getGrantTypeAttribute($value) : array
    {
        $results = [];

        $permissions = [
            $this::GRANT_TYPE_CREATE,
            $this::GRANT_TYPE_DELETE,
            $this::GRANT_TYPE_WRITE,
            $this::GRANT_TYPE_READ,
        ];

        foreach ($permissions as $permission) {
            if ($value >= $permission) {
                $value -= $permission;
                $results[] = $permission;
            }
        }

        return $results;
    }

    public static function userIdsHavingPermission($resource)
    {
        $userIds = [];

        $userIds[] = self::where([
            'resource_type' => self::RESOURCE_TYPE_DEVICES_FULL,
        ])->pluck('user_id')->toArray();

        if ($resource instanceof Device || $resource instanceof DeviceSection) {
            $section = $resource instanceof Device
                ? $resource->section
                : $resource;

            if ($section->owner_id) {
                $userIds[] = $section->owner_id;
            }

            $userIds[] = self::where(function($query) use ($section) {
                $query
                    ->where('resource_type', self::RESOURCE_TYPE_DEVICES_DEVICE)
                    ->whereIn('resource_id', function ($query) use ($section) {
                        $query
                            ->select('id')
                            ->where('section_id', $section->id)
                            ->from('devices');
                    });
            })->orWhere(function($query) use ($section) {
                $query->where([
                    'resource_type' => self::RESOURCE_TYPE_DEVICES_SECTION,
                    'resource_id' => $section->id,
                ]);
            })->pluck('user_id')->toArray();
        } elseif ($resource instanceof IpAddress || $resource instanceof IpCategory) {
            $category = $resource instanceof Device
                ? $resource->category
                : $resource;

            if ($category->owner_id) {
                $userIds[] = $category->owner_id;
            }

            $userIds[] = self::where(function($query) use ($category) {
                $subnetIds = IpAddress::getSubnetsFor($category->id)->pluck('id');

                $query
                    ->where('resource_type', self::RESOURCE_TYPE_IP_SUBNET)
                    ->whereIn('resource_id', $subnetIds);
            })->orWhere(function($query) use ($category) {
                $query->where([
                    'resource_type' => self::RESOURCE_TYPE_IP_CATEGORY,
                    'resource_id' => $category->id,
                ]);
            })->pluck('user_id')->toArray();
        }

        return array_unique(array_flatten($userIds));
    }

    /**
     * @return bool
     */
    public function preloadResource()
    {
        try {
            switch ($this->resource_type) {
                case self::RESOURCE_TYPE_DEVICES_FULL:
                    break;
                case self::RESOURCE_TYPE_DEVICES_SECTION:
                    $this->resource = DeviceSection::findOrFail($this->resource_id);
                    break;
                case self::RESOURCE_TYPE_DEVICES_DEVICE:
                    $this->resource = Device::findOrFail($this->resource_id);
                    break;
                case self::RESOURCE_TYPE_IP_CATEGORY:
                    $this->resource = IpCategory::findOrFail($this->resource_id);
                    break;
                case self::RESOURCE_TYPE_IP_SUBNET:
                    $this->resource = IpAddress::findOrFail($this->resource_id);
                    break;
            }

            return true;
        } catch (Exception $e) {
            $this->delete();

            $this->flushCache();

            return false;
        }
    }

    public static function flushCache()
    {
        self::$cachedPermissions = null;
        Cache::forget('permissions');
    }

    public static function getCached()
    {
        if (self::$cachedPermissions) {
            return self::$cachedPermissions;
        }

        self::$cachedPermissions = Cache::get('permissions');

        if (!self::$cachedPermissions) {
            self::$cachedPermissions = self::all()->toArray();

            Cache::put('permissions', self::$cachedPermissions, 30);
        }

        return self::$cachedPermissions;
    }

    /**
     * @param int $userId
     * @return array
     */
    public static function allForUser(int $userId)
    {
        return array_filter(self::getCached(), function ($permission) use ($userId) {
            return $permission['user_id'] === $userId;
        });
    }

    /**
     * Check given permission for given user
     *
     * @param string $action: view, edit, delete, create
     * @param mixed $resource
     * @param int|null $userId
     * @return bool
     */
    public static function can($action, $resource, $userId = null)
    {
        if (!isset(self::$acl[$action])) {
            return false;
        }

        if (is_null($userId)) {
            $userId = auth()->id();
        }
//dd(self::allForUser($userId));
        foreach (self::allForUser($userId) as $permission) {
            if (!in_array(self::$acl[$action], $permission['grant_type'])) {
                continue;
            }

            if ($permission['resource_type'] === self::RESOURCE_TYPE_DEVICES_FULL) {
                return true;
            } elseif ($resource instanceof DeviceSection) {
                if ($permission['resource_type'] === self::RESOURCE_TYPE_DEVICES_SECTION && $permission['resource_id'] == $resource->id) {
                    return true;
                }
            } elseif ($resource instanceof Device) {
                if ($permission['resource_type'] === self::RESOURCE_TYPE_DEVICES_DEVICE && $permission['resource_id'] == $resource->id) {
                    return true;
                }

                if ($permission['resource_type'] === self::RESOURCE_TYPE_DEVICES_SECTION && $permission['resource_id'] == $resource->section_id) {
                    return true;
                }
            } elseif ($resource instanceof IpCategory) {
                if ($permission['resource_type'] === self::RESOURCE_TYPE_IP_CATEGORY && $permission['resource_id'] == $resource->id) {
                    return true;
                }
            } elseif ($resource instanceof IpAddress) {
                $firstInSubnet = $resource->firstInSubnet();

                if ($permission['resource_type'] === self::RESOURCE_TYPE_IP_CATEGORY && $permission['resource_id'] == $firstInSubnet->category_id) {
                    return true;
                }

                if ($permission['resource_type'] === self::RESOURCE_TYPE_IP_SUBNET && $permission['resource_id'] === $firstInSubnet->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if given user can list given resource
     *
     * @param mixed $resource
     * @param null|int $userId
     * @return bool
     */
    public static function canList($resource, $userId = null)
    {
        $userId = is_null($userId) ? auth()->id() : $userId;

        if ($resource instanceof DeviceSection) {
            return self::canListDeviceSection($resource, $userId);
        } elseif ($resource instanceof IpCategory) {
            return self::canListIpCategory($resource, $userId);
        }

        return false;
    }

    /**
     * Check if given user can list given device section
     *
     * @param \App\DeviceSection $section
     * @param int $userId
     * @return bool
     */
    public static function canListDeviceSection(DeviceSection $section, $userId)
    {
        $devices = Device::where('section_id', $section->id)
            ->pluck('id')
            ->toArray();
        $permissions = self::allForUser($userId);

        foreach ($permissions as $permission) {
            switch($permission['resource_type']) {
                case self::RESOURCE_TYPE_DEVICES_FULL:
                    return true;
                case self::RESOURCE_TYPE_DEVICES_SECTION:
                    if ($permission['resource_id'] == $section->id) {
                        return true;
                    }
                    break;
                case self::RESOURCE_TYPE_DEVICES_DEVICE:
                    if (in_array($permission['resource_id'], $devices)) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }

    public static function canListIpCategory(IpCategory $category, $userId)
    {
        $subnetIds = IpAddress::selectRaw('min(id) as id')
            ->where('category_id', $category->id)
            ->groupBy('subnet')
            ->pluck('id')
            ->toArray();

        $permissions = self::allForUser($userId);

        foreach ($permissions as $permission) {
            switch ($permission['resource_type']) {
                case self::RESOURCE_TYPE_DEVICES_FULL:
                    return true;
                case self::RESOURCE_TYPE_IP_CATEGORY:
                    if ($permission['resource_id'] == $category->id) {
                        return true;
                    }
                    break;
                case self::RESOURCE_TYPE_IP_SUBNET:
                    if (in_array($permission['resource_id'], $subnetIds)) {
                        return true;
                    }
                    break;
            }
        }
    }
}
