<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class DeviceSectionPresenter extends Presenter
{
    /**
     * @return string
     */
    public function icon()
    {
        return '<i class="fa fa-' . $this->entity->icon . '"></i>';
    }

    public function idPrefix()
    {
        foreach ($this->entity->fields as $field) {
            if ($field->getType() === 'ID') {
                $prefix = $field->getOption('prefix');

                if ($prefix) {
                    return $prefix;
                }
            }
        }

        return '';
    }

    private function treeRecursive($isActive, $parent = '#')
    {
        $output = '';

        foreach ($this->entity->categories as $category) {
            if (!isset($category['id'], $category['text'], $category['parent']) || $category['parent'] !== $parent) {
                continue;
            }

            $subCategories = $this->treeRecursive($isActive, $category['id']);
            $subHasActive = strpos($subCategories, 'class="active') !== false;

            $classNames = [
                'active' => $subHasActive || ($isActive && request()->route()->parameter('category') === $category['id']),
                'treeview' => !empty($subCategories),
            ];

            $output .= '<li class="' . join_class_names($classNames) . '">';
                $output .= '<a href="' . route('device.index', [$this->entity->id, $category['id']]) . '">';
                    $output .= '<i class="fa fa-circle-o"></i>';
                    $output .= $category['text'];

                    if ($classNames['treeview']) {
                        $output .= '<span class="pull-right-container">';
                            $output .= '<i class="fa fa-angle-left pull-right"></i>';
                        $output .= '</span>';
                    }
                $output .= '</a>';

            if ($classNames['treeview']) {
                $output .= '<ul class="treeview-menu">';
                    $output .= $subCategories;
                $output .= '</ul>';
            }

            $output .= '</li>';
        }

        return $output;
    }

    public function treeviewMenu($isActive)
    {
        $output = '<ul class="treeview-menu">';
            $output .= $this->treeRecursive($isActive);

            $output .= '<li class="' . ($isActive && !request()->route()->parameter('category') ? 'active' : '') . '">';
                $output .= '<a href="' . route('device.index', $this->entity->id) . '">';
                    $output .= '<i class="fa fa-circle"></i>';
                    $output .= 'show all';
                $output .= '</a>';
            $output .= '</li>';
        $output .= '</ul>';

        return $output;
    }

    private function selectorRecursive($parent = '#', $level = 0) : array
    {
        $output = [];
        $indent = str_repeat('--', $level);
        $indent = $indent ? "{$indent} " : '';

        foreach ($this->entity->categories as $category) {
            if (!isset($category['id'], $category['text'], $category['parent']) || $category['parent'] !== $parent) {
                continue;
            }

            $output[$category['id']] = $indent . $category['text'];
            $output += $this->selectorRecursive($category['id'], $level + 1);
        }

        return $output;
    }

    public function categorySelector() : array
    {
        return ['' => '- uncategorized -'] + $this->selectorRecursive();
    }
}
