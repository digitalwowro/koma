<?php

namespace App;

use App\Presenters\ItemPresenter;
use App\Scopes\ItemTenant;
use App\EncryptableModel;
use Laracasts\Presenter\PresentableTrait;

class Item extends EncryptableModel
{
    use PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = ItemPresenter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['section_id', 'data', 'created_by'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ItemTenant);
    }

    /**
     * Relationship with Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo('App\Category', 'section_id');
    }

    /**
     * Returns all permissions referring to this resource
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sharedWith()
    {
        return Permission::with('user', 'group')
            ->where('resource_type', Permission::RESOURCE_TYPE_ITEM)
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

    public function ips() : array
    {
        $results = [];

        IpSubnet::each(function($subnet) use (&$results) {
            foreach ((array) $subnet->assigned as $key => $assignment) {
                if (!is_array($assignment) || empty($assignment['device_id'])) {
                    continue;
                }

                if ($assignment['device_id'] === $this->id) {
                    $results[] = [
                        'ip' => $key,
                        'subnet' => $subnet,
                    ];
                }
            }
        });

        $customIps = $this->ips ?? [];

        foreach ($customIps as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                continue;
            }

            $results[] = [
                'ip' => $ip,
                'custom' => true,
            ];
        }

        return $results;
    }

    /**
     * @param IpField $field
     * @return string
     */
    public function ipFieldValue(IpField $field)
    {
        $deviceType = $this->section_id;

        if (isset($field->bindings[$deviceType])) {
            $bindTo = $field->bindings[$deviceType];

            foreach ($this->section->fields as $deviceField) {
                if ($deviceField->getInputName() == $bindTo) {
                    if (method_exists($deviceField, 'customDeviceListContent')) {
                        return $deviceField->customDeviceListContent($this);
                    } elseif (isset($this->data[$deviceField->getInputName()])) {
                        if (is_array($this->data[$deviceField->getInputName()])) {
                            return urlify(implode(', ', $this->data[$deviceField->getInputName()]));
                        } else {
                            return urlify($this->data[$deviceField->getInputName()]);
                        }
                    } else {
                        return '-';
                    }
                }
            }
        }

        return '-';
    }
}
