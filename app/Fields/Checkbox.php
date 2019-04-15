<?php

namespace App\Fields;

use Form;

class Checkbox extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        $options = $this->getOption('options', '');
        $options = explode(',', $options);

        $return = $this->prerender();

        foreach ($options as $option) {
            $rand    = $this->getTotallyRandomString();
            $checked = is_array($contents) && in_array($option, $contents);

            $return .=
                '<div class="checkbox-nice">' .
                Form::checkbox($this->getInputName() . '[]', $option, $checked, [
                    'id' => $rand,
                ]) .
                '<label for="' . $rand . '">' .
                htmlentities($option) .
                '</label>' .
                '</div>';
        }

        return $return . $this->postrender();
    }

    /**
     * {@inheritDoc}
     */
    public function renderOptions($index)
    {
        $value  = $this->getOption('options', '');
        $name   = 'fields[' . $index . '][options][options]';

        return
            '<label>Comma separated options</label>' .
            Form::text($name, $value, [
                'class'       => 'form-control',
                'placeholder' => 'e.g: Ubuntu,Debian,CentOS',
            ]) .
            '<hr>' .
            parent::renderOptions($index);
    }

}
