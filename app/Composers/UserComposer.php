<?php

namespace App\Composers;

use Illuminate\Contracts\View\View;
use App\User;

class UserComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function admin(View $view)
    {
        $view->with('users', User::pagedForAdmin());
    }

    public function shareModal(View $view)
    {
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => gravatar($user->email, 40),
                ];
            })
            ->toJson();

        $view->with('users', $users);
    }

}
