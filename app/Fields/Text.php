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
        $return = '-';

        if (isset($model->data[$this->getInputName()]))
        {
            $content = $model->data[$this->getInputName()];

            $return = urlify($content);

            if (filter_var($this->getOption('copypaste', ''), FILTER_VALIDATE_BOOLEAN))
            {
                $return .= ' <a href="#" class="copy-this" data-clipboard-text="' . htmlentities($content) . '"><i class="fa fa-copy" title="Copy to Clipboard"></i></a>';
            }
        }

        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function renderOptions($index)
    {
        $checked = filter_var($this->getOption('copypaste', ''), FILTER_VALIDATE_BOOLEAN);
        $rand    = $this->getTotallyRandomString();
        $name    = 'fields[' . $index . '][options][copypaste]';

        return
            parent::renderOptions($index) .
            '<div class="checkbox-nice">' .
                Form::checkbox($name, 1, $checked, [
                    'id' => $rand,
                ]) .
            '<label for="' . $rand . '">' .
            'Show copy text button for this field' .
            '</label>' .
            '</div>';
    }

}
