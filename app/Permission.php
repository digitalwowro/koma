<?php

namespace App;

use App\Presenters\PermissionPresenter;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Arr;
use Laracasts\Presenter\PresentableTrait;

class Permission extends Model
{
    use PresentableTrait;

    const GRANT_TYPE_READ = 1;
    const GRANT_TYPE_EDIT = 2;
    const GRANT_TYPE_DELETE = 4;
    const GRANT_TYPE_CREATE = 8;

    const RESOURCE_TYPE_DEVICE_SECTION = 1;
    const RESOURCE_TYPE_DEVICE = 2;
    const RESOURCE_TYPE_IP_CATEGORY = 3;
    const RESOURCE_TYPE_IP_SUBNET = 4;

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
    protected $fillable = [
        'resource_type', 'resource_id', 'resource_type', 'grant_type',
        'user_id', 'group_id',
    ];

    private static $acl = [
        'view' => self::GRANT_TYPE_READ,
        'edit' => self::GRANT_TYPE_EDIT,
        'delete' => self::GRANT_TYPE_DELETE,
        'create' => self::GRANT_TYPE_CREATE,
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
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @var mixed
     */
    public $resource;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

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
            $this::GRANT_TYPE_EDIT,
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

    /**
     * @param array $list
     * @return array
     */
    private static function userIdsFromPermissionList(array $list)
    {
        $userIds = [];
        $groupIds = [];

        foreach ($list as $data) {
            if (!empty($data['user_id'])) {
                $userIds[] = $data['user_id'];
            }

            if (!empty($data['group_id'])) {
                $groupIds[] = $data['group_id'];
            }
        }

        if (count($groupIds)) {
            Group::with('users')
                ->whereIn('id', $groupIds)
                ->each(function ($group) use (&$userIds) {
                    $userIds = array_merge($userIds, $group->users->pluck('id')->toArray());
                });

            $userIds = array_unique($userIds);
        }

        return $userIds;
    }

    /**
     * @param mixed $resource
     * @return array
     */
    public static function userIdsHavingPermission($resource)
    {
        $userIds = [];

        if ($resource instanceof Device || $resource instanceof DeviceSection) {
            $section = $resource instanceof Device
                ? $resource->section
                : $resource;

            if ($section->owner_id) {
                $userIds[] = $section->owner_id;
            }

            $list = self::select('user_id', 'group_id')
                ->where(function($query) use ($section) {
                    $query
                        ->where('resource_type', self::RESOURCE_TYPE_DEVICE)
                        ->whereIn('resource_id', function($query) use ($section) {
                            $query
                                ->select('id')
                                ->where('section_id', $section->id)
                                ->from('devices');
                        });
                })
                ->orWhere(function($query) use ($section) {
                    $query->where([
                        'resource_type' => self::RESOURCE_TYPE_DEVICE_SECTION,
                        'resource_id' => $section->id,
                    ]);
                })
                ->get()
                ->toArray();

            $userIds[] = self::userIdsFromPermissionList($list);
        } elseif ($resource instanceof IpAddress || $resource instanceof IpCategory) {
            $category = $resource instanceof Device
                ? $resource->category
                : $resource;

            if ($category->owner_id) {
                $userIds[] = $category->owner_id;
            }

            $list = self::select('user_id', 'group_id')
                ->where(function($query) use ($category) {
                    $subnetIds = IpAddress::getSubnetsFor($category->id)->pluck('id');

                    $query
                        ->where('resource_type', self::RESOURCE_TYPE_IP_SUBNET)
                        ->whereIn('resource_id', $subnetIds);
                })
                ->orWhere(function($query) use ($category) {
                    $query->where([
                        'resource_type' => self::RESOURCE_TYPE_IP_CATEGORY,
                        'resource_id' => $category->id,
                    ]);
                })
                ->get()
                ->toArray();

            $userIds[] = self::userIdsFromPermissionList($list);
        }

        return array_unique(Arr::flatten($userIds));
    }

    /**
     * @return bool
     */
    public function preloadResource()
    {
        try {
            switch ($this->resource_type) {
                case self::RESOURCE_TYPE_DEVICE_SECTION:
                    $this->resource = DeviceSection::findOrFail($this->resource_id);
                    break;
                case self::RESOURCE_TYPE_DEVICE:
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
        cache()->forget('permissions');
    }

    public static function getCached()
    {
        if (self::$cachedPermissions) {
            return self::$cachedPermissions;
        }

        self::$cachedPermissions = cache('permissions');

        if (!self::$cachedPermissions) {
            self::$cachedPermissions = self::all()->toArray();

            cache()->put('permissions', self::$cachedPermissions, 1800);
        }

        return self::$cachedPermissions;
    }

    /**
     * @param User $user
     * @return array
     */
    public static function allForUser(User $user)
    {
        $groupIds = $user->groups
            ->pluck('id')
            ->toArray();

        return array_filter(self::getCached(), function ($permission) use ($user, $groupIds) {
            if (!empty($permission['user_id']) && $permission['user_id'] === $user->id) {
                return true;
            }

            if (!empty($permission['group_id']) && in_array($permission['group_id'], $groupIds)) {
                return true;
            }

            return false;
        });
    }

    /**
     * Check given permission for given user
     *
     * @param string $action: view, edit, delete, create
     * @param mixed $resource
     * @param User $user
     * @return bool
     */
    public static function can($action, $resource, User $user)
    {
        if (!isset(self::$acl[$action])) {
            return false;
        }

        // allow custom IPs
        if ($resource instanceof IpAddress && !$resource->subnet) {
            $resource = $resource->device;
        }

        if ($resource->isOwner($user)) {
            return true;
        }

        foreach (self::allForUser($user) as $permission) {
            if (!in_array(self::$acl[$action], $permission['grant_type'])) {
                continue;
            }

            if ($resource instanceof DeviceSection) {
                if ($permission['resource_type'] === self::RESOURCE_TYPE_DEVICE_SECTION && $permission['resource_id'] == $resource->id) {
                    return true;
                }
            } elseif ($resource instanceof Device) {
                if ($permission['resource_type'] === self::RESOURCE_TYPE_DEVICE && $permission['resource_id'] == $resource->id) {
                    return true;
                }

                if ($permission['resource_type'] === self::RESOURCE_TYPE_DEVICE_SECTION && $permission['resource_id'] == $resource->section_id) {
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
}
