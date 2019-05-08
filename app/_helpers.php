<?php

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
 * @param  string  $email
 * @param  int $size
 * @return string
 */
function gravatar($email, $size = 24) {
    return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=' . $size . '&d=mm&r=r';
}

/**
 * Encrypt a string
 *
 * @param $str
 * @param null $key
 * @return string
 */
function dsEncrypt($str, $key = null) {
    if (is_null($key)) {
        $key = request()->cookie('key');
    }

    $key = md5($key . config('app.key'));
    $cipher = config('app.cipher');

    if (!isset($GLOBALS['encrypters'])) {
        $GLOBALS['encrypters'] = [];
    }

    if (!isset($GLOBALS['encrypters'][$key])) {
        $GLOBALS['encrypters'][$key] = new \Illuminate\Encryption\Encrypter($key, $cipher);
    }

    return base64_encode($GLOBALS['encrypters'][$key]->encrypt($str));
}

/**
 * Decrypt a string
 *
 * @param $str
 * @param null $key
 * @return string
 */
function dsDecrypt($str, $key = null)
{
    if (is_null($key)) {
        $key = request()->cookie('key');
    }

    $key = md5($key . config('app.key'));
    $cipher = config('app.cipher');

    if (!isset($GLOBALS['encrypters'])) {
        $GLOBALS['encrypters'] = [];
    }

    if (!isset($GLOBALS['encrypters'][$key])) {
        $GLOBALS['encrypters'][$key] = new \Illuminate\Encryption\Encrypter($key, $cipher);
    }

    return $GLOBALS['encrypters'][$key]->decrypt(base64_decode($str));
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
