<?php
declare(strict_types=1);

namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use App\Helpers\SlugHelper;
use App\Traits\HasSlugManagement;
use App\Services\GlobalTabService;

#[Layout('admin.layout')]
class PortfolioCategoryManageComponent extends Component
{
    use HasSlugManagement;

    public int|null $categoryId = null;
    public string $currentLanguage = 'tr';
    public array $availableLanguages = [];
    
    // Çoklu dil inputs
    public array $multiLangInputs = [];
    
    // Diğer inputs
    public array $inputs = [];
    public int $order = 0;
    public bool $is_active = true;
    
    // Tab sistemi
    public array $tabConfig = [];
    public array $tabCompletionStatus = [];
    
    // SEO Cache - Universal SEO Component için
    public array $seoDataCache = [];
    
    // Dependencies
    protected $tabService;

    /**
     * Modern dependency injection
     */
    public function boot(): void
    {
        $this->tabService = app(GlobalTabService::class);
    }

    public function mount(int|null $id = null): void
    {
        try {
            $this->categoryId = $id;
            
            // Site varsayılan dilini ayarla
            $this->currentLanguage = $this->siteLocale();
            $this->availableLanguages = $this->availableSiteLanguages()->toArray();
            
            // Tab konfigürasyonu
            $this->loadTabConfiguration();
            
            if ($id) {
                $this->loadCategory($id);
            } else {
                $this->order = PortfolioCategory::max('order') + 1;
                $this->initializeEmptyInputs();
            }
            
            // Initialize inputs array
            $this->inputs = [
                'is_active' => $this->is_active
            ];
            
            // SEO Cache initialize et
            $this->initializeSeoDataCache();
            
            // Tab completion status'unu güncelle
            $this->updateTabCompletionStatus();

        } catch (\Exception $e) {
            \Log::error('Portfolio Category Mount Error', [
                'error_message' => $e->getMessage(),
                'category_id' => $id
            ]);
            throw $e;
        }
    }

    protected function loadCategory(int $id): void
    {
        try {
            $category = PortfolioCategory::findOrFail($id);
            
            // Diğer alanları doldur
            $this->order = $category->order;
            $this->is_active = $category->is_active;
            
            // Inputs array'ini güncelle
            $this->inputs = [
                'is_active' => $this->is_active
            ];
            
            // Çoklu dil alanları doldur
            foreach ($this->availableSiteLanguages() as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $category->getTranslated('title', $lang) ?? '',
                    'slug' => $category->getTranslated('slug', $lang) ?? '',
                    'body' => $category->getTranslated('body', $lang) ?? '',
                ];
            }
            
        } catch (\Exception $e) {
            \Log::error('Portfolio Category Load Error', [
                'error_message' => $e->getMessage(),
                'category_id' => $id
            ]);
            throw $e;
        }
    }

    protected function initializeEmptyInputs(): void
    {
        foreach ($this->availableSiteLanguages() as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'slug' => '',
                'body' => '',
            ];
        }
    }
    
    /**
     * SEO Data Cache initialize et
     */
    protected function initializeSeoDataCache(): void
    {
        foreach ($this->availableSiteLanguages() as $lang) {
            $this->seoDataCache[$lang] = [
                'seo_title' => '',
                'seo_description' => '',
                'seo_keywords' => '',
                'canonical_url' => '',
            ];
        }
    }

    #[Computed]
    public function availableSiteLanguages(): Collection
    {
        return TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code');
    }

    #[Computed]
    public function adminLocale(): string
    {
        return session('admin_locale', 'tr');
    }

    #[Computed]
    public function siteLocale(): string
    {
        return session('site_default_language', 'tr');
    }
    
    /**
     * Tab konfigürasyonunu yükle
     */
    protected function loadTabConfiguration(): void
    {
        $this->tabConfig = [
            ['key' => 'basic', 'name' => __('portfolio::admin.basic_information'), 'icon' => 'fa-edit'],
            ['key' => 'seo', 'name' => __('portfolio::admin.seo'), 'icon' => 'fa-search']
        ];
    }
    
    /**
     * Tab completion status'unu güncelle
     */
    protected function updateTabCompletionStatus(): void
    {
        $this->tabCompletionStatus = [
            'basic' => $this->isBasicTabCompleted(),
            'seo' => $this->isSeoTabCompleted()
        ];
    }
    
    /**
     * Temel bilgiler tabı tamamlandı mı?
     */
    protected function isBasicTabCompleted(): bool
    {
        $defaultLang = $this->siteLocale();
        return !empty($this->multiLangInputs[$defaultLang]['title'] ?? '');
    }
    
    /**
     * SEO tabı tamamlandı mı?
     */
    protected function isSeoTabCompleted(): bool
    {
        // SEO tabı opsiyonel olarak işaretlenebilir
        return true;
    }

    /**
     * Dil değiştirme işlevi
     */
    public function switchLanguage(string $language): void
    {
        if (in_array($language, $this->availableSiteLanguages()->toArray())) {
            $this->currentLanguage = $language;
        }
    }

    /**
     * Form kaydetme işlevi
     */
    public function save(): void
    {
        try {
            // Validation rules
            $rules = [];
            
            foreach ($this->availableSiteLanguages() as $lang) {
                $rules["multiLangInputs.{$lang}.title"] = 'required|string|max:255';
                $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
                $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
            }
            
            $rules['order'] = 'required|integer|min:0';
            $rules['inputs.is_active'] = 'boolean';
            
            $this->validate($rules);
            
            // Inputs array'inden veriyi al
            $this->is_active = $this->inputs['is_active'] ?? false;
            
            // Slug işlemi
            $processedSlugs = $this->processMultiLanguageSlugs(
                PortfolioCategory::class,
                $this->multiLangInputs,
                $this->availableSiteLanguages()->toArray(),
                $this->categoryId
            );
            
            // JSON encode
            $titleJson = [];
            $slugJson = [];
            $bodyJson = [];
            
            foreach ($this->availableSiteLanguages() as $lang) {
                $titleJson[$lang] = $this->multiLangInputs[$lang]['title'] ?? '';
                $slugJson[$lang] = $processedSlugs[$lang] ?? '';
                $bodyJson[$lang] = $this->multiLangInputs[$lang]['body'] ?? '';
            }
            
            // Veritabanına kaydet
            if ($this->categoryId) {
                // Güncelleme
                $category = PortfolioCategory::findOrFail($this->categoryId);
                $category->update([
                    'title' => $titleJson,
                    'slug' => $slugJson,
                    'body' => $bodyJson,
                    'order' => $this->order,
                    'is_active' => $this->is_active,
                ]);
                
                session()->flash('message', __('portfolio::admin.category_updated_successfully'));
            } else {
                // Yeni kayıt
                $category = PortfolioCategory::create([
                    'title' => $titleJson,
                    'slug' => $slugJson,
                    'body' => $bodyJson,
                    'order' => $this->order,
                    'is_active' => $this->is_active,
                ]);
                
                session()->flash('message', __('portfolio::admin.category_created_successfully'));
                
                // Yeni kayıtta ID'yi güncelle
                $this->categoryId = $category->portfolio_category_id;
            }
            
            // Tab completion status'unu güncelle
            $this->updateTabCompletionStatus();
            
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => session('message'),
                'type' => 'success'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.general_error'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Current category accessor - Universal SEO Component için
     */
    public function currentCategory()
    {
        if (!$this->categoryId) {
            return null;
        }
        
        return PortfolioCategory::with('seoSetting')->find($this->categoryId);
    }
    
    public function render()
    {
        return view('portfolio::admin.livewire.portfolio-category-manage-component');
    }
}