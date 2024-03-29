<?php

namespace App\Scopes;

use App\Device;
use App\IpSubnet;
use App\IpCategory;
use App\Permission;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class IpSubnetTenant implements Scope
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
        $categories = IpCategory::where('owner_id', $user->id)
            ->pluck('id')
            ->toArray();

        foreach (Permission::allForUser($user) as $permission) {
            if ($permission['resource_type'] === Permission::RESOURCE_TYPE_IP_SUBNET) {
                $accessibleIds[] = $permission['resource_id'];
            } elseif ($permission['resource_type'] === Permission::RESOURCE_TYPE_IP_CATEGORY) {
                $categories[] = $permission['resource_id'];
            }
        }

        if (count($categories)) {
            $accessibleIds = IpSubnet::withoutGlobalScope(IpSubnetTenant::class)
                ->whereIn('category_id', $categories)
                ->pluck('id')
                ->merge($accessibleIds)
                ->unique()
                ->toArray();
        }

        $builder->whereIn('id', $accessibleIds);
    }
}
