<?php

namespace App\Fields;

use Form;

class Date extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        return
            $this->prerender() .
            Form::text($this->getInputName(), $contents, [
                'class' => 'form-control datepicker',
            ]) .
            $this->postrender();
    }
}
