<?php

namespace App\Presenters;

use App\DeviceSection;
use App\IpCategory;
use App\Permission;
use Laracasts\Presenter\Presenter;

class PermissionPresenter extends Presenter
{
    /**
     * @return string
     */
    public function grantType()
    {
        $results = [];

        foreach ($this->entity->grant_type as $grantType) {
            switch ($grantType) {
                case $this->entity::GRANT_TYPE_READ:
                    $results[] = 'view';
                    break;
                case $this->entity::GRANT_TYPE_EDIT:
                    $results[] = 'edit';
                    break;
                case $this->entity::GRANT_TYPE_CREATE:
                    $results[] = 'create';
                    break;
                case $this->entity::GRANT_TYPE_DELETE:
                    $results[] = 'delete';
                    break;
            }
        }

        return implode(', ', $results);
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

    public function categoryUrl()
    {
        $category = IpCategory::find($this->entity->resource_id);

        if (!$category) {
            return '';
        }

        $url = route('ip-category.edit', $category->id);

        return '<a href="' . $url . '">' . htmlentities($category->title) . '</a>';
    }

    public function grantThrough($full = false)
    {
        switch ($this->entity->resource_type) {
            case $this->entity::RESOURCE_TYPE_DEVICE:
                $identifier = 'this device';

                if ($full) {
                    $resource = $this->entity->getResource();
                    $url = route('device.show', $this->entity->resource_id);

                    if ($resource) {
                        $identifier = htmlentities($resource->present()->humanIdField);
                    }

                    $identifier = '<a href="' . $url . '">' . $identifier . '</a>';
                }

                return '<u>' . $this->grantType() . '</u> access to ' . $identifier;
            case $this->entity::RESOURCE_TYPE_DEVICE_SECTION:
                return '<u>' .
                    $this->grantType() .
                    '</u> access to section ' .
                    $this->sectionUrl();
            case $this->entity::RESOURCE_TYPE_IP_SUBNET:
                $identifier = 'this subnet';

                if ($full) {
                    $resource = $this->entity->getResource();

                    if ($resource) {
                        $url = route('subnet.edit', ['category' => $resource->category_id, 'id' => $resource->id]);

                        $identifier = isset($resource->data['name'])
                            ? $resource->data['name']
                            : $resource->subnet;

                        $identifier = '<a href="' . $url . '">' . htmlentities($identifier) . '</a>';
                    }

                }

                return '<u>' . $this->grantType() . '</u> access to ' . $identifier;
            case $this->entity::RESOURCE_TYPE_IP_CATEGORY:
                return '<u>' .
                    $this->grantType() .
                    '</u> access to category ' .
                    $this->categoryUrl();
            default:
                return '-';
        }
    }

    public function sharedWith()
    {
        $share = $this->entity;

        if ($share->user) {
            $icon = 'fa-user';
            $url = route('users.edit', $share->user_id);
            $name = $share->user->name;
        } elseif ($share->group) {
            $icon = 'fa-users';
            $url = route('groups.edit', $share->group_id);
            $name = $share->group->name;
        } else {
            return '-';
        }

        return '<i class="fa ' . $icon . '"></i> <a href="' . $url . '">' . $name . '</a>';
    }
}
