<?php

namespace App\Scopes;

use App\Item;
use App\Category;
use App\Permission;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemTenant implements Scope
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
        $categories = Category::where('owner_id', $user->id)
            ->pluck('id')
            ->toArray();

        foreach (Permission::allForUser($user) as $permission) {
            if ($permission['resource_type'] === Permission::RESOURCE_TYPE_ITEM) {
                $accessibleIds[] = $permission['resource_id'];
            } elseif ($permission['resource_type'] === Permission::RESOURCE_TYPE_CATEGORY) {
                $categories[] = $permission['resource_id'];
            }
        }

        if (count($categories)) {
            $accessibleIds = Item::withoutGlobalScope(ItemTenant::class)
                ->whereIn('category_id', $categories)
                ->pluck('id')
                ->merge($accessibleIds)
                ->unique()
                ->toArray();
        }

        $builder->whereIn('id', $accessibleIds);
    }
}
