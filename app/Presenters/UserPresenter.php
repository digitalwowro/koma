<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class UserPresenter extends Presenter
{
    /**
     * @return string
     */
    public function groups()
    {
        $groups = $this->entity->groups;

        return $groups->count()
            ? $groups->pluck('name')->join(', ')
            : '-';
    }

}
