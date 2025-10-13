<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use App\Helpers\SlugHelper;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopProductService;
use Modules\Shop\App\Services\ShopCategoryService;
use Modules\Shop\App\Services\ShopBrandService;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class ShopProductManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $productId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'is_active' => true,
        'category_id' => null,
        'brand_id' => null,
        'sku' => null,
        'product_type' => 'physical',
        'condition' => 'new',
        'price_on_request' => false,
        'base_price' => null,
        'compare_at_price' => null,
        'currency' => 'TRY',
        'sort_order' => 0,
    ];

    // Custom JSON Fields (Tenant-defined categories)
    public $customJsonFields = [];

    public $studioEnabled = false;

    // Universal Component Data
    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = []; // Dil adları (native_name)
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    // SOLID Dependencies
    protected $productService;
    protected $categoryService;
    protected $brandService;

    /**
     * Get current product model
     */
    #[Computed]
    public function currentProduct()
    {
        if (!$this->productId) {
            return null;
        }

        return ShopProduct::query()->find($this->productId);
    }

    /**
     * Get active categories for dropdown
     */
    #[Computed]
    public function activeCategories()
    {
        return $this->categoryService->getActiveCategories();
    }

    /**
     * Get active brands for dropdown
     */
    #[Computed]
    public function activeBrands()
    {
        return $this->brandService->getActiveBrands();
    }

    // Livewire Listeners - Universal component'lerden gelen event'ler
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    // Dependency Injection Boot
    public function boot(
        ShopProductService $productService,
        ShopCategoryService $categoryService,
        ShopBrandService $brandService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;

        // Layout sections
        view()->share('pretitle', __('shop::admin.product_management'));
        view()->share('title', __('shop::admin.products'));
    }

    public function updated($propertyName)
    {
        // Tab completion status güncelleme - Universal Tab System'e bildir
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    public function mount($id = null)
    {
        // Dependencies initialize
        $this->boot(
            app(ShopProductService::class),
            app(ShopCategoryService::class),
            app(ShopBrandService::class)
        );

        // Universal Component'lerden initial data al
        $this->initializeUniversalComponents();

        // Ürün verilerini yükle
        if ($id) {
            $this->productId = (int) $id;
            $this->loadProductData((int) $id);
        } else {
            $this->initializeEmptyInputs();
        }

        // Studio modül kontrolü - config'den kontrol et
        $studioConfig = config('shop.integrations.studio', []);
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
        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('shop');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('shop');
    }

    /**
     * Dil değişikliğini handle et (UniversalLanguageSwitcher'dan)
     */
    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 ShopProductManage - Dil değişti', [
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
                                ShopProduct::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'product_id',
                                $this->productId
                            );
                        }
                    }
                }
            }

            // Çevirileri veritabanına kaydet
            $this->save();

            Log::info('✅ ShopProductManage - Çeviri sonuçları alındı ve kaydedildi', [
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

                Log::info('✅ ShopProductManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    /**
     * Ürün verilerini yükle
     */
    protected function loadProductData($id)
    {
        $formData = $this->productService->prepareProductForForm($id, $this->currentLanguage);
        $product = $formData['product'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($product) {
            // Dil-neutral alanlar
            $this->inputs = array_merge($this->inputs, [
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'sku' => $product->sku,
                'product_type' => $product->product_type,
                'condition' => $product->condition,
                'price_on_request' => (bool) $product->price_on_request,
                'base_price' => $product->base_price,
                'compare_at_price' => $product->compare_at_price,
                'currency' => $product->currency,
                'is_active' => (bool) $product->is_active,
            ]);

            // Çoklu dil alanları - FALLBACK KAPALI (kullanıcı tüm dilleri boşaltabilsin)
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $product->getTranslated('title', $lang, false) ?? '',
                    'short_description' => $product->getTranslated('short_description', $lang, false) ?? '',
                    'body' => $product->getTranslated('body', $lang, false) ?? '',
                    'slug' => $product->getTranslated('slug', $lang, false) ?? '',
                ];
            }


            // Custom JSON Fields yükle (boş array garantisi)
            if (is_array($product->custom_json_fields)) {
                $this->customJsonFields = $product->custom_json_fields;
            } elseif ($product->custom_json_fields) {
                $this->customJsonFields = json_decode($product->custom_json_fields, true) ?: [];
            } else {
                $this->customJsonFields = [];
            }
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
                'short_description' => '',
                'body' => '',
                'slug' => '',
            ];
        }

        // Custom JSON fields'ı başlat
        $this->customJsonFields = [];
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
        return get_tenant_default_locale();
    }

    protected function rules()
    {
        $rules = [
            'inputs.is_active' => 'boolean',
            'inputs.category_id' => 'nullable|exists:shop_categories,category_id',
            'inputs.brand_id' => 'nullable|exists:shop_brands,brand_id',
            'inputs.sku' => 'required|string|max:191',
            'inputs.product_type' => 'required|in:physical,digital,service',
            'inputs.condition' => 'required|in:new,used,refurbished',
            'inputs.currency' => 'required|string|size:3',
            'inputs.price_on_request' => 'boolean',
            'inputs.base_price' => 'nullable|numeric|min:0',
            'inputs.compare_at_price' => 'nullable|numeric|min:0',
        ];

        // Çoklu dil alanları - ana dil mecburi, diğerleri opsiyonel
        $mainLanguage = $this->getMainLanguage();
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $mainLanguage ? 'required|min:3|max:191' : 'nullable|min:3|max:191';
            $rules["multiLangInputs.{$lang}.short_description"] = 'nullable|string|max:500';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
        }

        return $rules;
    }

    protected $messages = [
        'inputs.is_active.boolean' => 'Aktif durumu geçerli bir değer olmalıdır',
        'inputs.sku.required' => 'SKU alanı zorunludur',
        'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
        'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Başlık en fazla 191 karakter olabilir',
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

        // HTML validation (her dil için)
        foreach ($this->availableLanguages as $lang) {
            $longDesc = $this->multiLangInputs[$lang]['body'] ?? '';
            if (!empty(trim($longDesc))) {
                $result = \App\Services\SecurityValidationService::validateHtml($longDesc);
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
     * Çoklu dil verilerini hazırla (title, slug, descriptions)
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
            ShopProduct::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->productId
        );

        // Short description
        $multiLangData['short_description'] = [];
        foreach ($this->availableLanguages as $lang) {
            $shortDesc = $this->multiLangInputs[$lang]['short_description'] ?? '';
            if (!empty($shortDesc)) {
                $multiLangData['short_description'][$lang] = $shortDesc;
            }
        }

        // Long description (validated'dan)
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
            'productId' => $this->productId,
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

        // Çoklu dil verilerini hazırla
        $multiLangData = $this->prepareMultiLangData($validation['data']);

        // Safe inputs
        $safeInputs = $this->inputs;

        // JSON inputs'ları ekle (SADECE custom_json_fields - migration ile statik alanlar kaldırıldı)
        $jsonData = [
            'custom_json_fields' => !empty($this->customJsonFields) ? $this->customJsonFields : null,
        ];

        $data = array_merge($safeInputs, $multiLangData, $jsonData);

        // Yeni kayıt mı kontrol et
        $isNewRecord = !$this->productId;

        if ($this->productId) {
            $product = ShopProduct::query()->findOrFail($this->productId);
            $currentData = collect($product->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('shop::admin.product_updated'),
                    'type' => 'success'
                ];
            } else {
                $product->update($data);
                log_activity($product, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('shop::admin.product_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $product = ShopProduct::query()->create($data);
            $this->productId = $product->product_id;
            log_activity($product, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('shop::admin.product_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'productId' => $this->productId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        // Toast mesajı göster
        $this->dispatch('toast', $toast);

        // SEO VERİLERİNİ KAYDET - Universal SEO Tab Component'e event gönder
        $this->dispatch('page-saved', $this->productId);

        // Redirect istendiyse
        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.shop.products.index');
        }

        // Yeni kayıt oluşturulduysa - medya event'ini dispatch et ve redirect
        if ($isNewRecord && isset($product)) {
            // UniversalMediaComponent'e save event'i gönder
            $this->dispatch('product-saved', $product->product_id);

            session()->flash('toast', $toast);
            return redirect()->route('admin.shop.products.manage', ['id' => $product->product_id]);
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'productId' => $this->productId
        ]);

        if ($resetForm && !$this->productId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('shop::admin.livewire.product-manage-component', [
            'jsVariables' => [
                'currentProductId' => $this->productId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    // =================================
    // GLOBAL AI CONTENT GENERATION TRAIT IMPLEMENTATION
    // =================================

    public function getEntityType(): string
    {
        return 'shop_product';
    }

    public function getTargetFields(array $params): array
    {
        $productFields = [
            'title' => 'string',
            'short_description' => 'text',
            'body' => 'html',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $productFields[$params['target_field']] ?? 'html'];
        }

        return $productFields;
    }

    public function getModuleInstructions(): string
    {
        return __('shop::admin.ai_content_instructions');
    }
}
