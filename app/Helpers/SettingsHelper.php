<?php

if (!function_exists('settings')) {
    function settings($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        return app('settings')->get($key, $default);
    }
}

if (!function_exists('settings_id')) {
    function settings_id($id = null, $default = null)
    {
        if (is_null($id)) {
            return app('settings');
        }

        return app('settings')->getById($id, $default);
    }
}