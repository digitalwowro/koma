<?php

namespace App\Composers;

use Illuminate\Contracts\View\View;
use App\User;

class UserComposer
{
    /**
     * The User model
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new composer.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function admin(View $view)
    {
        $view->with('users', $this->user->pagedForAdmin());
    }

}
