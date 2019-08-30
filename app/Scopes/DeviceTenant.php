<?php

namespace App\Scopes;

use App\Device;
use App\DeviceSection;
use App\Permission;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DeviceTenant implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!$user = auth()->user()) {
            return;
        }

        $accessibleIds = [];
        $sections = DeviceSection::where('owner_id', $user->id)
            ->pluck('id')
            ->toArray();

        foreach (Permission::allForUser($user) as $permission) {
            if ($permission['resource_type'] === Permission::RESOURCE_TYPE_DEVICE) {
                $accessibleIds[] = $permission['resource_id'];
            } elseif ($permission['resource_type'] === Permission::RESOURCE_TYPE_DEVICE_SECTION) {
                $sections[] = $permission['resource_id'];
            }
        }

        if (count($sections)) {
            $accessibleIds = Device::withoutGlobalScope(DeviceTenant::class)
                ->whereIn('section_id', $sections)
                ->pluck('id')
                ->merge($accessibleIds)
                ->unique()
                ->toArray();
        }

        $builder->whereIn('id', $accessibleIds);
    }
}
