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
    protected $fillable = ['section_id', 'data', 'created_by'];

    private $decrypted;

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
     * Decode the data field
     *
     * @return array
     */
    public function getDataAttribute() : array
    {
        if (is_null($this->decrypted)) {
            $this->decrypted = EncryptedStore::pull($this);
        }

        return $this->decrypted;
    }

    public function sharedWith()
    {
        return Permission::with('user')
            ->orWhere(function($query) {
                $query
                    ->where('resource_type', Permission::RESOURCE_TYPE_DEVICE)
                    ->where('resource_id', $this->id);
            })
            ->orWhere(function($query) {
                $query
                    ->where('resource_type', Permission::RESOURCE_TYPE_DEVICE_SECTION)
                    ->where('resource_id', $this->section_id);
            })
            ->get();
    }

    /**
     * Returns whether given user is owner of current resource
     *
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->section->owner_id === $user->id;
    }
}
