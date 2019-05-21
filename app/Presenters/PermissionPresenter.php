<?php

namespace App\Presenters;

use App\DeviceSection;
use App\Permission;
use Laracasts\Presenter\Presenter;

class PermissionPresenter extends Presenter
{
    public $verbs = [
        Permission::GRANT_TYPE_READ => [
            Permission::RESOURCE_TYPE_DEVICES_DEVICE => 'View',
            'default' => 'View all',
        ],
        Permission::GRANT_TYPE_WRITE => [
            Permission::RESOURCE_TYPE_DEVICES_DEVICE => 'View & Edit',
            Permission::RESOURCE_TYPE_IP_CATEGORY => 'View & Assign',
            Permission::RESOURCE_TYPE_IP_SUBNET => 'View & Assign',
            'default' => 'View & Edit all',
        ],
        Permission::GRANT_TYPE_FULL => [
            Permission::RESOURCE_TYPE_DEVICES_DEVICE => 'View, Edit & Delete',
            Permission::RESOURCE_TYPE_IP_CATEGORY => 'View, Assign & Delete',
            Permission::RESOURCE_TYPE_IP_SUBNET => 'View, Assign & Delete',
            'default' => 'View, Edit & Delete all',
        ],
        Permission::GRANT_TYPE_CREATE => [
            'default' => 'Create',
        ],
        Permission::GRANT_TYPE_READ_CREATE => [
            'default' => 'View all & Create',
        ],
        Permission::GRANT_TYPE_OWNER => [
            'default' => 'Owner',
        ],
    ];

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
            case $this->entity::GRANT_TYPE_FULL:
                return 'view, edit & delete';
            case $this->entity::GRANT_TYPE_CREATE:
                return 'create';
            case $this->entity::GRANT_TYPE_READ_CREATE:
                return 'view & create';
            case $this->entity::GRANT_TYPE_OWNER:
                return 'owner';
        }
    }

    public function sectionUrl()
    {
        $section = DeviceSection::find($this->entity->resource_id);

        if (!$section) {
            return '';
        }

        $url = route('device-sections.edit', $section->id);

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

    public function actionVerb($action)
    {
        $resourceType = $this->entity->resource_type;

        return isset($this->verbs[$action][$resourceType])
            ? $this->verbs[$action][$resourceType]
            : (isset($this->verbs[$action]['default']) ? $this->verbs[$action]['default'] : '');
    }

}
