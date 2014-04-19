<?php

/**
 * @param array $array
 * @param string $key
 * @param mixed $default
 *
 * @return mixed
 */
function array_get (array $array, $key, $default = null) {
    if (isset($array[$key])) {
        return $array[$key];
    }
    return $default;
}