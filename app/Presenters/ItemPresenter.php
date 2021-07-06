<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class ItemPresenter extends Presenter
{
    /**
     * @param null|\App\Category $category
     * @return string
     */
    public function humanIdField($category = null)
    {
        $device = $this->entity;
        $firstField = '';

        if (is_null($category)) {
            $category = $device->section;
        }

        foreach ($category->fields as $field) {
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
