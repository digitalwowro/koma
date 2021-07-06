<?php

namespace App\Fields;

use Form;
use App\Item;

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
     * @param Item $model
     * @return string
     */
    public function customDeviceListContent(Item $model)
    {
        if (isset($model->data[$this->getInputName()])) {
            $content = $model->data[$this->getInputName()];

            return xss_safe_newline_to_br($content);
        }

        return '';
    }
}
