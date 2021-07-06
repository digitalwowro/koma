<?php

namespace App\Fields;

use App\Item;
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
     * @param Item $model
     * @return string
     */
    public function customDeviceListContent(Item $model)
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
            $checked = $contents == $option['label'];

            $return .=
                '<div class="radio icheck">' .
                    '<label>' .
                        Form::radio($this->getInputName(), $option['label'], $checked) .
                        '<span class="label label-' . $option['type'] . '" style="margin-left: 10px;">' . htmlentities($option['label']) . '</span>' .
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

        $return .= '<br><label>Default preselected status(optional, must match one of the statuses above)</label>' .
            Form::text('fields[' . $index . '][options][preselected]', $this->getOption('preselected'), [
                'class' => 'form-control',
                'placeholder' => 'Active',
            ]);

        $showFilter =
            '<div class="radio icheck">' .
                '<label>' .
                    Form::checkbox($name('show_filter'), 1, $this->showFilter()) .
                    ' Enable filter for this field in the device listing' .
                '</label>' .
            '</div>';

        return $return . '<br>' . parent::renderOptions($index) . $showFilter;
    }

}
