<?php

namespace App\Fields;

use Form;

class Dropdown extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        $options = $this->getOption('options', '');
        $options = explode(',', $options);
        $selectItems = [];

        foreach ($options as $option)
        {
            $selectItems[$option] = $option;
        }

        return
            $this->prerender() .
            Form::select($this->getInputName(), $selectItems, $contents, [
                'class' => 'form-control',
            ]) .
            $this->postrender();
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
