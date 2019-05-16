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

    public function idPrefix()
    {
        foreach ($this->entity->fields as $field) {
            if ($field->getType() === 'ID') {
                $prefix = $field->getOption('prefix');

                if ($prefix) {
                    return $prefix;
                }
            }
        }

        return '';
    }
}
