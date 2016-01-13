<?php

namespace App\Fields;

class Textarea extends AbstractField
{
    public function render()
    {
        return $this->prerender() . '<textarea class="form-control" name="' . $this->getName() . '"></textarea>' . $this->postrender();
    }

}
