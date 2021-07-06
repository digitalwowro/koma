<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InvalidResourceException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EncryptedStore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'resource_type', 'resource_id', 'data'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'encrypted_store';

    /**
     * Relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @param EncryptableModel $resource
     * @param array $data
     * @param User  $user
     * @throws InvalidResourceException
     */
    public static function upsertSingle(EncryptableModel $resource, array $data, User $user)
    {
        EncryptedStore::updateOrInsert([
            'user_id' => $user->id,
            'resource_type' => getResourceType($resource),
            'resource_id' => $resource->id,
        ], [
            'data' => app('encrypt')->encryptForUser(json_encode($data), $user),
        ]);
    }

    /**
     * @param EncryptableModel $resource
     * @param array         $data
     * @return void
     * @throws InvalidResourceException
     */
    public static function upsert(EncryptableModel $resource, array $data)
    {
        $userIds = Permission::userIdsHavingPermission($resource);

        User::whereIn('id', $userIds)
            ->each(function (User $user) use ($resource, &$data) {
                self::upsertSingle($resource, $data, $user);
            });

        EncryptedStore::whereNotIn('user_id', $userIds)
            ->where('resource_type', getResourceType($resource))
            ->where('resource_id', $resource->id)
            ->delete();
    }

    /**
     * @param EncryptableModel $resource
     * @param bool $throwException
     * @return array
     * @throws InvalidResourceException
     * @throws ModelNotFoundException
     */
    public static function pull(EncryptableModel $resource, $throwException = false) : array
    {
        try {
            $encrypted = EncryptedStore::where([
                'user_id' => auth()->id(),
                'resource_type' => getResourceType($resource),
                'resource_id' => $resource->id,
            ])->firstOrFail();

            $encryption = app('encrypt');

            $return = @json_decode($encryption->decrypt($encrypted->data), true);

            return is_array($return) ? $return : [];
        } catch (ModelNotFoundException $e) {
            if ($throwException) {
                throw $e;
            }

            return [];
        }
    }

    /**
     * Destroy resource permissions / encrypted store data
     *
     * @param array|int $resource
     * @return int|void
     * @throws InvalidResourceException
     */
    public static function destroy($resource)
    {
        $filters = [
            'resource_type' => getResourceType($resource),
            'resource_id' => $resource->id,
        ];

        EncryptedStore::where($filters)->delete();

        Permission::where($filters)->delete();

        if ($resource instanceof Category) {
            $deviceIds = $resource->devices()->pluck('id');

            EncryptedStore::where('resource_type', Permission::RESOURCE_TYPE_ITEM)
                ->whereIn('resource_id', $deviceIds)
                ->delete();

            Permission::where('resource_type', Permission::RESOURCE_TYPE_ITEM)
                ->whereIn('resource_id', $deviceIds)
                ->delete();
        }

        if ($resource instanceof IpCategory) {
            $subnetIds = $resource->ips()->pluck('id');

            EncryptedStore::where('resource_type', Permission::RESOURCE_TYPE_IP_SUBNET)
                ->whereIn('resource_id', $subnetIds)
                ->delete();

            Permission::where('resource_type', Permission::RESOURCE_TYPE_IP_SUBNET)
                ->whereIn('resource_id', $subnetIds)
                ->delete();
        }
    }

    /**
     * @param User  $user
     * @param array $queries
     */
    public static function ensurePermissions(User $user, array $queries)
    {
        $existing = EncryptedStore::select('id', 'resource_type', 'resource_id')
            ->where('user_id', $user->id)
            ->get();

        foreach ($queries as $query) {
            $query->chunk(200, function($resources) use (&$existing, $user) {
                $resourceType = getResourceType($resources->first());

                foreach ($resources as $resource) {
                    $test = function ($value) use ($resource, $user, $resourceType) {
                        return intval($value->resource_type) === $resourceType
                            && intval($value->resource_id) === intval($resource->id);
                    };

                    $exists = $existing->first($test);

                    if ($exists) {
                        $existing = $existing->reject($test);

                        continue;
                    }

                    $data = self::pull($resource);
                    self::upsertSingle($resource, $data, $user);
                }
            });
        }

        EncryptedStore::whereIn('id', $existing->pluck('id'))->delete();
    }

}
