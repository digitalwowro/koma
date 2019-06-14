<?php

namespace App\Presenters;

use App\DeviceSection;
use App\Permission;
use Laracasts\Presenter\Presenter;

class PermissionPresenter extends Presenter
{
    /**
     * @return string
     */
    public function grantType()
    {
        switch ($this->entity->grant_type) {
            case $this->entity::GRANT_TYPE_NONE:
                return 'none';
            case $this->entity::GRANT_TYPE_READ:
                return 'view';
            case $this->entity::GRANT_TYPE_WRITE:
                return 'view & edit';
            case $this->entity::GRANT_TYPE_CREATE:
                return 'create';
        }
    }

    public function sectionUrl()
    {
        $section = DeviceSection::find($this->entity->resource_id);

        if (!$section) {
            return '';
        }

        $url = route('device-section.edit', $section->id);

        return '<a href="' . $url . '">' . htmlentities($section->title) . '</a>';
    }

    public function grantThrough()
    {
        switch ($this->entity->resource_type) {
            case $this->entity::RESOURCE_TYPE_DEVICES_DEVICE:
                return '<u>' . $this->grantType() . '</u> access to this device';
            case $this->entity::RESOURCE_TYPE_DEVICES_SECTION:
                return '<u>' .
                    $this->grantType() .
                    '</u> access to section ' .
                    $this->sectionUrl();
            case $this->entity::RESOURCE_TYPE_DEVICES_FULL:
                return 'full <u>' . $this->grantType() . '</u> access';
            default:
                return '-';
        }
    }
}
