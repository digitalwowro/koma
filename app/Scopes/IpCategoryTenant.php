<?php

namespace App\Scopes;

use App\IpAddress;
use App\Permission;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class IpCategoryTenant implements Scope
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
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $accessibleIds = [];
        $subnets = [];

        foreach (Permission::allForUser($user) as $permission) {
            if ($permission['resource_type'] === Permission::RESOURCE_TYPE_IP_CATEGORY) {
                $accessibleIds[] = $permission['resource_id'];
            } elseif ($permission['resource_type'] === Permission::RESOURCE_TYPE_IP_SUBNET) {
                $subnets[] = $permission['resource_id'];
            }
        }

        if (count($subnets)) {
            $accessibleIds = IpAddress::withoutGlobalScope(IpAddressTenant::class)
                ->whereIn('id', $subnets)
                ->pluck('category_id')
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
