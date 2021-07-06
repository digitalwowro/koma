<?php

namespace App\Fields;

use App\Item;
use App\IpSubnet;
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
     * @param Item $model
     * @return string
     */
    public function customDeviceListContent(Item $model)
    {
        $filterEnabled = filter_var($this->getOption('subnetsenable', ''), FILTER_VALIDATE_BOOLEAN);
        $subnets = $this->getOption('subnets', []);
        $showCustom = in_array('c', $subnets);

        $ips = $return = $model->ips();;
        $results = [];

        foreach ($ips as $ip) {
            if (!$filterEnabled) {
                $results[] = $ip['ip'];
            } elseif ($showCustom && isset($ip['custom']) && $ip['custom'] === true) {
                $results[] = $ip['ip'];
            } elseif (isset($ip['subnet']) && in_array($ip['subnet']->id, $subnets)) {
                $results[] = $ip['ip'];
            }
        }

        $return = implode(', ', $results);

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
        $value = $this->getOption('subnets', []);
        $subnets = IpSubnet::all();
        $value = is_array($value) ? $value : (array) $value;

        $rand = $this->getTotallyRandomString();
        $name = 'fields[' . $index . '][options][subnetsenable]';
        $checked = filter_var($this->getOption('subnetsenable', ''), FILTER_VALIDATE_BOOLEAN);
        $output = '<div class="checkbox icheck se-' . $rand . '">' .
            '<label>' .
                Form::checkbox($name, 1, $checked) .
                ' Filter IPs by subnet' .
            '</label>' .
        '</div>';

        $name = 'fields[' . $index . '][options][subnets][]';
        $output .= '<div class="subnetsenable-' . $rand . '"><label>Select one or more subnets to show</label><br>';
        $output .= '<select id="' . $rand . '" style="width:100%; max-width:300px;" name="' . $name . '"" multiple>';

        foreach ($subnets as $subnet) {
            if (!$subnet->subnet) {
                continue;
            }

            $selected = in_array($subnet->id, $value) ? ' selected' : '';
            $output .= '<option value="' . $subnet->id . '"'. $selected . '>' . htmlentities($subnet->category->title) . ': ' . htmlentities($subnet->data['name'] ?? $subnet->subnet) . '</option>';
        }

        $custom  = in_array('c', $value) ? ' selected' : '';
        $output .= '<option value="c"' . $custom . '>Custom IPs</option>';
        $output .= '</select><br></div>';
        $output .= "<script>
            setTimeout(function(){
                $('#{$rand}').select2();
                $('.se-{$rand} input').on('ifToggled', function() {
                    $('.subnetsenable-{$rand}').toggle($(this).is(':checked'));
                });
                $('.subnetsenable-{$rand}').toggle($('.se-{$rand} input').is(':checked'));
            }, 200)</script>";

        $checked = filter_var($this->getOption('copypaste', ''), FILTER_VALIDATE_BOOLEAN);
        $name = 'fields[' . $index . '][options][copypaste]';

        $output .= '<div class="checkbox icheck">' .
            '<label>' .
                Form::checkbox($name, 1, $checked) .
                ' Show copy text button for this field' .
            '</label>' .
        '</div>';

        return $output;
    }

}
