<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

/**
 * Custom Media Model - Tenant-Aware Connection
 *
 * Spatie Media Library kullanır ama connection'ı tenant context'e göre belirler.
 * Setting model CentralConnection kullanır ama media'sı tenant DB'ye gitmeli.
 */
class CustomMedia extends BaseMedia
{
    /**
     * Get the current connection name for the model.
     *
     * Media her zaman tenant context'inde çalışmalı.
     * Setting model central DB'de olsa bile, media tenant DB'ye kayıt olmalı.
     */
    public function getConnectionName()
    {
        // Tenant context varsa tenant DB kullan
        if (function_exists('tenant') && tenant()) {
            return 'tenant';
        }

        // Central context için default connection
        return parent::getConnectionName();
    }
}
