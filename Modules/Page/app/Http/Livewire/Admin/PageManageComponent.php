<?php

namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Models\PageTranslation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;
use App\Services\GlobalTabService;
use Modules\LanguageManagement\App\Models\TenantLanguage;

/**
 * PAGE MANAGE COMPONENT - REFACTORED VERSION
 * Pattern: A1 CMS Universal System
 *
 * Universal Component'leri kullanarak refactor edilmiş clean version
 * Sadece Page CRUD işlemlerini yönetir, ortak özellikler universal component'lerde
 *
 * @method void dispatch(string $event, mixed ...$params)
 * @method mixed validate(array $rules = [], array $messages = [], array $attributes = [])
 * @method void reset(...$properties)
 * @method void skipRender()
 * @method void fill(array $values)
 * @method void validateOnly(string $field, array $rules = null, array $messages = [], array $attributes = [])
 * @property-read \Illuminate\Contracts\Auth\Authenticatable|null $user
 */
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
        'tabSwitched' => 'handleTabSwitch',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    // Dependency Injection Boot
    public function boot()
    {
        // PageService'i initialize et
        if (class_exists(\Modules\Page\App\Services\PageService::class)) {
            $this->pageService = app(\Modules\Page\App\Services\PageService::class);
        }

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
        // Dil bilgileri - UniversalLanguageSwitcher'dan gelecek
        $this->availableLanguages = array_column(available_tenant_languages(), 'code');
        $this->currentLanguage = get_tenant_default_locale();

        // Tab bilgileri - UniversalTabSystem'den gelecek
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
     * Dil değişikliğini handle et - JavaScript'ten çağrılabilir
     */
    public function handleLanguageSwitch($language)
    {
        if (is_array($language)) {
            $language = $language['language'] ?? $language[0] ?? 'tr';
        }

        $this->currentLanguage = $language;

        Log::info('🎯 PageManage - Dil değişti (handleLanguageSwitch)', [
            'new_language' => $language
        ]);

        // NOT: Event dispatch KALDIRILDI - Pure jQuery ile client-side yapıyoruz
        // Livewire morph'ları gereksiz yere tab içeriğini bozuyor
    }

    /**
     * Tab değişikliğini handle et (UniversalTabSystem'den)
     */
    public function handleTabSwitch($data)
    {
        $this->activeTab = $data['newTab'] ?? $this->activeTab;

        Log::info('📑 PageManage - Tab değişti', [
            'new_tab' => $this->activeTab
        ]);
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
        // PageService varsa kullan, yoksa doğrudan veritabanından çek
        if ($this->pageService) {
            $formData = $this->pageService->preparePageForForm($id, $this->currentLanguage);
            $page = $formData['page'] ?? null;
            $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];
        } else {
            // Fallback: doğrudan veritabanından çek
            $page = Page::query()->find($id);
            $this->tabCompletionStatus = [];
        }

        if ($page) {
            // Dil-neutral alanlar
            $this->inputs = $page->only(['css', 'js', 'is_active', 'is_homepage']);

            // Çoklu dil alanları
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $page->getTranslated('title', $lang) ?? '',
                    'body' => $page->getTranslated('body', $lang) ?? '',
                    'slug' => $page->getTranslated('slug', $lang) ?? '',
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

            // SEO boş başlat
            $this->seoDataCache[$lang] = $this->getEmptySeoData();
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
     */
    protected function getMainLanguage()
    {
        // Önce is_main_language=true olan dili bul
        $mainLang = \Modules\LanguageManagement\App\Models\TenantLanguage::query()
            ->where('is_active', true)
            ->where('is_main_language', true)
            ->value('code');

        // Yoksa is_default=true olan dili bul
        if (!$mainLang) {
            $mainLang = \Modules\LanguageManagement\App\Models\TenantLanguage::query()
                ->where('is_active', true)
                ->where('is_default', true)
                ->value('code');
        }

        // Hiçbiri yoksa fallback olarak tr
        return $mainLang ?? 'tr';
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

    public function save($redirect = false, $resetForm = false)
    {
        // TinyMCE içeriğini senkronize et
        $this->dispatch('sync-tinymce-content');

        Log::info('🚀 SAVE METHOD BAŞLADI', [
            'pageId' => $this->pageId,
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
                'title' => 'Validation Hatası',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
            return;
        }

        // JSON formatında çoklu dil verilerini hazırla
        $multiLangData = [];
        foreach (['title', 'slug', 'body'] as $field) {
            $multiLangData[$field] = [];
            foreach ($this->availableLanguages as $lang) {
                $value = $this->multiLangInputs[$lang][$field] ?? '';

                // HTML body güvenlik kontrolü
                if ($field === 'body' && !empty(trim($value))) {
                    $htmlValidation = \App\Services\SecurityValidationService::validateHtml($value);
                    if (!$htmlValidation['valid']) {
                        $this->dispatch('toast', [
                            'title' => __('admin.error'),
                            'message' => "HTML Güvenlik Hatası ({$lang}): " . implode(', ', $htmlValidation['errors']),
                            'type' => 'error',
                        ]);
                        return;
                    }
                    $value = $htmlValidation['clean_code'];
                }

                // Slug işleme - SlugHelper kullan
                if ($field === 'slug') {
                    if (empty($value) && !empty($this->multiLangInputs[$lang]['title'])) {
                        // Boş slug'lar için title'dan oluştur
                        $value = SlugHelper::generateFromTitle(
                            Page::class,
                            $this->multiLangInputs[$lang]['title'],
                            $lang,
                            'slug',
                            'page_id',
                            $this->pageId
                        );
                    } elseif (!empty($value)) {
                        // Dolu slug'lar için unique kontrolü yap
                        $value = SlugHelper::generateUniqueSlug(
                            Page::class,
                            $value,
                            $lang,
                            'slug',
                            'page_id',
                            $this->pageId
                        );
                    }
                }

                if (!empty($value)) {
                    $multiLangData[$field][$lang] = $value;
                }
            }
        }

        // CSS/JS güvenlik kontrolü
        $safeInputs = $this->inputs;

        // CSS güvenlik doğrulaması
        if (!empty(trim($this->inputs['css']))) {
            $cssValidation = \App\Services\SecurityValidationService::validateCss($this->inputs['css']);
            if (!$cssValidation['valid']) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => 'CSS Güvenlik Hatası: ' . implode(', ', $cssValidation['errors']),
                    'type' => 'error',
                ]);
                return;
            }
            $safeInputs['css'] = $cssValidation['clean_code'];
        } else {
            $safeInputs['css'] = '';
        }

        // JS güvenlik doğrulaması
        if (!empty(trim($this->inputs['js']))) {
            $jsValidation = \App\Services\SecurityValidationService::validateJs($this->inputs['js']);
            if (!$jsValidation['valid']) {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => 'JavaScript Güvenlik Hatası: ' . implode(', ', $jsValidation['errors']),
                    'type' => 'error',
                ]);
                return;
            }
            $safeInputs['js'] = $jsValidation['clean_code'];
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
            log_activity($page, 'oluşturuldu');

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
        $this->dispatch('page-saved', pageId: $this->pageId);

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
            ]
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
        return 'Sayfa içerikleri üretimi. SEO uyumlu, kullanıcı dostu ve kapsamlı sayfa içerikleri oluştur.';
    }
}