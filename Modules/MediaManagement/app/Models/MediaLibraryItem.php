<?php

namespace Modules\MediaManagement\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Modules\MediaManagement\App\Services\MediaService;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * MediaLibraryItem
 *
 * Global media library host model. Stores metadata about standalone uploads
 * and keeps a single media attachment in the `library` collection.
 */
class MediaLibraryItem extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * @var string
     */
    protected $table = 'media_library_items';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'media_id',
        'created_by',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Register library collection that accepts all configured media types.
     */
    public function registerMediaCollections(): void
    {
        $collection = $this->addMediaCollection('library')
            ->singleFile();

        $mimeTypes = collect(config('mediamanagement.media_types', []))
            ->pluck('mime_types')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($mimeTypes)) {
            $collection->acceptsMimeTypes($mimeTypes);
        }
    }

    /**
     * Register conversions for image uploads only.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Skip conversions for non-image uploads
        if ($media && !str_starts_with($media->mime_type, 'image/')) {
            return;
        }

        foreach (config('mediamanagement.conversions', []) as $name => $settings) {
            $conversion = $this->addMediaConversion($name)
                ->performOnCollections('library');

            if (isset($settings['width'])) {
                $conversion->width($settings['width']);
            }

            if (isset($settings['height'])) {
                $conversion->height($settings['height']);
            }

            if (isset($settings['format'])) {
                $conversion->format($settings['format']);
            }

            if (isset($settings['quality'])) {
                $conversion->quality($settings['quality']);
            }

            if (!empty($settings['responsive'])) {
                $conversion->withResponsiveImages();
            }

            if (array_key_exists('queued', $settings) && $settings['queued'] === false) {
                $conversion->nonQueued();
            } else {
                $conversion->queued();
            }
        }
    }

    /**
     * Linked media record helper.
     */
    public function libraryMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'library');
    }

    /**
     * Determine media type based on attached media record.
     */
    public function detectType(MediaService $mediaService): ?string
    {
        $media = $this->libraryMedia;
        if (!$media) {
            return null;
        }

        return $mediaService->getMediaTypeFromMime($media->mime_type ?? '');
    }
}
