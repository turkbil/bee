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
            // Slug-friendly dosya adı oluştur (Türkçe karakter desteği)
            $originalName = $file->getClientOriginalName();
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $sluggedBaseName = \Illuminate\Support\Str::slug($baseName);
            $sluggedFileName = $sluggedBaseName . '.' . $extension;

            // Livewire TemporaryUploadedFile için özel işlem
            if (class_exists('Livewire\Features\SupportFileUploads\TemporaryUploadedFile')
                && $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {

                // Livewire temp dosyasını geçici bir yere kopyala
                $tempPath = sys_get_temp_dir() . '/' . uniqid('media_') . '_' . $sluggedFileName;
                copy($file->getRealPath(), $tempPath);

                $media = $model->addMedia($tempPath)
                    ->usingName($sluggedBaseName)
                    ->usingFileName($sluggedFileName)
                    ->toMediaCollection($collectionName);

                // Geçici dosyayı temizle
                @unlink($tempPath);

            } else {
                // Normal file upload
                $filePath = method_exists($file, 'getRealPath') ? $file->getRealPath() : $file->path();

                $media = $model->addMedia($filePath)
                    ->usingName($sluggedBaseName)
                    ->usingFileName($sluggedFileName)
                    ->toMediaCollection($collectionName);
            }

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
            // Eğer media başarıyla oluşturulduysa (ID varsa) hatayı logla ama throw etme
            // Çünkü Spatie bazen file_size alamasa bile upload'u başarıyla yapabiliyor
            if (isset($media) && $media && $media->id) {
                Log::warning('Media uploaded but with warnings', [
                    'media_id' => $media->id,
                    'warning' => $e->getMessage()
                ]);
                return $media;
            }

            Log::error('Media upload failed', [
                'model' => get_class($model),
                'collection' => $collectionName,
                'error' => $e->getMessage(),
                'file_class' => get_class($file)
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
            $updates = [];

            foreach ($items as $item) {
                $media = Media::find($item['id']);

                if ($media && $media->model_id == $model->id) {
                    $oldOrder = $media->order_column;
                    $media->order_column = $item['order'];
                    $media->save();

                    $updates[] = [
                        'id' => $media->id,
                        'name' => $media->file_name,
                        'old_order' => $oldOrder,
                        'new_order' => $item['order']
                    ];
                }
            }

            Log::info('🔄 Gallery order updated', [
                'model' => get_class($model),
                'model_id' => $model->id,
                'items_count' => count($items),
                'updates' => $updates
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
