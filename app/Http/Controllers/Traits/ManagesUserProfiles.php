<?php

namespace App\Http\Controllers\Traits;

use App\DeviceSection;
use Illuminate\Http\Request;

trait ManagesUserProfiles
{
    protected function profileSettings(Request $request, array $current = [])
    {
        unset($current['device_sections']);

        if (filter_var($request->input('device_sections_customize'), FILTER_VALIDATE_BOOLEAN)) {
            $validSections = DeviceSection::pluck('id')->toArray();
            $sanitized = [];
            $inputSections = $request->input('device_sections');

            if (is_array($inputSections)) {
                foreach ($inputSections as $section) {
                    if (in_array($section, $validSections)) {
                        $sanitized[] = $section;
                    }
                }
            }

            $current['device_sections'] = $sanitized;
        }

        if (in_array($request->input('devices_per_page'), [10, 25, 50, 100])) {
            $current['devices_per_page'] = $request->input('devices_per_page');
        }

        return $current;
    }
}
