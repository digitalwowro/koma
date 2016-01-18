<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class DeviceSectionPresenter extends Presenter
{
    /**
     * @return string
     */
    public function icon()
    {
        return '<i class="fa fa-' . $this->entity->icon . '"></i>';
    }

}
