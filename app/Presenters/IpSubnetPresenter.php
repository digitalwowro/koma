<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class IpSubnetPresenter extends Presenter
{
    public function countIps($humanReadable = false)
    {
        $subnet = $this->entity->subnet;

        if (!$subnet) {
            return '-';
        }

        list($ip, $mask) = explode('/', $subnet);

        $count = pow(2, (32 - $mask));

        return $humanReadable
            ? number_format($count, 0, ',', '.')
            : $count;
    }

    public function freeIps() : string
    {
        $count = $this->countIps();

        if (!is_numeric($count)) {
            return '- / -';
        }

        $assigned = count((array) ($this->entity->data['assigned'] ?? []));

        return number_format($count - $assigned, 0, ',', '.') . ' / ' . number_format($count, 0, ',', '.');
    }
}
