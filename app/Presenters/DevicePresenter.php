<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class DevicePresenter extends Presenter
{
    /**
     * @param null|\App\DeviceSection $section
     * @return string
     */
    public function humanIdField($section = null)
    {
        $device = $this->entity;
        $firstField = '';

        if (is_null($section)) {
            $section = $device->section;
        }

        foreach ($section->fields as $field) {
            if ($field->getType() === 'Text') {
                $firstField = $device->data[$field->getKey()] ?? '';
                break;
            }
        }

        if ($firstField && is_string($firstField)) {
            return $firstField;
        }

        return $this->entity->section->present()->idPrefix . $this->entity->id;
    }

}
