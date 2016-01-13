<?php

namespace App\Fields;

abstract class AbstractField
{
    /**
     * Abstract methods
     */
    abstract public function render();

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    protected $showInDeviceList = false;

    /**
     * AbstractField constructor.
     *
     * @param string $name
     * @param array $config
     */
    public function __construct($name, array $config = [])
    {
        $this->name   = $name;
        $this->config = $config;
    }

    /**
     * Get field type
     *
     * @return string
     */
    public function getType()
    {
        return @end(explode('\\', get_class($this)));
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns whether the current field should be shown in the devices list
     *
     * @return bool
     */
    public function showInDeviceList()
    {
        return $this->showInDeviceList;
    }

    public function getOptions()
    {
        return '..2.';
    }

    /**
     * @return string
     */
    public function prerender()
    {
        return
            '<div class="form-group">' .
                '<label for="title" class="col-lg-2 control-label">' . $this->getName() . '</label>' .
                    '<div class="col-lg-10">';
    }

    /**
     * @return string
     */
    public function postrender()
    {
        return
                '</div>' .
            '</div>';
    }


}
