<?php

namespace App\Fields;

use Form;
use App\Device;

class Textarea extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        return
            $this->prerender() .
            Form::textarea($this->getInputName(), $contents, [
                'class' => 'form-control',
            ]) .
            $this->postrender();
    }

    /**
     * Custom device list content
     *
     * @param Device $model
     * @return string
     */
    public function customDeviceListContent(Device $model)
    {
        if (isset($model->data[$this->getInputName()])) {
            $content = $model->data[$this->getInputName()];
            $content = str_replace(["\r\n", "\n"], '<br>', $content);

            return $content;
        }

        return '';
    }
}
