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
    // SERVICES
    // ========================================
    protected MediaService $mediaService;

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
        $this->validate([
            'featuredImageFile' => 'image|max:10240', // 10MB
        ]);

        // Model ID yoksa, parent'a geçici dosya bilgisi gönder
        if (!$this->modelId) {
            $this->dispatch('media-uploaded-temporarily', [
                'collection' => 'featured_image',
                'file' => $this->featuredImageFile,
            ]);
            return;
        }

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

        // Model ID yoksa, parent'a geçici dosya bilgisi gönder
        if (!$this->modelId) {
            $this->dispatch('media-uploaded-temporarily', [
                'collection' => 'gallery',
                'files' => $this->galleryFiles,
            ]);
            $this->galleryFiles = [];
            return;
        }

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

        $success = $this->mediaService->updateGalleryOrder($model, $list);

        if ($success) {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.order_updated'),
                'type' => 'success'
            ]);
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

    protected function hasCollection(string $collectionName): bool
    {
        return in_array($collectionName, $this->collections);
    }

    protected function getCollectionConfig(string $collectionName): ?array
    {
        return $this->mediaService->getCollectionConfig($this->modelType, $collectionName);
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
