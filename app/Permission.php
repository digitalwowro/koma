<?php

namespace App;

use App\Presenters\PermissionPresenter;
use Illuminate\Database\Eloquent\Model;
use Cache, Exception;
use Laracasts\Presenter\PresentableTrait;

class Permission extends Model
{
    use PresentableTrait;

    const GRANT_TYPE_NONE        = 0;
    const GRANT_TYPE_READ        = 1;
    const GRANT_TYPE_WRITE       = 2;
    const GRANT_TYPE_FULL        = 3;
    const GRANT_TYPE_CREATE      = 4; // only for device sections
    const GRANT_TYPE_READ_CREATE = 5; // only for device sections
    const GRANT_TYPE_OWNER       = 6; // only for device sections

    const RESOURCE_TYPE_DEVICES_FULL    = 1;
    const RESOURCE_TYPE_DEVICES_SECTION = 2;
    const RESOURCE_TYPE_DEVICES_DEVICE  = 3;

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
        'view' => [
            self::GRANT_TYPE_READ,
            self::GRANT_TYPE_WRITE,
            self::GRANT_TYPE_FULL,
            self::GRANT_TYPE_READ_CREATE,
            self::GRANT_TYPE_OWNER,
        ],

        'edit' => [
            self::GRANT_TYPE_WRITE,
            self::GRANT_TYPE_FULL,
            self::GRANT_TYPE_OWNER,
        ],

        'delete' => [
            self::GRANT_TYPE_FULL,
            self::GRANT_TYPE_OWNER,
        ],

        'create' => [
            self::GRANT_TYPE_WRITE,
            self::GRANT_TYPE_FULL,
            self::GRANT_TYPE_CREATE,
            self::GRANT_TYPE_READ_CREATE,
            self::GRANT_TYPE_OWNER,
        ],

        'manage' => [
            self::GRANT_TYPE_OWNER,
        ],
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

        foreach (self::getCached() as $permission) {
            if ($permission['user_id'] !== $userId) {
                continue;
            }

            if (!in_array($permission['grant_type'], self::$acl[$action])) {
                continue;
            }

            if ($permission['resource_type'] == self::RESOURCE_TYPE_DEVICES_FULL) {
                return true;
            } elseif ($resource instanceof DeviceSection) {
                if ($permission['resource_type'] == self::RESOURCE_TYPE_DEVICES_SECTION && $permission['resource_id'] == $resource->id) {
                    return true;
                }
            } elseif ($resource instanceof Device) {
                if ($permission['resource_type'] == self::RESOURCE_TYPE_DEVICES_DEVICE && $permission['resource_id'] == $resource->id) {
                    return true;
                }

                if ($permission['resource_type'] == self::RESOURCE_TYPE_DEVICES_SECTION && $permission['resource_id'] == $resource->section_id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if given user can list given device section
     *
     * @param \App\DeviceSection $section
     * @param int|null $userId
     * @return bool
     */
    public static function canList(DeviceSection $section, $userId = null)
    {
        $userId = is_null($userId) ? auth()->id() : $userId;
        $devices = Device::where('section_id', $section->id)->lists('id')->toArray();

        foreach (self::getCached() as $permission) {
            if ($permission['user_id'] == $userId) {
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
        }

        return false;
    }

    public static function sectionOwnership($userId = null)
    {
        $userId = is_null($userId) ? auth()->id() : $userId;

        foreach (self::getCached() as $permission) {
            if ($permission['user_id'] == $userId
                && $permission['resource_type'] === self::RESOURCE_TYPE_DEVICES_SECTION
                && $permission['grant_type'] === self::GRANT_TYPE_OWNER) {
                return true;
            }
        }

        return false;
    }

    public static function manageableSections(User $user = null)
    {
        $user = $user instanceof User ? $user : auth()->user();

        if ($user->isAdmin()) {
            return false;
        }

        $results = [];

        foreach (self::getCached() as $permission) {
            if ($permission['user_id'] == $user->id
                && $permission['resource_type'] === self::RESOURCE_TYPE_DEVICES_SECTION
                && $permission['grant_type'] === self::GRANT_TYPE_OWNER) {
                $results[] = $permission['resource_id'];
            }
        }

        return $results;
    }
}
