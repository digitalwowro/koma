<?php

namespace App\Policies;

use App\User;
use App\IpCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class IpCategoryPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any ip categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the ip category.
     *
     * @param  \App\User  $user
     * @param  \App\IpCategory  $ipCategory
     * @return mixed
     */
    public function view(User $user, IpCategory $ipCategory)
    {
        //
    }

    /**
     * Determine whether the user can create ip categories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the ip category.
     *
     * @param  \App\User  $user
     * @param  \App\IpCategory  $ipCategory
     * @return mixed
     */
    public function update(User $user, IpCategory $ipCategory)
    {
        //
    }

    /**
     * Determine whether the user can delete the ip category.
     *
     * @param  \App\User  $user
     * @param  \App\IpCategory  $ipCategory
     * @return mixed
     */
    public function delete(User $user, IpCategory $ipCategory)
    {
        //
    }

    /**
     * Determine whether the user can restore the ip category.
     *
     * @param  \App\User  $user
     * @param  \App\IpCategory  $ipCategory
     * @return mixed
     */
    public function restore(User $user, IpCategory $ipCategory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the ip category.
     *
     * @param  \App\User  $user
     * @param  \App\IpCategory  $ipCategory
     * @return mixed
     */
    public function forceDelete(User $user, IpCategory $ipCategory)
    {
        //
    }
}
