<?php

/**
 * Laravel 12 Compatibility Helpers
 *
 * Laravel 12'de kaldırılan helper fonksiyonlar
 */

if (!function_exists('array_last')) {
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    function array_last($array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? value($default) : end($array);
        }

        return Illuminate\Support\Arr::last($array, $callback, $default);
    }
}
