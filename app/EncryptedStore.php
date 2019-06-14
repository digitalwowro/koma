<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InvalidResourceException;

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

    public static function upsertSingle($resource, array $data, User $user)
    {
        $resourceType = getResourceType($resource);

        return EncryptedStore::updateOrInsert([
            'user_id' => $user->id,
            'resource_type' => $resourceType,
            'resource_id' => $resource->id,
        ], [
            'data' => app('encrypt')->encryptForUser(json_encode($data), $user),
        ]);
    }

    /**
     * @param Device|IpAddress $resource
     * @param array            $data
     * @return void
     * @throws InvalidResourceException
     */
    public static function upsert($resource, array $data)
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
     * @param Device|IpAddress $resource
     * @return array
     */
    public static function pull($resource) : array
    {
        try {
            $encrypted = EncryptedStore::where([
                'user_id' => auth()->id(),
                'resource_type' => getResourceType($resource),
                'resource_id' => $resource->id,
            ])->firstOrFail();

            $return = @json_decode(app('encrypt')->decrypt($encrypted->data), true);

            return is_array($return) ? $return : [];
        } catch (\Exception $e) {
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
    }
}
