<?php

namespace App\Policies;

use App\EncryptableModel;
use App\User;

abstract class EncryptablePolicy
{
    /**
     * Determine whether the user can update the resource.
     *
     * @param User $user
     * @param EncryptableModel $model
     * @return mixed
     */
    public function update(User $user, EncryptableModel $model)
    {
        if ($model->isOwner($user)) {
            return true;
        }
    }

    /**
     * Determine whether the user can share the resource
     *
     * @param User             $user
     * @param EncryptableModel $model
     * @return mixed
     */
    public function share(User $user, EncryptableModel $model) {
        return $model->isOwner($user);
    }

    /**
     * Determine whether the user owns the resource
     *
     * @param User             $user
     * @param EncryptableModel $model
     * @return mixed
     */
    public function owner(User $user, EncryptableModel $model) {
        return $model->isOwner($user);
    }

    /**
     * Determine whether the user can manage the resource
     *
     * @param User             $user
     * @param EncryptableModel $model
     * @return mixed
     */
    public function manage(User $user, EncryptableModel $model) {
        return $model->isOwner($user); // @todo separate permission?
    }
}
