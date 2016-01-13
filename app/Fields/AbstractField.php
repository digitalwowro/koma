<?php

namespace App\Fields;

use Form;

abstract class AbstractField
{
    /**
     * Abstract methods
     */

    /**
     * Render edit HTML code
     *
     * @param string|null $contents
     * @return string
     */
    abstract public function render($contents = null);

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options;

    /**
     * AbstractField constructor.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options = [])
    {
        $this->name    = $name;
        $this->options = $options;
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
     * Get name for use in HTML inputs
     *
     * @return string
     */
    public function getInputName()
    {
        return clean_url($this->getName());
    }

    /**
     * Returns whether the current field should be shown in the devices list
     *
     * @return bool
     */
    public function showInDeviceList()
    {
        return $this->getOption('showlist') ? true : false;
    }

    /**
     * Return field setup options HTML code
     *
     * @param int $index
     * @return string
     */
    public function renderOptions($index)
    {
        $name    = 'fields[' . $index . '][options][showlist]';
        $rand    = $this->getTotallyRandomString();
        $checked = $this->showInDeviceList();

        return
            '<div class="checkbox-nice">' .
                Form::checkbox($name, 1, $checked, [
                    'id' => $rand,
                ]) .
                '<label for="' . $rand . '">' .
                    'Show this field in the device listing' .
                '</label>' .
            '</div>';

    }

    /**
     * Get option
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
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

    /**
     * Get a totally random string
     *
     * @return string
     */
    public function getTotallyRandomString()
    {
        return md5(time() . mt_rand(0, 999999) . rand(0, 999999));
    }

}
