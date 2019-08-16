<?php

namespace App\Composers;

use Illuminate\Contracts\View\View;
use App\Group;
use Illuminate\Support\Str;

class GroupComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function admin(View $view)
    {
        $view->with('groups', Group::pagedForAdmin());
    }

    public function shareModal(View $view)
    {
        $groups = auth()->user()
            ->groups()
            ->select('groups.id', 'groups.name')
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                $memberCount = $group->users()->count();

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'email' => $memberCount . ' group '. Str::plural('member', $memberCount),
                    'avatar' => gravatar("group_id_{$group->id}", 40, 'retro'),
                ];
            })
            ->toJson();

        $view->with('groups', $groups);
    }

    public function keyValue(View $view)
    {
        $groups = auth()->user()
            ->groups()
            ->pluck('groups.name', 'groups.id');

        $view->with('groups', $groups);
    }

}
