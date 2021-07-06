<?php

namespace App\Fields;

use App\Item;
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
     * @param Item $model
     * @return string
     */
    public function customDeviceListContent(Item $model)
    {
        $return = '-';

        if (isset($model->data[$this->getInputName()])) {
            $content = $model->data[$this->getInputName()];

            $return = urlify($content);

            if ($content && $this->copyPaste()) {
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
        $checked = $this->copyPaste();
        $name = 'fields[' . $index . '][options][copypaste]';

        return
            parent::renderOptions($index) .
            '<div class="checkbox icheck">' .
                '<label>' .
                    Form::checkbox($name, 1, $checked) .
                    ' Show copy text button for this field' .
                '</label>' .
            '</div>';
    }

}
