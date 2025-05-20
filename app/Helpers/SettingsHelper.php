<?php

if (!function_exists('settings')) {
    function settings($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('settings');
        }

        try {
            // Tenant bağlantısını zorlayalım
            if (function_exists('is_tenant') && is_tenant()) {
                // Tenant veritabanında çalıştığımızdan emin olalım
                config(['database.connections.tenant.driver' => 'mysql']);
                \Illuminate\Support\Facades\DB::purge('tenant');
            }
            
            return app('settings')->get($key, $default);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Settings helper error: " . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('settings_id')) {
    function settings_id($id = null, $default = null)
    {
        if (is_null($id)) {
            return app('settings');
        }

        try {
            // Tenant bağlantısını zorlayalım
            if (function_exists('is_tenant') && is_tenant()) {
                // Tenant veritabanında çalıştığımızdan emin olalım
                config(['database.connections.tenant.driver' => 'mysql']);
                \Illuminate\Support\Facades\DB::purge('tenant');
            }
            
            return app('settings')->getById($id, $default);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Settings ID helper error: " . $e->getMessage());
            return $default;
        }
    }
}