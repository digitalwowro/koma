<?php

namespace App\Fields;

use App\Device;
use App\IpAddress;
use Form;

class IP extends AbstractField
{
    /**
     * {@inheritDoc}
     */
    public function render($contents = '')
    {
        return false;
    }

    /**
     * Custom device list content
     *
     * @param Device $model
     * @return string
     */
    public function customDeviceListContent(Device $model)
    {
        $subnets = $this->getOption('subnets', []);
        $showCustom = in_array('c', $subnets);

        $return = $model->ips->filter(function ($item) use ($subnets, $showCustom) {
            return in_array($item->subnet, $subnets) || ($showCustom && is_null($item->subnet));
        })->pluck('ip')->implode(', ');

        if ($return && $this->copyPaste()) {
            $return .= ' <a href="#" class="copy-this" data-clipboard-text="' . htmlentities($return) . '"><i class="fa fa-copy" title="Copy to Clipboard"></i></a>';
        }

        return $return;
    }

    /**
     * Returns whether the current field should be shown in the devices list
     *
     * @return bool
     */
    public function showInDeviceList()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function renderOptions($index)
    {
        $rand    = $this->getTotallyRandomString();
        $value   = $this->getOption('subnets', []);
        $name    = 'fields[' . $index . '][options][subnets][]';
        $subnets = IpAddress::getSubnetsFor();
        $output  = '<label>Select one or more subnets to show here</label><br>';
        $output .= '<select id="' . $rand . '" style="width:100%; max-width:300px;" name="' . $name . '"" multiple>';
        $value   = is_array($value) ? $value : (array)$value;

        foreach ($subnets as $subnet) {
            $selected = in_array($subnet->subnet, $value) ? ' selected' : '';
            $output .= '<option value="' . htmlentities($subnet->subnet) . '"'. $selected .'>' . htmlentities($subnet->category->title) . ':' . htmlentities($subnet->subnet) . '</option>';
        }

        $custom  = in_array('c', $value) ? ' selected' : '';
        $output .= '<option value="c"' . $custom . '>Custom IPs</option>';
        $output .= '</select>';
        $output .= "<script>setTimeout(function(){ $('#{$rand}').select2(); }, 200)</script>";
        $output .= '<hr>';

        $checked = filter_var($this->getOption('copypaste', ''), FILTER_VALIDATE_BOOLEAN);
        $rand    = $this->getTotallyRandomString();
        $name    = 'fields[' . $index . '][options][copypaste]';

        $output .= '<div class="checkbox-nice">' .
            Form::checkbox($name, 1, $checked, [
                'id' => $rand,
            ]) .
        '<label for="' . $rand . '">' .
        'Show copy text button for this field' .
        '</label>' .
        '</div>';

        return $output;
    }

}
