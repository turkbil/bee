<?php

namespace Modules\Blog\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Blog\App\Models\Blog;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

#[Layout('admin.layout')]
class BlogManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $blogId;

    // Çoklu dil inputs
    public $multiLangInputs = [];

    // Dil-neutral inputs
    public $inputs = [
        'is_active' => true,
        'blog_category_id' => null,
        'published_at' => null,
        'is_featured' => false,
        'tags' => [],
        'faq_data' => null,
        'howto_data' => null,
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
    protected $blogService;

    /**
     * Get current blog model
     */
    #[Computed]
    public function currentPage()
    {
        if (!$this->blogId) {
            return null;
        }

        return Blog::query()->find($this->blogId);
    }

    /**
     * Get active categories for dropdown
     */
    #[Computed]
    public function activeCategories()
    {
        return \Modules\Blog\App\Models\BlogCategory::active()
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
        // BlogService'i initialize et (her zaman var)
        $this->blogService = app(\Modules\Blog\App\Services\BlogService::class);

        // Layout sections
        view()->share('pretitle', __('blog::admin.blog_management'));
        view()->share('title', __('blog::admin.blogs'));
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
            $this->blogId = $id;
            $this->loadPageData($id);
        } else {
            $this->initializeEmptyInputs();
        }

        // Studio modül kontrolü - config'den kontrol et
        $studioConfig = config('blog.integrations.studio', []);
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
        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('blog');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('blog');
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
                                Blog::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'blog_id',
                                $this->blogId
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
        // BlogService her zaman var, fallback gereksiz
        $formData = $this->blogService->prepareBlogForForm($id, $this->currentLanguage);
        $blog = $formData['blog'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($blog) {
            // Dil-neutral alanlar
            $this->inputs = $blog->only([
                'is_active',
                'blog_category_id',
                'published_at',
                'is_featured',
                'faq_data',
                'howto_data'
            ]);

            // FAQ ve HowTo data - locale-aware flatten (Alpine.js visual editor için)
            // Veri multi-language nested olarak saklanıyor: {"question": {"tr": "..."}, ...}
            // Form için flatten ediyoruz: {"question": "...", ...}
            $locale = $this->currentLanguage;

            if (!empty($this->inputs['faq_data']) && is_array($this->inputs['faq_data'])) {
                $this->inputs['faq_data'] = array_map(function($faq) use ($locale) {
                    return [
                        'question' => is_array($faq['question'] ?? null)
                            ? ($faq['question'][$locale] ?? reset($faq['question']) ?: '')
                            : ($faq['question'] ?? ''),
                        'answer' => is_array($faq['answer'] ?? null)
                            ? ($faq['answer'][$locale] ?? reset($faq['answer']) ?: '')
                            : ($faq['answer'] ?? ''),
                        'icon' => $faq['icon'] ?? 'fa-question-circle'
                    ];
                }, $this->inputs['faq_data']);
            }

            if (!empty($this->inputs['howto_data']) && is_array($this->inputs['howto_data'])) {
                $howto = $this->inputs['howto_data'];
                $this->inputs['howto_data'] = [
                    'name' => is_array($howto['name'] ?? null)
                        ? ($howto['name'][$locale] ?? reset($howto['name']) ?: '')
                        : ($howto['name'] ?? ''),
                    'description' => is_array($howto['description'] ?? null)
                        ? ($howto['description'][$locale] ?? reset($howto['description']) ?: '')
                        : ($howto['description'] ?? ''),
                    'steps' => array_map(function($step) use ($locale) {
                        return [
                            'name' => is_array($step['name'] ?? null)
                                ? ($step['name'][$locale] ?? reset($step['name']) ?: '')
                                : ($step['name'] ?? ''),
                            'text' => is_array($step['text'] ?? null)
                                ? ($step['text'][$locale] ?? reset($step['text']) ?: '')
                                : ($step['text'] ?? ''),
                            'icon' => $step['icon'] ?? 'fa-cog'
                        ];
                    }, $howto['steps'] ?? [])
                ];
            }

            $this->inputs['tags'] = $blog->tag_list;

            // Published_at'i string formatına çevir (date input için)
            if ($this->inputs['published_at']) {
                $this->inputs['published_at'] = $this->inputs['published_at']->format('Y-m-d\TH:i');
            }

            // Çoklu dil alanları - FALLBACK KAPALI (kullanıcı tüm dilleri boşaltabilsin)
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $blog->getTranslated('title', $lang, false) ?? '',
                    'body' => $blog->getTranslated('body', $lang, false) ?? '',
                    'excerpt' => $blog->getTranslated('excerpt', $lang, false) ?? '',
                    'slug' => $blog->getTranslated('slug', $lang, false) ?? '',
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
                'excerpt' => '',
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
            'inputs.blog_category_id' => 'nullable|exists:blog_categories,category_id',
            'inputs.published_at' => 'nullable|date',
            'inputs.is_featured' => 'boolean',
            'inputs.tags' => 'nullable|array',
        ];

        // Çoklu dil alanları - ana dil mecburi, diğerleri opsiyonel
        $mainLanguage = $this->getMainLanguage();
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $mainLanguage ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
            $rules["multiLangInputs.{$lang}.excerpt"] = 'nullable|string|max:500';
        }

        return $rules;
    }

    protected $messages = [
        'inputs.is_active.boolean' => 'Aktif durumu geçerli bir değer olmalıdır',
        'inputs.published_at.date' => 'Yayın tarihi geçerli bir tarih olmalıdır',
        'inputs.is_featured.boolean' => 'Öne çıkan durumu geçerli bir değer olmalıdır',
        'inputs.tags.array' => 'Etiketler dizi formatında olmalıdır',
        'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
        'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Başlık en fazla 255 karakter olabilir',
        'multiLangInputs.*.body.string' => 'İçerik metin formatında olmalıdır',
        'multiLangInputs.*.excerpt.string' => 'Özet metin formatında olmalıdır',
        'multiLangInputs.*.excerpt.max' => 'Özet en fazla 500 karakter olabilir',
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

        // Excerpt verilerini topla
        $multiLangData['excerpt'] = [];
        foreach ($this->availableLanguages as $lang) {
            $excerpt = $this->multiLangInputs[$lang]['excerpt'] ?? '';
            if (!empty($excerpt)) {
                $multiLangData['excerpt'][$lang] = $excerpt;
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
            Blog::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->blogId
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
            'blogId' => $this->blogId,
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
        $tagNames = $this->normalizeTagInputs();
        unset($safeInputs['tags']);

        // FAQ ve HowTo - JSON decode ve locale-aware unflatten (nested yapıya dönüştür)
        $locale = $this->currentLanguage;

        if (!empty($safeInputs['faq_data'])) {
            // JSON string ise decode et
            if (is_string($safeInputs['faq_data'])) {
                $safeInputs['faq_data'] = json_decode($safeInputs['faq_data'], true) ?: [];
            }

            // Mevcut veriyi al (diğer dilleri korumak için)
            $existingFaq = [];
            if ($this->blogId) {
                $existingBlog = Blog::query()->find($this->blogId);
                $existingFaq = $existingBlog?->faq_data ?? [];
            }

            // Nested yapıya dönüştür (diğer dilleri koru)
            $safeInputs['faq_data'] = array_map(function($faq, $index) use ($locale, $existingFaq) {
                $existing = $existingFaq[$index] ?? [];
                return [
                    'question' => array_merge(
                        is_array($existing['question'] ?? null) ? $existing['question'] : [],
                        [$locale => $faq['question'] ?? '']
                    ),
                    'answer' => array_merge(
                        is_array($existing['answer'] ?? null) ? $existing['answer'] : [],
                        [$locale => $faq['answer'] ?? '']
                    ),
                    'icon' => $faq['icon'] ?? 'fa-question-circle'
                ];
            }, $safeInputs['faq_data'], array_keys($safeInputs['faq_data']));
        }

        if (!empty($safeInputs['howto_data'])) {
            // JSON string ise decode et
            if (is_string($safeInputs['howto_data'])) {
                $safeInputs['howto_data'] = json_decode($safeInputs['howto_data'], true) ?: [];
            }

            // Mevcut veriyi al (diğer dilleri korumak için)
            $existingHowto = [];
            if ($this->blogId) {
                $existingBlog = $existingBlog ?? Blog::query()->find($this->blogId);
                $existingHowto = $existingBlog?->howto_data ?? [];
            }

            $howto = $safeInputs['howto_data'];
            $safeInputs['howto_data'] = [
                'name' => array_merge(
                    is_array($existingHowto['name'] ?? null) ? $existingHowto['name'] : [],
                    [$locale => $howto['name'] ?? '']
                ),
                'description' => array_merge(
                    is_array($existingHowto['description'] ?? null) ? $existingHowto['description'] : [],
                    [$locale => $howto['description'] ?? '']
                ),
                'steps' => array_map(function($step, $index) use ($locale, $existingHowto) {
                    $existing = $existingHowto['steps'][$index] ?? [];
                    return [
                        'name' => array_merge(
                            is_array($existing['name'] ?? null) ? $existing['name'] : [],
                            [$locale => $step['name'] ?? '']
                        ),
                        'text' => array_merge(
                            is_array($existing['text'] ?? null) ? $existing['text'] : [],
                            [$locale => $step['text'] ?? '']
                        ),
                        'icon' => $step['icon'] ?? 'fa-cog'
                    ];
                }, $howto['steps'] ?? [], array_keys($howto['steps'] ?? []))
            ];
        }

        // Published_at'i datetime formatına çevir
        if (!empty($safeInputs['published_at'])) {
            $safeInputs['published_at'] = \Carbon\Carbon::parse($safeInputs['published_at']);
        }

        $data = array_merge($safeInputs, $multiLangData);

        // Yeni kayıt mı kontrol et
        $isNewRecord = !$this->blogId;

        if ($this->blogId) {
            $blog = Blog::query()->findOrFail($this->blogId);
            $currentData = collect($blog->toArray())->only(array_keys($data))->all();
            $currentTagList = collect($blog->tag_list)
                ->map(fn ($tag) => trim((string) $tag))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($data == $currentData && $tagNames === $currentTagList) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.blog_updated'),
                    'type' => 'success'
                ];
            } else {
                $blog->update($data);
                $blog->syncTagsByName($tagNames);
                log_activity($blog, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('admin.blog_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $blog = Blog::query()->create($data);
            $blog->syncTagsByName($tagNames);
            $this->blogId = $blog->blog_id;
            log_activity($blog, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('admin.blog_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'blogId' => $this->blogId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        // Toast mesajı göster
        $this->dispatch('toast', $toast);

        // SEO VERİLERİNİ KAYDET - Universal SEO Tab Component'e event gönder
        $this->dispatch('page-saved', $this->blogId);

        // Redirect istendiyse
        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.blog.index');
        }

        // Yeni kayıt oluşturulduysa - medya event'ini dispatch et ve redirect
        if ($isNewRecord && isset($blog)) {
            // UniversalMediaComponent'e save event'i gönder
            $this->dispatch('blog-saved', $blog->blog_id);

            session()->flash('toast', $toast);
            return redirect()->route('admin.blog.manage', ['id' => $blog->blog_id]);
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'blogId' => $this->blogId
        ]);

        if ($resetForm && !$this->blogId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    /**
     * Etiket girişlerini normalize et.
     *
     * @return array<int, string>
     */
    protected function normalizeTagInputs(): array
    {
        return collect($this->inputs['tags'] ?? [])
            ->filter(fn ($tag) => filled($tag))
            ->map(fn ($tag) => trim((string) $tag))
            ->reject(fn ($tag) => $tag === '')
            ->unique()
            ->values()
            ->all();
    }


    public function render()
    {
        return view('blog::admin.livewire.blog-manage-component', [
            'jsVariables' => [
                'currentBlogId' => $this->blogId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    // =================================
    // GLOBAL AI CONTENT GENERATION TRAIT IMPLEMENTATION
    // =================================

    public function getEntityType(): string
    {
        return 'blog';
    }

    public function getTargetFields(array $params): array
    {
        $blogFields = [
            'title' => 'string',
            'body' => 'html',
            'excerpt' => 'text',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $blogFields[$params['target_field']] ?? 'html'];
        }

        return $blogFields;
    }

    public function getModuleInstructions(): string
    {
        return __('blog::admin.ai_content_instructions');
    }
}
