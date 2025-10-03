<?php

namespace Modules\MediaManagement\App\Services;

use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;

/**
 * MediaService
 *
 * Media yönetimi için business logic
 * Upload, Delete, Sort, Set Featured işlemleri
 */
class MediaService
{
    /**
     * Get allowed MIME types for a media type
     */
    public function getAllowedMimeTypes(string $type): array
    {
        return config("mediamanagement.media_types.{$type}.mime_types", []);
    }

    /**
     * Get allowed extensions for a media type
     */
    public function getAllowedExtensions(string $type): array
    {
        return config("mediamanagement.media_types.{$type}.extensions", []);
    }

    /**
     * Get max file size for a media type (in KB)
     */
    public function getMaxFileSize(string $type): int
    {
        return config("mediamanagement.media_types.{$type}.max_size", 10240);
    }

    /**
     * Get enabled media types
     */
    public function getEnabledMediaTypes(): array
    {
        $types = config('mediamanagement.media_types', []);
        return array_filter($types, fn($type) => $type['enabled'] ?? false);
    }

    /**
     * Get collection config from module or defaults
     */
    public function getCollectionConfig(string $moduleName, string $collectionName): ?array
    {
        // Try module config first
        $config = config("{$moduleName}.media.collections.{$collectionName}");
        if ($config) {
            return $config;
        }

        // Try default templates
        return config("mediamanagement.collection_templates.{$collectionName}");
    }

    /**
     * Upload media to collection
     */
    public function uploadMedia(HasMedia $model, $file, string $collectionName): ?Media
    {
        try {
            $media = $model->addMedia($file->getRealPath())
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection($collectionName);

            Log::info('📤 Media uploaded', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'collection' => $collectionName,
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            return $media;

        } catch (\Exception $e) {
            Log::error('Media upload failed', [
                'model' => get_class($model),
                'collection' => $collectionName,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Upload multiple media files
     */
    public function uploadMultipleMedia(HasMedia $model, array $files, string $collectionName): array
    {
        $uploaded = [];

        foreach ($files as $file) {
            try {
                $media = $this->uploadMedia($model, $file, $collectionName);
                $uploaded[] = $media;
            } catch (\Exception $e) {
                Log::warning('Single file upload failed in batch', [
                    'filename' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $uploaded;
    }

    /**
     * Delete media by ID with security check
     */
    public function deleteMedia(int $mediaId, HasMedia $model): bool
    {
        try {
            $media = Media::findOrFail($mediaId);

            // Security: Check ownership
            if ($media->model_id != $model->id || $media->model_type != get_class($model)) {
                Log::warning('Unauthorized media delete attempt', [
                    'media_id' => $mediaId,
                    'model_id' => $model->id,
                    'actual_owner' => $media->model_id
                ]);
                return false;
            }

            $media->delete();

            Log::info('🗑️ Media deleted', [
                'media_id' => $mediaId,
                'model' => get_class($model),
                'model_id' => $model->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Media delete failed', [
                'media_id' => $mediaId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Set featured image from gallery
     */
    public function setFeaturedFromGallery(HasMedia $model, int $mediaId): bool
    {
        try {
            $media = Media::findOrFail($mediaId);

            // Security: Check ownership and collection
            if ($media->model_id != $model->id
                || $media->model_type != get_class($model)
                || $media->collection_name != 'gallery') {
                Log::warning('Unauthorized set featured attempt', [
                    'media_id' => $mediaId,
                    'model_id' => $model->id
                ]);
                return false;
            }

            // Clear existing featured image
            $model->clearMediaCollection('featured_image');

            // Copy gallery image to featured_image
            $model->copyMedia($media->getPath())
                ->toMediaCollection('featured_image');

            Log::info('📸 Featured image set from gallery', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'source_media_id' => $mediaId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Set featured from gallery failed', [
                'media_id' => $mediaId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Update gallery order
     */
    public function updateGalleryOrder(HasMedia $model, array $items): bool
    {
        try {
            foreach ($items as $item) {
                $media = Media::find($item['id']);

                if ($media && $media->model_id == $model->id) {
                    $media->order_column = $item['order'];
                    $media->save();
                }
            }

            Log::info('🔄 Gallery order updated', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'items_count' => count($items)
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Gallery order update failed', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get media type from MIME type
     */
    public function getMediaTypeFromMime(string $mimeType): string
    {
        $types = config('mediamanagement.media_types', []);

        foreach ($types as $typeName => $typeConfig) {
            if (in_array($mimeType, $typeConfig['mime_types'] ?? [])) {
                return $typeName;
            }
        }

        return 'document'; // Default fallback
    }

    /**
     * Validate file against media type rules
     */
    public function validateFile($file, string $type): array
    {
        $errors = [];

        // Check MIME type
        $allowedMimes = $this->getAllowedMimeTypes($type);
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = "Invalid file type. Allowed: " . implode(', ', $this->getAllowedExtensions($type));
        }

        // Check file size
        $maxSize = $this->getMaxFileSize($type) * 1024; // Convert to bytes
        if ($file->getSize() > $maxSize) {
            $errors[] = "File too large. Max size: " . ($this->getMaxFileSize($type) / 1024) . "MB";
        }

        return $errors;
    }

    /**
     * Get collection statistics
     */
    public function getCollectionStats(HasMedia $model, string $collectionName): array
    {
        $media = $model->getMedia($collectionName);

        return [
            'count' => $media->count(),
            'total_size' => $media->sum('size'),
            'types' => $media->pluck('mime_type')->unique()->values()->toArray(),
        ];
    }
}
