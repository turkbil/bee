<?php

namespace App\Services\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class TenantPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        // Path: {media_id}/
        // Tenant isolation symlink seviyesinde yapılıyor (public/storage/tenant{id})
        return $media->id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        // Conversion path: {media_id}/conversions/
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        // Responsive images path: {media_id}/responsive-images/
        return $this->getPath($media) . 'responsive-images/';
    }

    protected function getTenantId(): int
    {
        try {
            // Tenant context'inden ID al
            if (app()->bound(\Stancl\Tenancy\Tenancy::class)) {
                $tenancy = app(\Stancl\Tenancy\Tenancy::class);

                if ($tenancy->initialized) {
                    return tenant('id');
                }
            }

            // Central context - default tenant 1
            return 1;
        } catch (\Exception $e) {
            // Fallback
            return 1;
        }
    }
}
