<?php

namespace App\Fields;

use App\Device;
use Form;

class Text extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        return
            $this->prerender() .
            Form::text($this->getInputName(), $contents, [
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
        if (isset($model->data[$this->getInputName()]))
        {
            $content = $model->data[$this->getInputName()];
            $old = $content;

            $content = urlify($content);
            return $old === $content
                ? '<input type="text" value="' . htmlentities($content) . '" readonly style="border: none; background-color: transparent;" onclick="this.setSelectionRange(0, this.value.length)">'
                : $content;
        }

        return '-';
    }

}
