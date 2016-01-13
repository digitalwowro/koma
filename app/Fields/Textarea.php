<?php

namespace App\Fields;

use Form;

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

}
