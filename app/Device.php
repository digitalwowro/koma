<?php

namespace App;

use App\Presenters\DevicePresenter;
use App\Scopes\DeviceTenant;
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
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new DeviceTenant);
    }

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

    /**
     * Returns all permissions referring to this resource
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sharedWith()
    {
        return Permission::with('user', 'group')
            ->where('resource_type', Permission::RESOURCE_TYPE_DEVICE)
            ->where('resource_id', $this->id)
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
