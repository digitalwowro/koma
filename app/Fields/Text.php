<?php

namespace App\Fields;

class Text extends AbstractField
{
    protected $showInDeviceList = true;

    public function render()
    {
        return $this->prerender() . '<input type="text" class="form-control" name="' . $this->getName() . '"></input>' . $this->postrender();
    }
}
