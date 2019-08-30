<?php

namespace App\Scopes;

use App\Device;
use App\Permission;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DeviceSectionTenant implements Scope
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
        $devices = [];

        foreach (Permission::allForUser($user) as $permission) {
            if ($permission['resource_type'] === Permission::RESOURCE_TYPE_DEVICE_SECTION) {
                $accessibleIds[] = $permission['resource_id'];
            } elseif ($permission['resource_type'] === Permission::RESOURCE_TYPE_DEVICE) {
                $devices[] = $permission['resource_id'];
            }
        }

        if (count($devices)) {
            $accessibleIds = Device::withoutGlobalScope(DeviceTenant::class)
                ->whereIn('id', $devices)
                ->pluck('section_id')
                ->merge($accessibleIds)
                ->unique()
                ->toArray();
        }

        $builder->where(function($query) use ($user, $accessibleIds) {
            $query
                ->where('owner_id', $user->id)
                ->orWhereIn('id', $accessibleIds);
        });
    }
}
