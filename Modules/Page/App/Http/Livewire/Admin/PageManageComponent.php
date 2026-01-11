<?php

namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

#[Layout('admin.layout')]
class PageManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $pageId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'css' => '',
        'js' => '',
        'is_active' => true,
        'is_homepage' => false,
    ];

    public $studioEnabled = false;

    // Universal Component Data
    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = []; // Dil adları (native_name)
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    // SOLID Dependencies
    protected $pageService;

    /**
     * Get current page model
     */
    #[Computed]
    public function currentPage()
    {
        if (!$this->pageId) {
            return null;
        }

        return Page::query()->find($this->pageId);
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
        // PageService'i initialize et (her zaman var)
        $this->pageService = app(\Modules\Page\App\Services\PageService::class);

        // Layout sections
        view()->share('pretitle', __('page::admin.page_management'));
        view()->share('title', __('page::admin.pages'));
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
            $this->pageId = $id;
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
        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('page');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('page');
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
                                Page::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'page_id',
                                $this->pageId
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
        // PageService her zaman var, fallback gereksiz
        $formData = $this->pageService->preparePageForForm($id, $this->currentLanguage);
        $page = $formData['page'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($page) {
            // Dil-neutral alanlar
            $this->inputs = $page->only(['css', 'js', 'is_active', 'is_homepage']);

            // Çoklu dil alanları - FALLBACK KAPALI (kullanıcı tüm dilleri boşaltabilsin)
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $page->getTranslated('title', $lang, false) ?? '',
                    'body' => $page->getTranslated('body', $lang, false) ?? '',
                    'slug' => $page->getTranslated('slug', $lang, false) ?? '',
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
            'inputs.css' => 'nullable|string',
            'inputs.js' => 'nullable|string',
            'inputs.is_active' => 'boolean',
            'inputs.is_homepage' => 'boolean',
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

        // CSS validation
        if (!empty(trim($this->inputs['css']))) {
            $result = \App\Services\SecurityValidationService::validateCss($this->inputs['css']);
            if (!$result['valid']) {
                $errors[] = 'CSS: ' . implode(', ', $result['errors']);
            } else {
                $validated['css'] = $result['clean_code'];
            }
        }

        // JS validation
        if (!empty(trim($this->inputs['js']))) {
            $result = \App\Services\SecurityValidationService::validateJs($this->inputs['js']);
            if (!$result['valid']) {
                $errors[] = 'JavaScript: ' . implode(', ', $result['errors']);
            } else {
                $validated['js'] = $result['clean_code'];
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
            Page::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->pageId
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

        Log::info('🚀🚀🚀 PAGE SAVE METHOD BAŞLADI 🚀🚀🚀', [
            'pageId' => $this->pageId,
            'redirect' => $redirect,
            'currentLanguage' => $this->currentLanguage,
            'inputs' => $this->inputs,
            'multiLangInputs' => $this->multiLangInputs
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

        // Safe inputs - CSS ve JS validasyondan geldi
        $safeInputs = $this->inputs;
        if (isset($validation['data']['css'])) {
            $safeInputs['css'] = $validation['data']['css'];
        } else {
            $safeInputs['css'] = '';
        }
        if (isset($validation['data']['js'])) {
            $safeInputs['js'] = $validation['data']['js'];
        } else {
            $safeInputs['js'] = '';
        }

        $data = array_merge($safeInputs, $multiLangData);

        $currentPage = $this->pageId ? Page::query()->find($this->pageId) : null;
        if (($this->inputs['is_homepage'] || ($currentPage && $currentPage->is_homepage)) && isset($data['is_active']) && $data['is_active'] == false) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('admin.homepage_cannot_be_deactivated'),
                'type' => 'warning',
            ]);
            return;
        }

        if ($this->pageId) {
            $page = Page::query()->findOrFail($this->pageId);
            $currentData = collect($page->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.page_updated'),
                    'type' => 'success'
                ];
            } else {
                $page->update($data);
                log_activity($page, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.page_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $page = Page::query()->create($data);
            $this->pageId = $page->page_id;
            log_activity($page, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('admin.page_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'pageId' => $this->pageId,
            'redirect' => $redirect
        ]);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.page.index');
        }

        $this->dispatch('toast', $toast);

        // SEO VERİLERİNİ KAYDET - Universal SEO Tab Component'e event gönder
        Log::info('🚀 PAGE-SAVED EVENT GÖNDERİLİYOR', [
            'pageId' => $this->pageId,
            'modelId' => $this->pageId
        ]);

        // Global event dispatch - tüm component'ler dinleyebilir
        $this->dispatch('page-saved', $this->pageId);

        Log::info('✅ Event dispatched');

        Log::info('✅ Save method başarıyla tamamlandı', [
            'pageId' => $this->pageId
        ]);

        if ($resetForm && !$this->pageId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('page::admin.livewire.page-manage-component', [
            'jsVariables' => [
                'currentPageId' => $this->pageId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ],
            'page' => $this->pageId ? Page::find($this->pageId) : null
        ]);
    }

    // =================================
    // GLOBAL AI CONTENT GENERATION TRAIT IMPLEMENTATION
    // =================================

    public function getEntityType(): string
    {
        return 'page';
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
        return __('page::admin.ai_content_instructions');
    }
}
