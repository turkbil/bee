<?php

namespace Modules\MediaManagement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Modules\MediaManagement\App\Services\MediaService;

/**
 * Universal Media Component
 *
 * Tüm modüllerde kullanılabilir evrensel medya yönetimi component'i
 * Image, Video, Audio, Document, Archive desteği
 *
 * Usage:
 * <livewire:mediamanagement::universal-media
 *     :model-id="$announcementId"
 *     model-type="announcement"
 *     model-class="Modules\Announcement\App\Models\Announcement"
 *     :collections="['featured_image', 'gallery']"
 * />
 */
class UniversalMediaComponent extends Component
{
    use WithFileUploads;

    // ========================================
    // REQUIRED PROPS
    // ========================================
    public ?int $modelId = null;
    public string $modelType;
    public string $modelClass;

    // ========================================
    // CONFIGURATION PROPS
    // ========================================
    public array $collections = ['featured_image', 'gallery'];
    public ?int $maxGalleryItems = null;
    public bool $sortable = true;
    public bool $setFeaturedFromGallery = true;

    // ========================================
    // FILE UPLOAD PROPERTIES
    // ========================================
    public $featuredImageFile = null;
    public $galleryFiles = [];
    public $videoFiles = [];
    public $audioFiles = [];
    public $documentFiles = [];

    // ========================================
    // EXISTING MEDIA
    // ========================================
    public array $existingFeaturedImage = [];
    public array $existingGallery = [];
    public array $existingVideos = [];
    public array $existingAudio = [];
    public array $existingDocuments = [];

    // ========================================
    // SESSION-BASED TEMPORARY STORAGE
    // ========================================
    public ?array $tempFeaturedImage = null; // ['path' => 'temp/xxx.jpg', 'original_name' => 'photo.jpg']
    public array $tempGallery = []; // [['path' => 'temp/xxx.jpg', 'original_name' => 'photo.jpg'], ...]

    // ========================================
    // SERVICES
    // ========================================
    protected MediaService $mediaService;

    // ========================================
    // LISTENERS
    // ========================================
    protected $listeners = [
        'announcement-saved' => 'handleModelSaved',
        'page-saved' => 'handleModelSaved',
        'portfolio-saved' => 'handleModelSaved',
        'update-gallery-order' => 'updateGalleryOrderFromEvent',
        'update-temp-gallery-order' => 'updateTempGalleryOrderFromEvent',
        'save-media-caption' => 'saveMediaCaption',
    ];

    // ========================================
    // LIFECYCLE
    // ========================================

    public function boot()
    {
        $this->mediaService = app(MediaService::class);
    }

    public function mount()
    {
        // Validations
        if (!$this->modelType || !$this->modelClass) {
            throw new \Exception('modelType and modelClass are required');
        }

        // Load existing media
        if ($this->modelId) {
            $this->loadExistingMedia();
        }

        // Set defaults from config
        if (is_null($this->maxGalleryItems)) {
            $this->maxGalleryItems = config('mediamanagement.defaults.max_gallery_items', 50);
        }

        // Load temp files from session (for new records)
        if (!$this->modelId) {
            $this->loadTempFilesFromSession();
        }
    }

    // ========================================
    // LOAD EXISTING MEDIA
    // ========================================

    protected function loadExistingMedia()
    {
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        foreach ($this->collections as $collectionName) {
            $this->loadCollection($model, $collectionName);
        }
    }

