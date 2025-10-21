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
     * Media DAIMA tenant DB'de olmalı.
     * Model central DB'de bile olsa (Setting gibi), media tenant'a ait.
     */
    public function getConnectionName()
    {
        // 1. tenant() helper çalışıyorsa tenant kullan
        if (function_exists('tenant') && tenant()) {
            return 'tenant';
        }

        // 2. Request üzerinden tenant belirle (tenant() helper çalışmazsa)
        if (request()) {
            $host = request()->getHost();
            $centralDomains = config('tenancy.central_domains', []);

            // Central domain değilse tenant DB kullan
            if (!in_array($host, $centralDomains)) {
                try {
                    $domain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
                    if ($domain && $domain->tenant_id) {
                        return 'tenant';
                    }
                } catch (\Exception $e) {
                    // Ignore and continue
                }
            }
        }

        // 3. Son çare: parent connection (gerçekten central context ise)
        return parent::getConnectionName();
    }
}
