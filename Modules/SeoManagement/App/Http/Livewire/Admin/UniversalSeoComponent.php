<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\SeoManagement\app\Services\Interfaces\SeoServiceInterface;
use Modules\SeoManagement\app\Services\SchemaGeneratorService;
use App\Services\GlobalTabService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

#[Layout('admin.layout')]
class UniversalSeoComponent extends Component
{
    public ?Model $model = null;
    public string $modelType = '';
    public int $modelId = 0;
    public string $currentLanguage = 'tr';
    public array $availableLanguages = [];
    public string $activeTab = 'basic_seo';
    
    // SEO form data
    public array $seoData = [];
    public array $multiLangInputs = [];
    
    // Tab configuration
    public array $tabConfig = [];
    public array $tabCompletionStatus = [];
    
    private SeoServiceInterface $seoService;
    private GlobalTabService $tabService;
    private SchemaGeneratorService $schemaGenerator;

    public function boot(): void
    {
        $this->seoService = app(SeoServiceInterface::class);
        $this->tabService = app(GlobalTabService::class);
        $this->schemaGenerator = app(SchemaGeneratorService::class);
    }

    public function mount(?string $modelType = null, ?int $modelId = null): void
    {
        if ($modelType && $modelId) {
            $this->modelType = $modelType;
            $this->modelId = $modelId;
            $this->loadModel();
        }

        $this->loadTabConfiguration();
        $this->loadAvailableLanguages();
        $this->loadSeoData();
    }

    public function loadModel(): void
    {
        if (!$this->modelType || !$this->modelId) {
            return;
        }

        // Get model class from config
        $config = config('seomanagement.universal_seo.supported_models');
        $modelClass = $config[$this->modelType]['class'] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            session()->flash('error', 'Desteklenmeyen model türü.');
            return;
        }

        $this->model = $modelClass::find($this->modelId);

