<?php

namespace App\Fields;

use App\Item;
use Form;

class ID extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        return false;
    }

    /**
     * Custom device list content
     *
     * @param Item $model
     * @return string
     */
    public function customDeviceListContent(Item $model)
    {
        $prefix = $this->getOption('prefix', '');
        return $prefix . $model->id;
    }

    /**
     * Returns whether the current field should be shown in the devices list
     *
     * @return bool
     */
    public function showInDeviceList()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function renderOptions($index)
    {
        $value = $this->getOption('prefix', '');
        $name  = 'fields[' . $index . '][options][prefix]';

        return
            '<label>ID Prefix</label>' .
            Form::text($name, $value, [
                'class'       => 'form-control',
                'placeholder' => 'e.g: SERV-',
            ]);
    }

}
