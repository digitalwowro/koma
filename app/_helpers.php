<?php

/**
 * Check if a the current route is the one given
 *
 * @param mixed   $name
 * @param boolean $wrapped
 * @return string
 */
function is_route($name, $wrapped = true)
{
    $return = $wrapped ? 'class="active"' : 'active';

    if ( ! is_array($name)) return Route::is($name) ? $return : '';

    foreach($name as $route)
    {
        if (Route::is($route)) return $return;
    }

    return '';
}

/**
 * Get the gravatar for a particular email
 *
 * @param  string  $email
 * @param  int $size
 * @return string
 */
function gravatar($email, $size = 24)
{
    return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=' . $size . '&d=mm&r=r';
}