    protected function loadCollection($model, string $collectionName)
    {
        if (!$model->hasMedia($collectionName)) {
            return;
        }

        $media = $model->getMedia($collectionName)
            ->sortBy('order_column')
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'url' => $item->getUrl(),
                    'thumb' => $item->hasGeneratedConversion('thumb') ? $item->getUrl('thumb') : $item->getUrl(),
                    'name' => $item->name,
                    'file_name' => $item->file_name,
                    'size' => $item->human_readable_size,
                    'mime_type' => $item->mime_type,
                    'type' => $this->mediaService->getMediaTypeFromMime($item->mime_type),
                    'order' => $item->order_column,
                    'custom_properties' => $item->custom_properties ?? [],
                ];
            })
            ->values()
            ->toArray();

        // Map to appropriate property
        switch ($collectionName) {
            case 'featured_image':
                $this->existingFeaturedImage = !empty($media) ? $media[0] : [];
                break;
            case 'gallery':
                $this->existingGallery = $media;
                break;
            case 'videos':
                $this->existingVideos = $media;
                break;
            case 'audio':
                $this->existingAudio = $media;
                break;
            case 'documents':
                $this->existingDocuments = $media;
                break;
        }
    }

    // ========================================
    // UPLOAD METHODS
    // ========================================

    public function updatedFeaturedImageFile()
    {
        // Null ise (silme işlemi) validation yapma
        if (!$this->featuredImageFile) {
            return;
        }

        $this->validate([
            'featuredImageFile' => 'image|max:10240', // 10MB
        ]);

        // Model ID yoksa, session-based temp storage'a kaydet
        if (!$this->modelId) {
            $this->saveFeaturedToTempStorage();
            $this->featuredImageFile = null;
            return;
        }

        // Model varsa direkt upload
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        try {
            $this->mediaService->uploadMedia($model, $this->featuredImageFile, 'featured_image');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.upload_success'),
                'type' => 'success'
            ]);

            $this->featuredImageFile = null;
            $this->loadCollection($model, 'featured_image');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.upload_error', ['message' => $e->getMessage()]),
                'type' => 'error'
            ]);
        }
    }

    public function updatedGalleryFiles()
    {
        $this->validate([
            'galleryFiles.*' => 'image|max:10240', // 10MB per file
        ]);

        // Model ID yoksa, session-based temp storage'a kaydet
        if (!$this->modelId) {
            $this->saveGalleryToTempStorage();
            $this->galleryFiles = [];
            return;
        }

        // Model varsa direkt upload
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        try {
            $this->mediaService->uploadMultipleMedia($model, $this->galleryFiles, 'gallery');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.upload_success'),
                'type' => 'success'
            ]);

            $this->galleryFiles = [];
            $this->loadCollection($model, 'gallery');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.upload_error', ['message' => $e->getMessage()]),
                'type' => 'error'
            ]);
        }
    }

    // ========================================
    // SESSION-BASED TEMP STORAGE METHODS
    // ========================================

    protected function saveFeaturedToTempStorage()
    {
        $tempDir = public_path('temp/media');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $fileName = uniqid() . '_' . $this->featuredImageFile->getClientOriginalName();
        $filePath = $tempDir . '/' . $fileName;

        $this->featuredImageFile->storeAs('', $fileName, ['disk' => 'temp_media']);

        $this->tempFeaturedImage = [
            'path' => 'temp/media/' . $fileName,
            'original_name' => $this->featuredImageFile->getClientOriginalName(),
            'url' => asset('temp/media/' . $fileName)
        ];

        $this->saveTempFilesToSession();

        Log::info('📸 Featured image saved to temp storage', [
            'path' => $this->tempFeaturedImage['path']
        ]);
    }

    protected function saveGalleryToTempStorage()
    {
        $tempDir = public_path('temp/media');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        foreach ($this->galleryFiles as $file) {
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $filePath = $tempDir . '/' . $fileName;

            $file->storeAs('', $fileName, ['disk' => 'temp_media']);

            $this->tempGallery[] = [
                'path' => 'temp/media/' . $fileName,
                'original_name' => $file->getClientOriginalName(),
                'url' => asset('temp/media/' . $fileName)
            ];
        }

        $this->saveTempFilesToSession();

        Log::info('🖼️ Gallery images saved to temp storage', [
            'count' => count($this->tempGallery)
        ]);
    }

    protected function saveTempFilesToSession()
    {
        session([
            'media_temp_featured_' . $this->modelType => $this->tempFeaturedImage,
            'media_temp_gallery_' . $this->modelType => $this->tempGallery,
        ]);
    }

    protected function loadTempFilesFromSession()
    {
        $this->tempFeaturedImage = session('media_temp_featured_' . $this->modelType, null);
        $this->tempGallery = session('media_temp_gallery_' . $this->modelType, []);

        Log::info('🔄 Temp files loaded from session', [
            'featured' => !empty($this->tempFeaturedImage),
            'gallery_count' => count($this->tempGallery)
        ]);
    }

    protected function clearTempFilesFromSession()
    {
        session()->forget('media_temp_featured_' . $this->modelType);
        session()->forget('media_temp_gallery_' . $this->modelType);

        // Delete actual temp files
        if ($this->tempFeaturedImage) {
            $filePath = public_path($this->tempFeaturedImage['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        foreach ($this->tempGallery as $item) {
            $filePath = public_path($item['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->tempFeaturedImage = null;
        $this->tempGallery = [];
    }

    // ========================================
    // TEMP FILE MANAGEMENT
    // ========================================

    public function removeTempFeaturedImage()
    {
        if ($this->tempFeaturedImage) {
            $filePath = public_path($this->tempFeaturedImage['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->tempFeaturedImage = null;
            $this->saveTempFilesToSession();
        }
    }

    public function removeTempGalleryFile($index)
    {
        if (isset($this->tempGallery[$index])) {
            $filePath = public_path($this->tempGallery[$index]['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            unset($this->tempGallery[$index]);
            $this->tempGallery = array_values($this->tempGallery);
            $this->saveTempFilesToSession();
        }
    }

    public function removeGalleryFile($index)
    {
        if (isset($this->galleryFiles[$index])) {
            unset($this->galleryFiles[$index]);
            $this->galleryFiles = array_values($this->galleryFiles); // Re-index
        }
    }

    /**
     * Session-based temporary gallery sıralamasını güncelle
     */
    public function updateTempGalleryOrder(array $order)
    {
        $newOrder = [];

        foreach ($order as $item) {
            $index = $item['index'];
            if (isset($this->tempGallery[$index])) {
                $newOrder[] = $this->tempGallery[$index];
            }
        }

        $this->tempGallery = $newOrder;
        $this->saveTempFilesToSession();

        Log::info('🔄 Temp gallery order updated (session-based)', [
            'count' => count($this->tempGallery)
        ]);
    }

    /**
     * Event handler for gallery order update
     * Livewire 3: Global events receive payload as single parameter
     */
    public function updateGalleryOrderFromEvent($items)
    {
        // Livewire 3'te global event { items: [...] } şeklinde gelir
        // Ancak listener'da doğrudan $items olarak alırız
        if (is_array($items)) {
            $this->updateGalleryOrder($items);
        }
    }

    /**
     * Event handler for temp gallery order update
     * Livewire 3: Global events receive payload as single parameter
     */
    public function updateTempGalleryOrderFromEvent($items)
    {
        // Livewire 3'te global event { items: [...] } şeklinde gelir
        // Ancak listener'da doğrudan $items olarak alırız
        if (is_array($items)) {
            $this->updateTempGalleryOrder($items);
        }
    }

    // ========================================
    // DELETE METHODS
    // ========================================

    public function deleteFeaturedImage()
    {
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        try {
            $model->clearMediaCollection('featured_image');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.file_deleted'),
                'type' => 'success'
            ]);

            $this->existingFeaturedImage = [];

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.delete_error', ['message' => $e->getMessage()]),
                'type' => 'error'
            ]);
        }
    }

    public function deleteMedia(int $mediaId, string $collectionName = 'gallery')
    {
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        $success = $this->mediaService->deleteMedia($mediaId, $model);

        if ($success) {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.file_deleted'),
                'type' => 'success'
            ]);

            $this->loadCollection($model, $collectionName);
        } else {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.unauthorized'),
                'type' => 'error'
            ]);
        }
    }

    // ========================================
    // FEATURED FROM GALLERY
    // ========================================

    public function setFeaturedFromGallery(int $mediaId)
    {
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        $success = $this->mediaService->setFeaturedFromGallery($model, $mediaId);

        if ($success) {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.featured_set'),
                'type' => 'success'
            ]);

            $this->loadCollection($model, 'featured_image');
        } else {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.unauthorized'),
                'type' => 'error'
            ]);
        }
    }

    // ========================================
    // SORTING
    // ========================================

    public function updateGalleryOrder(array $list)
    {
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        Log::info('🔍 Gallery Order - Gelen sıralama:', [
            'list' => $list
        ]);

        // Backend'e kaydet
        $success = $this->mediaService->updateGalleryOrder($model, $list);

        if ($success) {
            // Frontend'i güncelle - YENİ sıralamayı yükle
            $this->loadCollection($model, 'gallery');

            Log::info('✅ Gallery sıralaması güncellendi ve frontend yenilendi');
        } else {
            Log::error('❌ Gallery sıralaması güncellenemedi');
        }
    }

    // ========================================
    // HELPERS
    // ========================================

    protected function getModel()
    {
        if (!$this->modelId) {
            return null;
        }

        return $this->modelClass::find($this->modelId);
    }

    /**
     * Model kaydedildiğinde session-based temp dosyaları attach et
     */
    public function handleModelSaved($modelId)
    {
        // ModelId güncelle
        $this->modelId = $modelId;

        // Geçici dosyalar varsa attach et
        $model = $this->getModel();
        if (!$model) {
            Log::warning('Model bulunamadı', ['modelId' => $modelId]);
            return;
        }

        // Featured image upload (from temp storage)
        if ($this->tempFeaturedImage) {
            try {
                $filePath = public_path($this->tempFeaturedImage['path']);

                if (file_exists($filePath)) {
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $filePath,
                        $this->tempFeaturedImage['original_name'],
                        mime_content_type($filePath),
                        null,
                        true
                    );

                    $this->mediaService->uploadMedia($model, $uploadedFile, 'featured_image');

                    Log::info('📸 Featured image attached from temp storage', [
                        'model_id' => $modelId,
                        'filename' => $this->tempFeaturedImage['original_name']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Featured image attach failed', ['error' => $e->getMessage()]);
            }
        }

        // Gallery images upload (from temp storage)
        if (!empty($this->tempGallery)) {
            try {
                $uploadedFiles = [];

                foreach ($this->tempGallery as $item) {
                    $filePath = public_path($item['path']);

                    if (file_exists($filePath)) {
                        $uploadedFiles[] = new \Illuminate\Http\UploadedFile(
                            $filePath,
                            $item['original_name'],
                            mime_content_type($filePath),
                            null,
                            true
                        );
                    }
                }

                if (!empty($uploadedFiles)) {
                    $this->mediaService->uploadMultipleMedia($model, $uploadedFiles, 'gallery');

                    Log::info('🖼️ Gallery images attached from temp storage', [
                        'model_id' => $modelId,
                        'count' => count($uploadedFiles)
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Gallery attach failed', ['error' => $e->getMessage()]);
            }
        }

        // Cleanup temp files
        $this->clearTempFilesFromSession();
        $this->loadCollection($model, 'featured_image');
        $this->loadCollection($model, 'gallery');
    }

    protected function hasCollection(string $collectionName): bool
    {
        return in_array($collectionName, $this->collections);
    }

    protected function getCollectionConfig(string $collectionName): ?array
    {
        return $this->mediaService->getCollectionConfig($this->modelType, $collectionName);
    }

    // ========================================
    // CAPTION MANAGEMENT
    // ========================================

    /**
     * Save media caption (title, description, alt_text)
     */
    public function saveMediaCaption($mediaId, $captionData)
    {
        Log::info('💾 Caption data received from frontend', [
            'mediaId' => $mediaId,
            'captionData' => $captionData
        ]);

        // Temp media için caption'u session'da sakla
        if ($mediaId === 'temp-featured') {
            if ($this->tempFeaturedImage) {
                $this->tempFeaturedImage['caption'] = $captionData;

                // Session'da da güncelle
                $sessionKey = "media_temp_{$this->modelType}_{$this->modelId}_featured_image";
                session([$sessionKey => $this->tempFeaturedImage]);

                Log::info('💾 Temp featured caption saved to session', [
                    'caption' => $captionData
                ]);
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.caption_saved'),
                'type' => 'success'
            ]);

            $this->dispatch('close-caption-modal');
            return;
        }

        // Temp gallery için caption'u session'da sakla
        if (is_string($mediaId) && str_starts_with($mediaId, 'temp-gallery-')) {
            $index = (int) str_replace('temp-gallery-', '', $mediaId);
            if (isset($this->tempGallery[$index])) {
                $this->tempGallery[$index]['caption'] = $captionData;

                // Session'da da güncelle
                $sessionKey = "media_temp_{$this->modelType}_{$this->modelId}_gallery";
                session([$sessionKey => $this->tempGallery]);

                Log::info('💾 Temp gallery caption saved to session', [
                    'index' => $index,
                    'caption' => $captionData
                ]);
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.caption_saved'),
                'type' => 'success'
            ]);

            $this->dispatch('close-caption-modal');
            return;
        }

        // Existing media için Spatie custom properties kullan
        $model = $this->getModel();
        if (!$model) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.error_occurred'),
                'type' => 'error'
            ]);
            return;
        }

        try {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($mediaId);

            // Security: Check ownership
            if (!$media || $media->model_id != $model->id || $media->model_type != get_class($model)) {
                Log::warning('Unauthorized media caption edit attempt', [
                    'media_id' => $mediaId,
                    'model_id' => $model->id
                ]);

                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => __('mediamanagement::admin.unauthorized'),
                    'type' => 'error'
                ]);
                return;
            }

            // Custom properties güncelle
            $media->setCustomProperty('title', $captionData['title'] ?? []);
            $media->setCustomProperty('description', $captionData['description'] ?? []);
            $media->setCustomProperty('alt_text', $captionData['alt_text'] ?? []);
            $media->save();

            Log::info('💾 Media caption saved', [
                'media_id' => $mediaId,
                'model_id' => $model->id,
                'custom_properties' => $media->custom_properties
            ]);

            // Sadece ilgili media item'ın custom_properties'ini güncelle
            // loadCollection çağırmıyoruz çünkü tüm component'i render ediyor ve tab durumlarını bozuyor
            if ($media->collection_name === 'featured_image') {
                if (!empty($this->existingFeaturedImage)) {
                    $this->existingFeaturedImage['custom_properties'] = $media->custom_properties;
                }
            } elseif ($media->collection_name === 'gallery') {
                foreach ($this->existingGallery as $index => $item) {
                    if ($item['id'] == $mediaId) {
                        $this->existingGallery[$index]['custom_properties'] = $media->custom_properties;
                        break;
                    }
                }
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.caption_saved'),
                'type' => 'success'
            ]);

            $this->dispatch('close-caption-modal');

        } catch (\Exception $e) {
            Log::error('Media caption save failed', [
                'media_id' => $mediaId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.error_occurred'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Get media caption for a specific field and locale
     */
    public function getMediaCaption($media, string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        // Array ise (temp media)
        if (is_array($media)) {
            $caption = $media['caption'][$field] ?? [];
            return $caption[$locale] ?? $caption['tr'] ?? null;
        }

        // Spatie Media object ise
        if ($media instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media) {
            $customProps = $media->getCustomProperty($field, []);
            return $customProps[$locale] ?? $customProps['tr'] ?? null;
        }

        return null;
    }

    // ========================================
    // RENDER
    // ========================================

    public function render()
    {
        return view('mediamanagement::admin.livewire.universal-media-component', [
            'hasFeautredImage' => $this->hasCollection('featured_image'),
            'hasGallery' => $this->hasCollection('gallery'),
            'hasVideos' => $this->hasCollection('videos'),
            'hasAudio' => $this->hasCollection('audio'),
            'hasDocuments' => $this->hasCollection('documents'),
        ]);
    }
}
