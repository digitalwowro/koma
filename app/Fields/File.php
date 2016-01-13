<?php

namespace App\Fields;

use Form;

class File extends AbstractField
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
}
