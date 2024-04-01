<?php

if (!function_exists('issetGetter')) {
    /**
     * Get the attribute from array if is set.
     *
     * @param array $data
     * @param string $attribute
     * @return mixed
     */
    function issetGetter(array $data, string $attribute): mixed
    {
        return isset($data[$attribute]) ? $data[$attribute] : null;
    }
}
