<?php

namespace App\Fields;

use Form;

class Radio extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        $options = $this->getOption('options', '');
        $options = explode(',', $options);

        $return = $this->prerender();

        foreach ($options as $option)
        {
            $rand = md5(time() . mt_rand(0, 999999) . rand(0, 999999));
            $checked = $contents == $option;

            $return .=
                '<div class="radio">' .
                    Form::radio($this->getInputName(), $option, $checked, [
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
