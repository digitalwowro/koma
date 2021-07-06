<?php

namespace App\Policies;

use App\User;
use App\IpSubnet;
use Illuminate\Auth\Access\HandlesAuthorization;

class IpSubnetPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any ip subnets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the ip subnet.
     *
     * @param  \App\User  $user
     * @param  \App\IpSubnet  $ipSubnet
     * @return mixed
     */
    public function view(User $user, IpSubnet $ipSubnet)
    {
        //
    }

    /**
     * Determine whether the user can create ip subnets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the ip subnet.
     *
     * @param  \App\User  $user
     * @param  \App\IpSubnet  $ipSubnet
     * @return mixed
     */
    public function update(User $user, IpSubnet $ipSubnet)
    {
        //
    }

    /**
     * Determine whether the user can delete the ip subnet.
     *
     * @param  \App\User  $user
     * @param  \App\IpSubnet  $ipSubnet
     * @return mixed
     */
    public function delete(User $user, IpSubnet $ipSubnet)
    {
        //
    }

    /**
     * Determine whether the user can restore the ip subnet.
     *
     * @param  \App\User  $user
     * @param  \App\IpSubnet  $ipSubnet
     * @return mixed
     */
    public function restore(User $user, IpSubnet $ipSubnet)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the ip subnet.
     *
     * @param  \App\User  $user
     * @param  \App\IpSubnet  $ipSubnet
     * @return mixed
     */
    public function forceDelete(User $user, IpSubnet $ipSubnet)
    {
        //
    }
}
