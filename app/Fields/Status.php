<?php

namespace App\Fields;

use App\Device;
use Form;

class Status extends AbstractField
{
    private $types  = ['primary', 'success', 'danger', 'info', 'warning', 'default'];

    public function getNiceOptions() {
        $options = [];

        foreach ($this->types as $type) {
            $vars = $this->getOption($type);

            if (!$vars) {
                continue;
            }

            $vars = explode(',', $vars);

            foreach ($vars as $var) {
                $options[] = [
                    'type'  => $type,
                    'label' => $var,
                ];
            }
        }

        return $options;
    }

    /**
     * Custom device list content
     *
     * @param Device $model
     * @return string
     */
    public function customDeviceListContent(Device $model)
    {
        if (isset($model->data[$this->getInputName()])) {
            $content = $model->data[$this->getInputName()];
            $options = $this->getNiceOptions();

            foreach ($options as $option) {
                if ($option['label'] == $content) {
                    return '<span class="label label-' . $option['type'] . '">' . htmlentities($option['label']) . '</span>';
                }
            }
        }

        return '-';
    }

    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        $options = $this->getNiceOptions();

        $return = $this->prerender();

        foreach ($options as $option) {
            $rand = md5(time() . mt_rand(0, 999999) . rand(0, 999999));
            $checked = $contents == $option['label'];

            $return .=
                '<div class="radio">' .
                    Form::radio($this->getInputName(), $option['label'], $checked, [
                        'id' => $rand,
                    ]) .
                    '<label for="' . $rand . '">' .
                    '<span class="label label-' . $option['type'] . '">' . htmlentities($option['label']) . '</span>' .
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
        $return = '';
        $name = function($key) use ($index) {
            return 'fields[' . $index . '][options][' . $key . ']';
        };

        foreach ($this->types as $type) {
            $return .=
                '<label><span class="label label-' . $type . '">Sample</span> Comma separated options for this color</label>' .
                Form::text($name($type), $this->getOption($type), [
                    'class' => 'form-control',
                    'placeholder' => 'e.g: Active,Suspended,Terminated',
                ]);
        }

        $return .= '<hr><label>Default preselected status(optional, must match one of the statuses above)</label>' .
            Form::text('fields[' . $index . '][options][preselected]', $this->getOption('preselected'), [
                'class' => 'form-control',
                'placeholder' => 'Active',
            ]);

        $rand = $this->getTotallyRandomString();

        $showFilter =
            '<div class="checkbox-nice">' .
            Form::checkbox($name('show_filter'), 1, $this->showFilter(), [
                'id' => $rand,
            ]) .
            '<label for="' . $rand . '">' .
            'Enable filter for this field in the device listing' .
            '</label>' .
            '</div>';

        return $return . '<hr>' . parent::renderOptions($index) . $showFilter;
    }

}
