<?php

namespace App\Fields;

use Form;
use App\Device;

class Password extends Text
{
    public function mask() {
        return filter_var($this->getOption('mask', ''), FILTER_VALIDATE_BOOLEAN);
    }

    public function prerender()
    {
        return parent::prerender() . '<div class="input-group">';
    }

    /**
     * @return string
     */
    public function postrender()
    {
        return  '<span class="input-group-btn">' .
                '<button class="btn btn-default" type="button" data-action="password-mask" title="Mask/Unmask password"><i class="fa fa-eye"></i></button>' .
                '<button class="btn btn-info" type="button" data-action="password-generator">Password Generator</button>' .
                '</span>' .
            '</div>' . parent::postrender();
    }

    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        $type = $this->mask() ? 'password' : 'text';
        return
            $this->prerender() .

            Form::input($type, $this->getInputName(), $contents, [
                'class' => 'form-control',
            ]) .

            $this->postrender();
    }

    /**
     * {@inheritDoc}
     */
    public function renderOptions($index)
    {
        $name = 'fields[' . $index . '][options][mask]';
        $checked = $this->mask();

        return
            '<div class="checkbox icheck">' .
                '<label>' .
                    Form::checkbox($name, 1, $checked) .
                    ' Mask password (********)' .
                '</label>' .
            '</div>' .
            parent::renderOptions($index);
    }

    /**
     * Custom device list content
     *
     * @param Device $model
     * @return string
     */
    public function customDeviceListContent(Device $model)
    {
        $return = '-';

        if (isset($model->data[$this->getInputName()])) {
            $content = $model->data[$this->getInputName()];

            $return = $this->mask() ? str_repeat('*', strlen($content)) : $content;
            $return = "<span>{$return}</span>";

            if ($content && $this->copyPaste()) {
                $return .= ' <a href="#" class="copy-this" data-clipboard-text="' . htmlentities($content) . '"><i class="fa fa-copy" title="Copy to Clipboard"></i></a>';
            }

            if ($this->mask()) {
                $return .= ' <a href="#" class="mask-this" data-value="' . htmlentities($content) . '"><i class="fa fa-eye" title="Mask/Unmask password"></i></a>';
            }
        }

        return $return;
    }
}
