<?php

namespace Modules\Announcement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Announcement\App\Models\Announcement;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

#[Layout('admin.layout')]
class AnnouncementManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $announcementId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'is_active' => true,
    ];

    public $studioEnabled = false;

    // Spatie Media Library - File uploads
    public $featuredImage;
    public $galleryImages = [];
    public $existingGallery = [];

    // Universal Component Data
    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = []; // Dil adları (native_name)
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    // SOLID Dependencies
    protected $announcementService;

    /**
     * Get current announcement model
     */
    #[Computed]
    public function currentPage()
    {
        if (!$this->announcementId) {
            return null;
        }

        return Announcement::query()->find($this->announcementId);
    }

    // Livewire Listeners - Universal component'lerden gelen event'ler
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    // Dependency Injection Boot
    public function boot()
    {
        // AnnouncementService'i initialize et (her zaman var)
        $this->announcementService = app(\Modules\Announcement\App\Services\AnnouncementService::class);

        // Layout sections
        view()->share('pretitle', __('announcement::admin.page_management'));
        view()->share('title', __('announcement::admin.pages'));
    }

    public function updated($propertyName)
    {
        // Tab completion status güncelleme - Universal Tab System'e bildir
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    public function mount($id = null)
    {
        // Dependencies initialize
        $this->boot();

        // Universal Component'lerden initial data al
        $this->initializeUniversalComponents();

        // Sayfa verilerini yükle
        if ($id) {
            $this->announcementId = $id;
            $this->loadPageData($id);
        } else {
            $this->initializeEmptyInputs();
        }

        // Studio modül kontrolü
        $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\EditorComponent');

        // Tab completion durumunu hesapla
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    /**
     * Universal Component'leri initialize et
     */
    protected function initializeUniversalComponents()
    {
        // Dil bilgileri - LanguageManagement modülünden (cached helper)
        $languages = available_tenant_languages();
        $this->availableLanguages = array_column($languages, 'code');
        $this->languageNames = array_column($languages, 'native_name', 'code');
        $this->currentLanguage = get_tenant_default_locale();

        // Tab bilgileri - Blade'de kullanılıyor
        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('announcement');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('announcement');
    }

    /**
     * Dil değişikliğini handle et (UniversalLanguageSwitcher'dan)
     */
    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 PageManage - Dil değişti', [
                'new_language' => $language
            ]);
        }
    }


    /**
     * Çeviri tamamlandığında (UniversalAIContent'ten)
     */
    public function handleTranslationCompleted($result)
    {
        if ($result['success'] && isset($result['results'])) {
            foreach ($result['results'] as $translationResult) {
                if ($translationResult['success']) {
                    $lang = $translationResult['language'];
                    $field = $translationResult['field'];
                    $translatedText = $translationResult['translated_text'];

                    // Çevrilmiş metni ilgili alana set et
                    if (isset($this->multiLangInputs[$lang][$field])) {
                        $this->multiLangInputs[$lang][$field] = $translatedText;

                        // Slug otomatik oluştur (sadece title çevirildiyse)
                        if ($field === 'title') {
                            $this->multiLangInputs[$lang]['slug'] = SlugHelper::generateFromTitle(
                                Announcement::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'announcement_id',
                                $this->announcementId
                            );
                        }
                    }
                }
            }

            // Çevirileri veritabanına kaydet
            $this->save();

            Log::info('✅ PageManage - Çeviri sonuçları alındı ve kaydedildi', [
                'translated_count' => $result['translated_count'] ?? 0
            ]);
        }
    }

    /**
     * AI içerik üretildiğinde (UniversalAIContent'ten)
     */
    public function handleAIContentGenerated($result)
    {
        if ($result['success']) {
            $content = $result['content'];
            $targetField = $result['target_field'];
            $language = $result['language'];

            // Content'i ilgili field'a ata
            if (isset($this->multiLangInputs[$language][$targetField])) {
                $this->multiLangInputs[$language][$targetField] = $content;

                // Database'e kaydet
                $this->save();

                Log::info('✅ PageManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    /**
     * Sayfa verilerini yükle
     */
    protected function loadPageData($id)
    {
        // AnnouncementService her zaman var, fallback gereksiz
        $formData = $this->announcementService->prepareAnnouncementForForm($id, $this->currentLanguage);
        $announcement = $formData['announcement'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($announcement) {
            // Dil-neutral alanlar
            $this->inputs = $announcement->only(['is_active']);

            // Çoklu dil alanları - FALLBACK KAPALI (kullanıcı tüm dilleri boşaltabilsin)
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $announcement->getTranslated('title', $lang, false) ?? '',
                    'body' => $announcement->getTranslated('body', $lang, false) ?? '',
                    'slug' => $announcement->getTranslated('slug', $lang, false) ?? '',
                ];
            }

            // NOT: SEO verileri Universal SEO Tab component'te yüklenir
        }
    }

    /**
     * Boş inputs hazırla
     */
    protected function initializeEmptyInputs()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'body' => '',
                'slug' => '',
            ];
        }
    }

    /**
     * Tüm form datasını al (tab completion için)
     */
    protected function getAllFormData(): array
    {
        return array_merge(
            $this->inputs,
            $this->multiLangInputs[$this->currentLanguage] ?? []
        );
    }

    /**
     * Ana dili belirle (mecburi olan dil)
     * LanguageManagement modülünden helper kullan
     */
    protected function getMainLanguage()
    {
        return get_tenant_default_locale();
    }

    protected function rules()
    {
        $rules = [
            'inputs.is_active' => 'boolean',
        ];

        // Çoklu dil alanları - ana dil mecburi, diğerleri opsiyonel
        $mainLanguage = $this->getMainLanguage();
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $mainLanguage ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
        }

        return $rules;
    }

    protected $messages = [
        'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
        'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Başlık en fazla 255 karakter olabilir',
    ];

    /**
     * Tüm validation mesajlarını al
     */
    protected function getMessages()
    {
        // Slug validation mesajları - SlugHelper'dan al
        $slugMessages = SlugHelper::getValidationMessages($this->availableLanguages, 'multiLangInputs');

        return array_merge($this->messages, $slugMessages);
    }

    /**
     * İçeriği validate et ve sanitize et (HTML, CSS, JS)
     */
    protected function validateAndSanitizeContent(): array
    {
        $validated = [];
        $errors = [];

        // HTML body validation (her dil için)
        foreach ($this->availableLanguages as $lang) {
            $body = $this->multiLangInputs[$lang]['body'] ?? '';
            if (!empty(trim($body))) {
                $result = \App\Services\SecurityValidationService::validateHtml($body);
                if (!$result['valid']) {
                    $errors[] = "HTML ({$lang}): " . implode(', ', $result['errors']);
                } else {
                    $validated['body'][$lang] = $result['clean_code'];
                }
            }
        }

        return [
            'valid' => empty($errors),
            'data' => $validated,
            'errors' => $errors
        ];
    }

    /**
     * Çoklu dil verilerini hazırla (title, slug, body)
     */
    protected function prepareMultiLangData(array $validatedContent = []): array
    {
        $multiLangData = [];

        // Title verilerini topla
        $multiLangData['title'] = [];
        foreach ($this->availableLanguages as $lang) {
            $title = $this->multiLangInputs[$lang]['title'] ?? '';
            if (!empty($title)) {
                $multiLangData['title'][$lang] = $title;
            }
        }

        // Slug verilerini işle - SlugHelper toplu işlem
        $slugInputs = [];
        $titleInputs = [];
        foreach ($this->availableLanguages as $lang) {
            $slugInputs[$lang] = $this->multiLangInputs[$lang]['slug'] ?? '';
            $titleInputs[$lang] = $this->multiLangInputs[$lang]['title'] ?? '';
        }

        $multiLangData['slug'] = SlugHelper::processMultiLanguageSlugs(
            Announcement::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->announcementId
        );

        // Body verilerini ekle (validated'dan)
        if (!empty($validatedContent['body'])) {
            $multiLangData['body'] = $validatedContent['body'];
        }

        return $multiLangData;
    }

    public function save($redirect = false, $resetForm = false)
    {
        // TinyMCE içeriğini senkronize et
        $this->dispatch('sync-tinymce-content');

        Log::info('🚀 SAVE METHOD BAŞLADI', [
            'announcementId' => $this->announcementId,
            'redirect' => $redirect,
            'currentLanguage' => $this->currentLanguage
        ]);

        try {
            $this->validate($this->rules(), $this->getMessages());
            Log::info('✅ Validation başarılı');
        } catch (\Exception $e) {
            Log::error('❌ Validation HATASI', [
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'title' => 'Doğrulama Hatası',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);

            // Tab restore tetikle - validation hatası sonrası tab görünür kalsın
            $this->dispatch('restore-active-tab');

            return;
        }

        // İçerik güvenlik validasyonu (HTML/CSS/JS)
        $validation = $this->validateAndSanitizeContent();
        if (!$validation['valid']) {
            $this->dispatch('toast', [
                'title' => 'İçerik Doğrulama Hatası',
                'message' => implode("\n", $validation['errors']),
                'type' => 'error'
            ]);

            // Tab restore tetikle
            $this->dispatch('restore-active-tab');

            return;
        }

        // Çoklu dil verilerini hazırla (title, slug, body)
        $multiLangData = $this->prepareMultiLangData($validation['data']);

        // Safe inputs
        $safeInputs = $this->inputs;

        $data = array_merge($safeInputs, $multiLangData);

        // Yeni kayıt mı kontrol et
        $isNewRecord = !$this->announcementId;

        if ($this->announcementId) {
            $announcement = Announcement::query()->findOrFail($this->announcementId);
            $currentData = collect($announcement->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.page_updated'),
                    'type' => 'success'
                ];
            } else {
                $announcement->update($data);
                log_activity($announcement, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.page_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $announcement = Announcement::query()->create($data);
            $this->announcementId = $announcement->announcement_id;
            log_activity($announcement, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('admin.page_created'),
                'type' => 'success'
            ];
        }

        // Spatie Media Library - Featured Image Upload
        if ($this->featuredImage) {
            $announcement->addMedia($this->featuredImage->getRealPath())
                ->usingName(pathinfo($this->featuredImage->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($this->featuredImage->getClientOriginalName())
                ->toMediaCollection('featured_image');

            Log::info('📸 Featured image uploaded', [
                'announcement_id' => $announcement->announcement_id,
                'filename' => $this->featuredImage->getClientOriginalName()
            ]);

            $this->featuredImage = null; // Reset after upload
        }

        // Spatie Media Library - Gallery Images Upload
        if (!empty($this->galleryImages)) {
            foreach ($this->galleryImages as $image) {
                $announcement->addMedia($image->getRealPath())
                    ->usingName(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME))
                    ->usingFileName($image->getClientOriginalName())
                    ->toMediaCollection('gallery');
            }

            Log::info('🖼️ Gallery images uploaded', [
                'announcement_id' => $announcement->announcement_id,
                'count' => count($this->galleryImages)
            ]);

            $this->galleryImages = []; // Reset after upload
        }

        // Reload existing gallery for UI
        $this->existingGallery = gallery($announcement);

        Log::info('🎯 Save method tamamlanıyor', [
            'announcementId' => $this->announcementId,
            'redirect' => $redirect
        ]);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.announcement.index');
        }

        // Yeni kayıt oluşturulduysa, edit URL'ine yönlendir (medya upload için ID gerekli)
        if ($isNewRecord && isset($announcement)) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.announcement.manage', ['id' => $announcement->announcement_id]);
        }

        $this->dispatch('toast', $toast);

        // SEO VERİLERİNİ KAYDET - Universal SEO Tab Component'e event gönder
        $this->dispatch('announcement-saved', announcementId: $this->announcementId);

        Log::info('✅ Save method başarıyla tamamlandı', [
            'announcementId' => $this->announcementId
        ]);

        if ($resetForm && !$this->announcementId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    /**
     * Delete featured image
     */
    public function deleteFeaturedImage()
    {
        if (!$this->announcementId) {
            return;
        }

        $announcement = Announcement::query()->find($this->announcementId);
        if ($announcement && $announcement->hasMedia('featured_image')) {
            $announcement->clearMediaCollection('featured_image');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('announcement::admin.media.featured_deleted'),
                'type' => 'success'
            ]);

            Log::info('🗑️ Featured image deleted', [
                'announcement_id' => $this->announcementId
            ]);
        }
    }

    /**
     * Delete gallery image
     */
    public function deleteGalleryImage($mediaId)
    {
        try {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);

            // Security: Check media belongs to this announcement
            if ($media->model_id != $this->announcementId || $media->model_type != Announcement::class) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => __('admin.unauthorized'),
                    'type' => 'error'
                ]);
                return;
            }

            $media->delete();

            // Reload existing gallery
            $announcement = Announcement::query()->find($this->announcementId);
            $this->existingGallery = gallery($announcement);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('announcement::admin.media.gallery_image_deleted'),
                'type' => 'success'
            ]);

            Log::info('🗑️ Gallery image deleted', [
                'announcement_id' => $this->announcementId,
                'media_id' => $mediaId
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Set featured image from gallery
     */
    public function setFeaturedFromGallery($mediaId)
    {
        try {
            if (!$this->announcementId) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => __('admin.save_first'),
                    'type' => 'error'
                ]);
                return;
            }

            $announcement = Announcement::query()->find($this->announcementId);
            if (!$announcement) {
                return;
            }

            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);

            // Security: Check media belongs to this announcement and is in gallery
            if ($media->model_id != $this->announcementId
                || $media->model_type != Announcement::class
                || $media->collection_name != 'gallery') {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => __('admin.unauthorized'),
                    'type' => 'error'
                ]);
                return;
            }

            // Copy gallery image to featured_image collection
            $announcement->clearMediaCollection('featured_image');
            $announcement->copyMedia($media->getPath())
                ->toMediaCollection('featured_image');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('announcement::admin.media.featured_set_from_gallery'),
                'type' => 'success'
            ]);

            Log::info('📸 Featured image set from gallery', [
                'announcement_id' => $this->announcementId,
                'media_id' => $mediaId
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);

            Log::error('Featured image set failed', [
                'announcement_id' => $this->announcementId,
                'media_id' => $mediaId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update gallery order
     */
    public function updateGalleryOrder($list)
    {
        try {
            if (!$this->announcementId) {
                return;
            }

            foreach ($list as $item) {
                $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($item['id']);
                if ($media && $media->model_id == $this->announcementId) {
                    $media->order_column = $item['order'];
                    $media->save();
                }
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('announcement::admin.media.gallery_order_updated'),
                'type' => 'success'
            ]);

            Log::info('🔄 Gallery order updated', [
                'announcement_id' => $this->announcementId,
                'items_count' => count($list)
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);

            Log::error('Gallery order update failed', [
                'announcement_id' => $this->announcementId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        // Load existing gallery for display
        $announcement = null;
        if ($this->announcementId) {
            $announcement = Announcement::query()->find($this->announcementId);
            if ($announcement) {
                $this->existingGallery = gallery($announcement);
            }
        }

        return view('announcement::admin.livewire.announcement-manage-component', [
            'announcement' => $announcement,
            'jsVariables' => [
                'currentAnnouncementId' => $this->announcementId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    // =================================
    // GLOBAL AI CONTENT GENERATION TRAIT IMPLEMENTATION
    // =================================

    public function getEntityType(): string
    {
        return 'announcement';
    }

    public function getTargetFields(array $params): array
    {
        $pageFields = [
            'title' => 'string',
            'body' => 'html',
            'excerpt' => 'text',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $pageFields[$params['target_field']] ?? 'html'];
        }

        return $pageFields;
    }

    public function getModuleInstructions(): string
    {
        return __('announcement::admin.ai_content_instructions');
    }
}
