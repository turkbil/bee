<?php

namespace Modules\Service\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Service\App\Models\Service;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

#[Layout('admin.layout')]
class ServiceManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $serviceId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'is_active' => true,
        'service_category_id' => null,
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
    protected $serviceService;

    /**
     * Get current service model
     */
    #[Computed]
    public function currentPage()
    {
        if (!$this->serviceId) {
            return null;
        }

        return Service::query()->find($this->serviceId);
    }

    /**
     * Get active categories for dropdown
     */
    #[Computed]
    public function activeCategories()
    {
        return \Modules\Service\App\Models\ServiceCategory::active()
            ->orderBy('sort_order')
            ->get();
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
        // ServiceService'i initialize et (her zaman var)
        $this->serviceService = app(\Modules\Service\App\Services\ServiceService::class);

        // Layout sections
        view()->share('pretitle', __('service::admin.service_management'));
        view()->share('title', __('service::admin.services'));
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
            $this->serviceId = $id;
            $this->loadPageData($id);
        } else {
            $this->initializeEmptyInputs();
        }

        // Studio modül kontrolü - config'den kontrol et
        $studioConfig = config('service.integrations.studio', []);
        $this->studioEnabled = ($studioConfig['enabled'] ?? false) && class_exists($studioConfig['component'] ?? '');

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
        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('service');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('service');
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
                                Service::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'service_id',
                                $this->serviceId
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
        // ServiceService her zaman var, fallback gereksiz
        $formData = $this->serviceService->prepareServiceForForm($id, $this->currentLanguage);
        $service = $formData['service'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($service) {
            // Dil-neutral alanlar
            $this->inputs = $service->only(['is_active', 'service_category_id']);

            // Çoklu dil alanları - FALLBACK KAPALI (kullanıcı tüm dilleri boşaltabilsin)
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $service->getTranslated('title', $lang, false) ?? '',
                    'body' => $service->getTranslated('body', $lang, false) ?? '',
                    'slug' => $service->getTranslated('slug', $lang, false) ?? '',
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
            'inputs.service_category_id' => 'nullable|exists:service_categories,category_id',
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
        'inputs.is_active.boolean' => 'Aktif durumu geçerli bir değer olmalıdır',
        'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
        'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Başlık en fazla 255 karakter olabilir',
        'multiLangInputs.*.body.string' => 'İçerik metin formatında olmalıdır',
        'multiLangInputs.*.slug.string' => 'Slug metin formatında olmalıdır',
        'multiLangInputs.*.slug.max' => 'Slug en fazla 255 karakter olabilir',
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
            Service::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->serviceId
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
            'serviceId' => $this->serviceId,
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
        $isNewRecord = !$this->serviceId;

        if ($this->serviceId) {
            $service = Service::query()->findOrFail($this->serviceId);
            $currentData = collect($service->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.service_updated'),
                    'type' => 'success'
                ];
            } else {
                $service->update($data);
                log_activity($service, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.service_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $service = Service::query()->create($data);
            $this->serviceId = $service->service_id;
            log_activity($service, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('admin.service_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'serviceId' => $this->serviceId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        // Toast mesajı göster
        $this->dispatch('toast', $toast);

        // SEO VERİLERİNİ KAYDET - Universal SEO Tab Component'e event gönder
        $this->dispatch('page-saved', $this->serviceId);

        // Redirect istendiyse
        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.service.index');
        }

        // Yeni kayıt oluşturulduysa - medya event'ini dispatch et ve redirect
        if ($isNewRecord && isset($service)) {
            // UniversalMediaComponent'e save event'i gönder
            $this->dispatch('service-saved', $service->service_id);

            session()->flash('toast', $toast);
            return redirect()->route('admin.service.manage', ['id' => $service->service_id]);
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'serviceId' => $this->serviceId
        ]);

        if ($resetForm && !$this->serviceId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }


    public function render()
    {
        return view('service::admin.livewire.service-manage-component', [
            'jsVariables' => [
                'currentServiceId' => $this->serviceId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    // =================================
    // GLOBAL AI CONTENT GENERATION TRAIT IMPLEMENTATION
    // =================================

    public function getEntityType(): string
    {
        return 'service';
    }

    public function getTargetFields(array $params): array
    {
        $serviceFields = [
            'title' => 'string',
            'body' => 'html',
            'excerpt' => 'text',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $serviceFields[$params['target_field']] ?? 'html'];
        }

        return $serviceFields;
    }

    public function getModuleInstructions(): string
    {
        return __('service::admin.ai_content_instructions');
    }
}
