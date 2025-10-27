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
    public bool $hideLabel = false; // Label'ı gizle (form-builder için)
    public ?string $acceptedFileTypes = null; // Özel dosya tipleri (örn: ".ico,.png" veya "image/png,image/x-icon")

    // ========================================
    // FILE UPLOAD PROPERTIES
    // ========================================
    public $featuredImageFile = null;
    public $seoOgImageFile = null;
    public $galleryFiles = [];
    public $videoFiles = [];
    public $audioFiles = [];
    public $documentFiles = [];

    // ========================================
    // EXISTING MEDIA
    // ========================================
    public array $existingFeaturedImage = [];
    public array $existingSeoOgImage = [];
    public array $existingGallery = [];
    public array $existingVideos = [];
    public array $existingAudio = [];
    public array $existingDocuments = [];

    // ========================================
    // SESSION-BASED TEMPORARY STORAGE
    // ========================================
    public ?array $tempFeaturedImage = null; // ['path' => 'temp/xxx.jpg', 'original_name' => 'photo.jpg']
    public ?array $tempSeoOgImage = null; // ['path' => 'temp/xxx.jpg', 'original_name' => 'photo.jpg']
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
        'product-saved' => 'handleModelSaved',
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
                // ✅ Thumbmaker kullan - cache'li ve hızlı
                $fullUrl = $item->getUrl();
                $thumbUrl = thumb($item, 400, 400, [
                    'quality' => 80,
                    'scale' => 1,
                    'alignment' => 'c',
                    'format' => 'webp'
                ]);

                return [
                    'id' => $item->id,
                    'url' => $fullUrl,
                    'thumb' => $thumbUrl,
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
            case 'seo_og_image':
                $this->existingSeoOgImage = !empty($media) ? $media[0] : [];
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
            default:
                // Dynamic collection (örn: Setting için site_logo, site_favicon vs.)
                // Tek görsel olan tüm collection'ları featured_image olarak göster
                $this->existingFeaturedImage = !empty($media) ? $media[0] : [];
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

        // Root user (ID: 1) için validation SKIP
        if (!auth()->check() || auth()->user()->id !== 1) {
            $this->validate([
                'featuredImageFile' => 'file|max:20480', // 20MB for non-root users
            ]);
        }
        // Root user için validation yok - sınırsız upload

        // HER ZAMAN temp storage kullan (SSL handshake sorununu bypass et)
        $this->saveFeaturedToTempStorage();
        $this->featuredImageFile = null;

        // Model varsa direkt media library'ye de ekle
        if ($this->modelId) {
            $model = $this->getModel();
            if ($model && $this->tempFeaturedImage) {
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

                        // Dynamic collection name (ilk collection'ı kullan)
                        $collectionName = $this->collections[0] ?? 'featured_image';
                        $this->mediaService->uploadMedia($model, $uploadedFile, $collectionName);
                        $this->loadCollection($model, $collectionName);

                        // Temp'i temizle
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        if (isset($this->tempFeaturedImage['thumb'])) {
                            $thumbPath = str_replace(asset(''), public_path(), $this->tempFeaturedImage['thumb']);
                            if (file_exists($thumbPath)) {
                                unlink($thumbPath);
                            }
                        }
                        $this->tempFeaturedImage = null;
                        $this->saveTempFilesToSession();

                        $this->dispatch('toast', [
                            'title' => __('admin.success'),
                            'message' => __('mediamanagement::admin.upload_success'),
                            'type' => 'success'
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => __('admin.error'),
                        'message' => __('mediamanagement::admin.upload_error', ['message' => $e->getMessage()]),
                        'type' => 'error'
                    ]);
                }
            }
        }
    }


    public function updatedSeoOgImageFile()
    {
        if (!$this->seoOgImageFile) {
            return;
        }

        // Root user (ID: 1) için sınırsız upload
        $maxSize = (auth()->check() && auth()->user()->id === 1) ? PHP_INT_MAX : 20480;

        $this->validate([
            'seoOgImageFile' => 'image|max:' . $maxSize . '|dimensions:max_width=4000,max_height=4000', // Root: unlimited, Others: 20MB
        ]);

        if (!$this->modelId) {
            $this->saveSeoOgToTempStorage();
            $this->seoOgImageFile = null;
            return;
        }

        $model = $this->getModel();
        if (!$model) {
            return;
        }

        try {
            $this->mediaService->uploadMedia($model, $this->seoOgImageFile, 'seo_og_image');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.upload_success'),
                'type' => 'success'
            ]);

            $this->seoOgImageFile = null;
            $this->loadCollection($model, 'seo_og_image');

            $this->dispatch('seo-og-image-updated', [
                'url' => $model->getFirstMediaUrl('seo_og_image') ?: '',
                'model_type' => $this->modelType,
                'model_id' => $this->modelId,
            ]);

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
        // Root user (ID: 1) için validation SKIP
        if (!auth()->check() || auth()->user()->id !== 1) {
            $this->validate([
                'galleryFiles.*' => 'image|max:20480|dimensions:max_width=4000,max_height=4000', // 20MB for non-root
            ]);
        }
        // Root user için validation yok - sınırsız upload

        // HER ZAMAN temp storage kullan (SSL handshake sorununu bypass et)
        $previousTempCount = count($this->tempGallery);
        $this->saveGalleryToTempStorage();
        $this->galleryFiles = [];

        // Model varsa direkt media library'ye de ekle
        if ($this->modelId) {
            $model = $this->getModel();
            if ($model && !empty($this->tempGallery)) {
                try {
                    $uploadedFiles = [];
                    $newTempItems = array_slice($this->tempGallery, $previousTempCount);

                    foreach ($newTempItems as $item) {
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
                        $this->loadCollection($model, 'gallery');

                        // Yüklenen temp dosyaları temizle
                        foreach ($newTempItems as $item) {
                            $filePath = public_path($item['path']);
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            if (isset($item['thumb'])) {
                                $thumbPath = str_replace(asset(''), public_path(), $item['thumb']);
                                if (file_exists($thumbPath)) {
                                    unlink($thumbPath);
                                }
                            }
                        }

                        // Temp gallery'den yeni eklenenileri kaldır
                        $this->tempGallery = array_slice($this->tempGallery, 0, $previousTempCount);
                        $this->saveTempFilesToSession();

                        $this->dispatch('toast', [
                            'title' => __('admin.success'),
                            'message' => __('mediamanagement::admin.upload_success'),
                            'type' => 'success'
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->dispatch('toast', [
                        'title' => __('admin.error'),
                        'message' => __('mediamanagement::admin.upload_error', ['message' => $e->getMessage()]),
                        'type' => 'error'
                    ]);
                }
            }
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

        // Thumbnail oluştur
        $thumbFileName = 'thumb_' . $fileName;
        $thumbPath = $tempDir . '/' . $thumbFileName;
        $this->createThumbnail($filePath, $thumbPath, 300, 200);

        $this->tempFeaturedImage = [
            'path' => 'temp/media/' . $fileName,
            'original_name' => $this->featuredImageFile->getClientOriginalName(),
            'url' => asset('temp/media/' . $fileName),
            'thumb' => asset('temp/media/' . $thumbFileName)
        ];

        $this->saveTempFilesToSession();

        Log::info('📸 Featured image saved to temp storage', [
            'path' => $this->tempFeaturedImage['path']
        ]);
    }


    protected function saveSeoOgToTempStorage()
    {
        $tempDir = public_path('temp/media');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $fileName = uniqid() . '_' . $this->seoOgImageFile->getClientOriginalName();
        $filePath = $tempDir . '/' . $fileName;

        $this->seoOgImageFile->storeAs('', $fileName, ['disk' => 'temp_media']);

        $thumbFileName = 'thumb_' . $fileName;
        $thumbPath = $tempDir . '/' . $thumbFileName;
        $this->createThumbnail($filePath, $thumbPath, 300, 200);

        $this->tempSeoOgImage = [
            'path' => 'temp/media/' . $fileName,
            'original_name' => $this->seoOgImageFile->getClientOriginalName(),
            'url' => asset('temp/media/' . $fileName),
            'thumb' => asset('temp/media/' . $thumbFileName)
        ];

        $this->saveTempFilesToSession();

        $this->dispatch('seo-og-image-updated', [
            'url' => $this->tempSeoOgImage['url'] ?? '',
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
        ]);

        Log::info('📸 SEO OG image saved to temp storage', [
            'path' => $this->tempSeoOgImage['path']
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

            // Thumbnail oluştur
            $thumbFileName = 'thumb_' . $fileName;
            $thumbPath = $tempDir . '/' . $thumbFileName;
            $this->createThumbnail($filePath, $thumbPath, 300, 200);

            $this->tempGallery[] = [
                'path' => 'temp/media/' . $fileName,
                'original_name' => $file->getClientOriginalName(),
                'url' => asset('temp/media/' . $fileName),
                'thumb' => asset('temp/media/' . $thumbFileName)
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
            'media_temp_seo_og_' . $this->modelType => $this->tempSeoOgImage,
            'media_temp_gallery_' . $this->modelType => $this->tempGallery,
        ]);
    }

    protected function loadTempFilesFromSession()
    {
        $this->tempFeaturedImage = session('media_temp_featured_' . $this->modelType, null);
        $this->tempSeoOgImage = session('media_temp_seo_og_' . $this->modelType, null);
        $this->tempGallery = session('media_temp_gallery_' . $this->modelType, []);

        Log::info('🔄 Temp files loaded from session', [
            'featured' => !empty($this->tempFeaturedImage),
            'gallery_count' => count($this->tempGallery)
        ]);
    }

    protected function clearTempFilesFromSession()
    {
        session()->forget('media_temp_featured_' . $this->modelType);
        session()->forget('media_temp_seo_og_' . $this->modelType);
        session()->forget('media_temp_gallery_' . $this->modelType);

        // Delete actual temp files and their thumbnails
        if ($this->tempFeaturedImage) {
            $filePath = public_path($this->tempFeaturedImage['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Thumbnail'i de sil
            if (isset($this->tempFeaturedImage['thumb'])) {
                $thumbPath = str_replace(asset(''), public_path(), $this->tempFeaturedImage['thumb']);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }
        }

        if ($this->tempSeoOgImage) {
            $filePath = public_path($this->tempSeoOgImage['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            if (isset($this->tempSeoOgImage['thumb'])) {
                $thumbPath = str_replace(asset(''), public_path(), $this->tempSeoOgImage['thumb']);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }
        }

        foreach ($this->tempGallery as $item) {
            $filePath = public_path($item['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Thumbnail'i de sil
            if (isset($item['thumb'])) {
                $thumbPath = str_replace(asset(''), public_path(), $item['thumb']);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }
        }

        $this->tempFeaturedImage = null;
        $this->tempSeoOgImage = null;
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
            // Thumbnail'i de sil
            if (isset($this->tempFeaturedImage['thumb'])) {
                $thumbPath = str_replace(asset(''), public_path(), $this->tempFeaturedImage['thumb']);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }
            $this->tempFeaturedImage = null;
            $this->saveTempFilesToSession();
        }
    }


    public function removeTempSeoOgImage()
    {
        if ($this->tempSeoOgImage) {
            $filePath = public_path($this->tempSeoOgImage['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            if (isset($this->tempSeoOgImage['thumb'])) {
                $thumbPath = str_replace(asset(''), public_path(), $this->tempSeoOgImage['thumb']);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }
            $this->tempSeoOgImage = null;
            $this->saveTempFilesToSession();

            $this->dispatch('seo-og-image-updated', [
                'url' => '',
                'model_type' => $this->modelType,
                'model_id' => $this->modelId,
            ]);
        }
    }
    public function removeTempGalleryFile($index)
    {
        if (isset($this->tempGallery[$index])) {
            $filePath = public_path($this->tempGallery[$index]['path']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Thumbnail'i de sil
            if (isset($this->tempGallery[$index]['thumb'])) {
                $thumbPath = str_replace(asset(''), public_path(), $this->tempGallery[$index]['thumb']);
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
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


    public function deleteSeoOgImage()
    {
        if (!$this->modelId) {
            $this->removeTempSeoOgImage();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.file_deleted'),
                'type' => 'success'
            ]);

            return;
        }

        $model = $this->getModel();
        if (!$model) {
            return;
        }

        try {
            $model->clearMediaCollection('seo_og_image');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('mediamanagement::admin.file_deleted'),
                'type' => 'success'
            ]);

            $this->existingSeoOgImage = [];
            $this->dispatch('seo-og-image-updated', [
                'url' => '',
                'model_type' => $this->modelType,
                'model_id' => $this->modelId,
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('mediamanagement::admin.delete_error', ['message' => $e->getMessage()]),
                'type' => 'error'
            ]);
        }
    }
    public function deleteFeaturedImage()
    {
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        try {
            // Dynamic collection name (ilk collection'ı kullan)
            $collectionName = $this->collections[0] ?? 'featured_image';
            $model->clearMediaCollection($collectionName);

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

            // Dynamic collection name (ilk collection'ı kullan)
            $collectionName = $this->collections[0] ?? 'featured_image';
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

                    // Dynamic collection name (ilk collection'ı kullan)
                    $collectionName = $this->collections[0] ?? 'featured_image';
                    $this->mediaService->uploadMedia($model, $uploadedFile, $collectionName);

                    Log::info('📸 Featured image attached from temp storage', [
                        'model_id' => $modelId,
                        'filename' => $this->tempFeaturedImage['original_name'],
                        'collection' => $collectionName
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Featured image attach failed', ['error' => $e->getMessage()]);
            }
        }

        // SEO OG image upload (from temp storage)
        if ($this->tempSeoOgImage) {
            try {
                $filePath = public_path($this->tempSeoOgImage['path']);

                if (file_exists($filePath)) {
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $filePath,
                        $this->tempSeoOgImage['original_name'],
                        mime_content_type($filePath),
                        null,
                        true
                    );

                    $this->mediaService->uploadMedia($model, $uploadedFile, 'seo_og_image');

                    Log::info('📸 SEO OG image attached from temp storage', [
                        'model_id' => $modelId,
                        'filename' => $this->tempSeoOgImage['original_name']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('SEO OG image attach failed', ['error' => $e->getMessage()]);
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

        // Reload all collections
        foreach ($this->collections as $collectionName) {
            $this->loadCollection($model, $collectionName);
        }

        $this->dispatch('seo-og-image-updated', [
            'url' => $model->getFirstMediaUrl('seo_og_image') ?: '',
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
        ]);
    }

    protected function hasCollection(string $collectionName): bool
    {
        return in_array($collectionName, $this->collections);
    }

    protected function getCollectionConfig(string $collectionName): ?array
    {
        return $this->mediaService->getCollectionConfig($this->modelType, $collectionName);
    }

    /**
     * Temp dosyalar için basit thumbnail oluştur (GD library)
     */
    protected function createThumbnail(string $sourcePath, string $destPath, int $maxWidth = 300, int $maxHeight = 200): bool
    {
        try {
            // Dosya var mı kontrol et
            if (!file_exists($sourcePath)) {
                Log::warning('Thumbnail source file not found', ['path' => $sourcePath]);
                return false;
            }

            // Görsel bilgilerini al
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                Log::warning('Invalid image file for thumbnail', ['path' => $sourcePath]);
                return false;
            }

            list($width, $height, $type) = $imageInfo;

            // Kaynak görseli yükle
            $source = null;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($sourcePath);
                    break;
                case IMAGETYPE_WEBP:
                    $source = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    Log::warning('Unsupported image type for thumbnail', ['type' => $type]);
                    return false;
            }

            if (!$source) {
                return false;
            }

            // Thumbnail boyutlarını hesapla (aspect ratio koru)
            $ratio = $width / $height;
            if ($width > $height) {
                $thumbWidth = min($maxWidth, $width);
                $thumbHeight = (int)($thumbWidth / $ratio);
            } else {
                $thumbHeight = min($maxHeight, $height);
                $thumbWidth = (int)($thumbHeight * $ratio);
            }

            // Thumbnail oluştur
            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);

            // PNG/GIF için transparency koru
            if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
                imagefilledrectangle($thumb, 0, 0, $thumbWidth, $thumbHeight, $transparent);
            }

            // Resize
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

            // WebP olarak kaydet (daha iyi performans)
            $saved = imagewebp($thumb, $destPath, 85);

            // Cleanup
            imagedestroy($source);
            imagedestroy($thumb);

            if ($saved) {
                Log::info('Thumbnail created successfully', [
                    'source' => $sourcePath,
                    'dest' => $destPath,
                    'size' => "{$thumbWidth}x{$thumbHeight}"
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Thumbnail creation failed', [
                'error' => $e->getMessage(),
                'source' => $sourcePath
            ]);
            return false;
        }
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
            $featuredCollectionName = $this->collections[0] ?? 'featured_image';
            if ($media->collection_name === $featuredCollectionName || $media->collection_name === 'featured_image') {
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

    /**
     * Manual upload handler - Livewire bypass for SSL issues
     */
    public static function manualFeaturedUpload(\Illuminate\Http\Request $request)
    {
        \Log::info('🔍 [MANUAL_UPLOAD] START', [
            'user_id' => auth()->id(),
            'is_root' => auth()->check() && auth()->user()->id === 1,
            'has_file' => $request->hasFile('file'),
        ]);

        try {
            $file = $request->file('file');

            if (!$file) {
                \Log::warning('❌ [MANUAL_UPLOAD] No file uploaded');
                return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
            }

            \Log::info('📁 [MANUAL_UPLOAD] File received', [
                'size_bytes' => $file->getSize(),
                'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                'mime' => $file->getMimeType(),
                'original_name' => $file->getClientOriginalName(),
            ]);

            // Root user (ID: 1) için validation SKIP
            $isRoot = auth()->check() && auth()->user()->id === 1;

            if (!$isRoot) {
                \Log::info('⚠️ [MANUAL_UPLOAD] Non-root user - validating');
                $request->validate([
                    'file' => 'required|file|max:20480', // 20MB for non-root
                ]);
                \Log::info('✅ [MANUAL_UPLOAD] Validation passed');
            } else {
                \Log::info('✅ [MANUAL_UPLOAD] Root user - SKIP validation');
            }

            // Save to temp storage
            \Log::info('💾 [MANUAL_UPLOAD] Storing file...');
            $tempPath = $file->store('livewire-tmp', 'public');
            \Log::info('✅ [MANUAL_UPLOAD] File stored', ['temp_path' => $tempPath]);

            return response()->json([
                'success' => true,
                'tempPath' => $tempPath,
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ [MANUAL_UPLOAD] Validation failed', [
                'errors' => $e->errors(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            \Log::error('❌ [MANUAL_UPLOAD] Exception', [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle manual upload from Alpine.js
     */
    public function handleManualUpload($tempPath)
    {
        try {
            // Temp dosyayı oku
            $fullPath = storage_path('app/public/' . $tempPath);

            if (!file_exists($fullPath)) {
                throw new \Exception('Temporary file not found');
            }

            // UploadedFile oluştur
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $fullPath,
                basename($tempPath),
                mime_content_type($fullPath),
                null,
                true
            );

            // Mevcut updatedFeaturedImageFile logic'ini kullan
            $this->featuredImageFile = $uploadedFile;
            $this->updatedFeaturedImageFile();

            // Temp dosyayı sil
            @unlink($fullPath);

        } catch (\Exception $e) {
            \Log::error('[HANDLE_MANUAL_UPLOAD_ERROR]', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->addError('featuredImageFile', $e->getMessage());
        }
    }

    public function render()
    {
        // Dynamic collection check (Setting için site_logo, site_favicon vs.)
        $firstCollection = $this->collections[0] ?? null;
        $knownCollections = ['featured_image', 'seo_og_image', 'gallery', 'videos', 'audio', 'documents'];
        $isDynamicSingleFile = $firstCollection && !in_array($firstCollection, $knownCollections);

        return view('mediamanagement::admin.livewire.universal-media-component', [
            'hasFeautredImage' => $this->hasCollection('featured_image') || $isDynamicSingleFile,
            'hasSeoOgImage' => $this->hasCollection('seo_og_image'),
            'hasGallery' => $this->hasCollection('gallery'),
            'hasVideos' => $this->hasCollection('videos'),
            'hasAudio' => $this->hasCollection('audio'),
            'hasDocuments' => $this->hasCollection('documents'),
            'hideLabel' => $this->hideLabel,
        ]);
    }
}