        if (!$this->model) {
            session()->flash('error', 'Model bulunamadı.');
        }
    }

    public function loadTabConfiguration(): void
    {
        $this->tabConfig = config('seomanagement.universal_seo.tabs', []);
        
        // Initialize tab completion status
        foreach ($this->tabConfig as $tab) {
            $this->tabCompletionStatus[$tab['key']] = false;
        }
    }

    public function loadAvailableLanguages(): void
    {
        $this->availableLanguages = config('seomanagement.universal_seo.multilingual.supported_languages', ['tr', 'en', 'ar']);
        $this->currentLanguage = config('seomanagement.universal_seo.multilingual.default_language', 'tr');
    }

    public function loadSeoData(): void
    {
        if (!$this->model) {
            $this->initializeEmptySeoData();
            return;
        }

        $seoSettings = $this->seoService->getSeoSettings($this->model);

        if ($seoSettings) {
            $this->seoData = [
                'meta_title' => $seoSettings->meta_title,
                'meta_description' => $seoSettings->meta_description,
                'meta_keywords' => $seoSettings->meta_keywords,
                'og_image' => $seoSettings->og_image,
                'og_type' => $seoSettings->og_type,
                'twitter_card' => $seoSettings->twitter_card,
                'twitter_title' => $seoSettings->twitter_title,
                'twitter_description' => $seoSettings->twitter_description,
                'twitter_image' => $seoSettings->twitter_image,
                'robots_meta' => $seoSettings->robots_meta ?? [],
                'schema_markup' => $seoSettings->schema_markup,
                'focus_keyword' => $seoSettings->focus_keyword,
                'additional_keywords' => $seoSettings->additional_keywords ?? [],
                'status' => $seoSettings->status,
                'priority' => $seoSettings->priority,
            ];

            // Load multi-language data
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $seoSettings->titles[$lang] ?? '',
                    'description' => $seoSettings->descriptions[$lang] ?? '',
                    'keywords' => is_array($seoSettings->keywords[$lang] ?? null) 
                        ? implode(',', $seoSettings->keywords[$lang]) 
                        : ($seoSettings->keywords[$lang] ?? ''),
                    'og_title' => $seoSettings->og_titles[$lang] ?? '',
                    'og_description' => $seoSettings->og_descriptions[$lang] ?? '',
                    'focus_keywords' => is_array($seoSettings->focus_keywords[$lang] ?? null)
                        ? implode(',', $seoSettings->focus_keywords[$lang])
                        : ($seoSettings->focus_keywords[$lang] ?? ''),
                ];
            }
        } else {
            $this->initializeEmptySeoData();
        }

        $this->updateTabCompletionStatus();
    }

    public function initializeEmptySeoData(): void
    {
        $this->seoData = [
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'og_image' => '',
            'og_type' => 'website',
            'twitter_card' => 'summary',
            'twitter_title' => '',
            'twitter_description' => '',
            'twitter_image' => '',
            'robots_meta' => ['index' => true, 'follow' => true],
            'schema_markup' => '',
            'focus_keyword' => '',
            'additional_keywords' => [],
            'status' => 'active',
            'priority_score' => 5,
        ];

        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'description' => '',
                'keywords' => '',
                'og_title' => '',
                'og_description' => '',
                'focus_keywords' => '',
            ];
        }
    }

    public function switchLanguage(string $language): void
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
        }
    }

    public function save(): void
    {
        if (!$this->model) {
            session()->flash('error', 'Model bulunamadı.');
            return;
        }

        $this->validate($this->getValidationRules());

        try {
            // Prepare single-value SEO data
            $seoData = $this->seoData;

            // Prepare multi-language data
            $languageData = [];
            foreach ($this->availableLanguages as $lang) {
                $languageData[$lang] = [
                    'title' => $this->multiLangInputs[$lang]['title'] ?? '',
                    'description' => $this->multiLangInputs[$lang]['description'] ?? '',
                    'keywords' => !empty($this->multiLangInputs[$lang]['keywords']) 
                        ? array_map('trim', explode(',', $this->multiLangInputs[$lang]['keywords']))
                        : [],
                    'og_title' => $this->multiLangInputs[$lang]['og_title'] ?? '',
                    'og_description' => $this->multiLangInputs[$lang]['og_description'] ?? '',
                ];

                if (!empty($this->multiLangInputs[$lang]['focus_keywords'])) {
                    $languageData[$lang]['focus_keywords'] = array_map('trim', explode(',', $this->multiLangInputs[$lang]['focus_keywords']));
                }
            }

            // Update SEO settings
            $this->seoService->updateSeoSettings($this->model, $seoData);
            $this->seoService->updateMultiLanguageSeoData($this->model, $languageData);

            // Calculate SEO score
            $this->seoService->calculateSeoScore($this->model);

            $this->updateTabCompletionStatus();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'SEO ayarları başarıyla güncellendi.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => 'SEO ayarları güncellenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Schema.org otomatik oluştur
     */
    public function generateAutoSchema(): void
    {
        if (!$this->model) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Model bulunamadı. Schema oluşturulamaz.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $schema = $this->schemaGenerator->generateSchema($this->model, $this->currentLanguage);
            $this->seoData['schema_markup'] = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Schema.org otomatik oluşturuldu!',
                'type' => 'success'
            ]);

            $this->updateTabCompletionStatus();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Schema oluşturulurken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updateTabCompletionStatus(): void
    {
        // Basic SEO tab
        $this->tabCompletionStatus['basic_seo'] = !empty($this->multiLangInputs[$this->currentLanguage]['title']) 
            && !empty($this->multiLangInputs[$this->currentLanguage]['description']);

        // Social Media tab
        $this->tabCompletionStatus['social_media'] = !empty($this->multiLangInputs[$this->currentLanguage]['og_title']) 
            || !empty($this->multiLangInputs[$this->currentLanguage]['og_description'])
            || !empty($this->seoData['og_image']);

        // Advanced tab
        $this->tabCompletionStatus['advanced'] = !empty($this->seoData['schema_markup']);
    }

    protected function getValidationRules(): array
    {
        $rules = config('seomanagement.universal_seo.validation_rules', []);
        
        // Add multi-language validation rules
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = 'nullable|string|max:60';
            $rules["multiLangInputs.{$lang}.description"] = 'nullable|string|max:160';
            $rules["multiLangInputs.{$lang}.keywords"] = 'nullable|string|max:255';
            $rules["multiLangInputs.{$lang}.og_title"] = 'nullable|string|max:60';
            $rules["multiLangInputs.{$lang}.og_description"] = 'nullable|string|max:160';
            $rules["multiLangInputs.{$lang}.focus_keywords"] = 'nullable|string|max:255';
        }

        return $rules;
    }

    public function render()
    {
        return view('seomanagement::livewire.admin.universal-seo-component');
    }
}