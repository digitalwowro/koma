<?php

use App\EncryptableModel;
use App\Item;
use App\Category;
use App\Exceptions\InvalidResourceException;
use App\IpSubnet;
use App\IpCategory;
use App\Permission;

/**
 * Check if a the current route is the one given
 *
 * @param mixed $name
 * @param boolean $wrapped
 * @param array $extraParams
 * @return bool
 */
function is_route_bool($name, array $extraParams = []) {
    if (!empty($extraParams)) {
        foreach ($extraParams as $extraParam => $value) {
            $data = Route::getCurrentRoute()->parameter($extraParam);

            if ($data != $value) {
                return false;
            }
        }
    }

    if (!is_array($name)) {
        return Route::is($name);
    }

    foreach($name as $route) {
        if (Route::is($route)) {
            return true;
        }
    }

    return false;
}

/**
 * Check if a the current route is the one given
 *
 * @param mixed $name
 * @param boolean $wrapped
 * @param array $extraParams
 * @return string
 */
function is_route($name, $wrapped = true, array $extraParams = []) {
    if (!is_route_bool($name, $extraParams)) {
        return '';
    }

    return $wrapped ? 'class="active"' : 'active';
}

/**
 * Get the gravatar for a particular email
 *
 * @param string $email
 * @param int    $size
 * @param string $default
 * @param string $rating
 * @return string
 */
function gravatar($email, $size = 24, $default = 'mp', $rating = 'pg') {
    return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . "?s={$size}&d={$default}&r={$rating}";
}

/**
 * Convert UTF8 string to ASCII using transliteration
 *
 * @param string $s
 * @return string
 */
function utf2ascii($s) {
    return iconv("UTF-8", "ISO-8859-1//TRANSLIT", $s);
}

/**
 * Sanitize string for use as url
 *
 * @param string $s
 * @param string $placeholder
 * @return string
 */
function clean_url($s, $placeholder = '-') {
    $s = utf2ascii($s);
    $s = preg_replace('/[^a-z0-9' . $placeholder . ']/', '', strtolower(str_replace(' ', $placeholder, $s)));
    $s = str_replace($placeholder . $placeholder, $placeholder, $s);
    $s = trim($s, $placeholder);

    return $s;
}

/**
 * Autolink URLs
 *
 * @param string $s
 * @return string
 */
function urlify($s) {
    $s = autolink($s, 128, ' target="_blank"');

    return $s ?: '-';
}

/**
 * @param EncryptableModel $resource
 * @return int
 */
function getResourceType(EncryptableModel $resource)
{
    if ($resource instanceof Item) {
        return Permission::RESOURCE_TYPE_ITEM;
    } elseif ($resource instanceof IpSubnet) {
        return Permission::RESOURCE_TYPE_IP_SUBNET;
    } elseif ($resource instanceof Category) {
        return Permission::RESOURCE_TYPE_CATEGORY;
    } elseif ($resource instanceof IpCategory) {
        return Permission::RESOURCE_TYPE_IP_CATEGORY;
    } else {
        throw new InvalidResourceException;
    }
}

function xss_safe_newline_to_br($string) : string
{
    $string = str_replace("\r\n", "\n", $string);
    $parts = explode("\n", $string);

    foreach ($parts as &$part) {
        $part = htmlspecialchars($part);
    }

    return implode('<br>', $parts);
}

function join_class_names(array $classNames) : string
{
    return implode(' ', array_keys(array_filter($classNames)));
}
