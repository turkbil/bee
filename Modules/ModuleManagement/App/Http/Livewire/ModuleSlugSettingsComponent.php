<?php

namespace Modules\ModuleManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\ModuleTenantSetting;
use App\Services\ModuleSlugService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Facades\Log;
use App\Services\GlobalSeoService;
use App\Contracts\GlobalSeoRepositoryInterface;

#[Layout('admin.layout')]
class ModuleSlugSettingsComponent extends Component
{
    public $moduleName;
    public $moduleDisplayName;
    public $slugs = [];
    public $defaultSlugs = [];
    
    // Language Management
    public $currentLanguage = 'tr';
    public $availableLanguages = [];
    public $multiLangSlugs = [];
    public $multiLangNames = [];
    
    // Tab Configuration
    public $tabConfig = [];
    public $tabCompletionStatus = [];
    
    // SEO Alanları - Page pattern'ından birebir kopyalanan sistem
    public $seoDataCache = [];
    public $allLanguagesSeoData = [];
    
    // SOLID Dependencies
    protected $seoRepository;
    
    public function mount($module)
    {
        $this->moduleName = $module;
        $this->boot(); // SEO dependencies
        $this->loadAvailableLanguages();
        $this->loadModuleData();
        $this->loadCurrentSlugs();
        $this->loadSeoData(); // SEO data yükle
        $this->loadTabConfiguration();
    }
    
    /**
     * Dependency Injection Boot - Page pattern'ından kopyalanan
     */
    public function boot()
    {
        $this->seoRepository = app(GlobalSeoRepositoryInterface::class);
    }
    
    /**
     * Tab konfigürasyonunu yükle - Page pattern'ından uyarlanan
     */
    protected function loadTabConfiguration()
    {
        $this->tabConfig = [
            ['key' => 'basic', 'name' => 'URL Ayarları', 'icon' => 'fas fa-link'],
            ['key' => 'seo', 'name' => 'SEO', 'icon' => 'fas fa-search']
        ];
        
        $this->updateTabCompletionStatus();
    }
    
    /**
     * SEO data yükleme - Page pattern'ından uyarlanan
     */
    protected function loadSeoData()
    {
        // Module index SEO ayarları için seo_settings tablosundan veri al
        $seoSettings = $this->seoRepository->getSeoSettings($this->moduleName, 'index');
        
        // Tüm diller için SEO cache oluştur
        foreach ($this->availableLanguages as $lang) {
            $this->seoDataCache[$lang] = [
                'seo_title' => $seoSettings['titles'][$lang] ?? '',
                'seo_description' => $seoSettings['descriptions'][$lang] ?? '', 
                'seo_keywords' => is_array($seoSettings['keywords'][$lang] ?? '') 
                    ? implode(', ', $seoSettings['keywords'][$lang]) 
                    : ($seoSettings['keywords'][$lang] ?? ''),
                'canonical_url' => $seoSettings['canonical_url'] ?? ''
            ];
        }
        
        // JavaScript için de aynı veriyi hazırla
        $this->allLanguagesSeoData = $this->seoDataCache;
    }

    /**
     * Tab completion durumunu güncelle - SEO tab dahil
     */
    protected function updateTabCompletionStatus()
    {
        // Basic tab: Her dil için en az bir slug dolu mu kontrol et
        $basicCompleted = false;
        foreach ($this->availableLanguages as $lang) {
            $langSlugs = $this->multiLangSlugs[$lang] ?? [];
            if (!empty($langSlugs)) {
                $basicCompleted = true;
                break;
            }
        }
        
        // SEO tab: En az bir dil için SEO title veya description dolu mu
        $seoCompleted = false;
        foreach ($this->availableLanguages as $lang) {
            $seoData = $this->seoDataCache[$lang] ?? [];
            if (!empty($seoData['seo_title']) || !empty($seoData['seo_description'])) {
                $seoCompleted = true;
                break;
            }
        }
        
        $this->tabCompletionStatus = [
            'basic' => $basicCompleted,
            'seo' => $seoCompleted
        ];
    }
    
    /**
     * Site dillerini yükle
     */
    protected function loadAvailableLanguages()
    {
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
            
        // Fallback sistem
        if (empty($this->availableLanguages)) {
            $this->availableLanguages = ['tr'];
        }
        
        // İlk dil default olsun - dinamik
        $defaultLanguage = session('site_default_language', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
        $this->currentLanguage = in_array($defaultLanguage, $this->availableLanguages) ? $defaultLanguage : \App\Services\TenantLanguageProvider::getDefaultLanguageCode();
    }
    
    #[Computed]
    public function availableSiteLanguages()
    {
        return TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }
    
    #[Computed]
    public function adminLocale(): string
    {
        return session('admin_locale', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
    }
    
    #[Computed] 
    public function siteLocale(): string
    {
        return session('site_locale', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
    }
    
    protected function loadModuleData()
    {
        // Modül klasörünü case-insensitive bul (Linux case-sensitive!)
        $modulesPath = base_path('Modules');
        $actualModuleName = null;

        if (is_dir($modulesPath)) {
            foreach (scandir($modulesPath) as $dir) {
                if ($dir === '.' || $dir === '..') continue;
                if (strtolower($dir) === strtolower($this->moduleName)) {
                    $actualModuleName = $dir;
                    break;
                }
            }
        }

        // Gerçek modül adı bulunduysa config'i yükle
        if ($actualModuleName) {
            $configPath = base_path("Modules/{$actualModuleName}/config/config.php");

            if (file_exists($configPath)) {
                $config = include $configPath;
                $this->moduleDisplayName = $config['name'] ?? $actualModuleName;
                $this->defaultSlugs = $config['slugs'] ?? [];
            }
        }

        // Config bulunamadıysa fallback
        if (empty($this->defaultSlugs)) {
            $this->moduleDisplayName = ModuleSlugService::getDefaultModuleName($this->moduleName, app()->getLocale());
        }

        // Initialize slugs with defaults
        $this->slugs = $this->defaultSlugs;
    }
    
    protected function loadCurrentSlugs()
    {
        $setting = ModuleTenantSetting::where('module_name', $this->moduleName)->first();
        
        if ($setting && isset($setting->settings['multiLangSlugs'])) {
            $this->multiLangSlugs = $setting->settings['multiLangSlugs'];
        } else {
            // Initialize empty multi-language slugs structure
            foreach ($this->availableLanguages as $language) {
                $this->multiLangSlugs[$language] = $this->defaultSlugs;
            }
        }
        
        // Multi-language names - title kolonundan al
        foreach ($this->availableLanguages as $language) {
            if ($setting && $setting->title && isset($setting->title[$language]) && !empty(trim($setting->title[$language]))) {
                // Veritabanından custom title varsa onu kullan
                $this->multiLangNames[$language] = $setting->title[$language];
            } else {
                // Yoksa default display name kullan
                $defaultName = ModuleSlugService::getDefaultModuleName($this->moduleName, $language);
                $this->multiLangNames[$language] = $defaultName;
            }
        }
        
        // Backward compatibility - eski slug sistemi varsa dönüştür
        if ($setting && isset($setting->settings['slugs']) && !isset($setting->settings['multiLangSlugs'])) {
            $oldSlugs = $setting->settings['slugs'];
            foreach ($this->availableLanguages as $language) {
                $this->multiLangSlugs[$language] = array_merge($this->defaultSlugs, $oldSlugs);
            }
            $this->saveSettings(); // Yeni yapıya kaydet
        }
        
        // Current language için slugs property'sini de güncelle (backward compatibility)
        $this->slugs = $this->multiLangSlugs[$this->currentLanguage] ?? $this->defaultSlugs;
    }
    
    public function updateSlug($key, $value, $language = null)
    {
        $language = $language ?? $this->currentLanguage;
        
        // URL temizleme
        $cleanValue = $this->cleanSlug($value);
        
        // Multi-language slug'ı güncelle
        if (!isset($this->multiLangSlugs[$language])) {
            $this->multiLangSlugs[$language] = $this->defaultSlugs;
        }
        $this->multiLangSlugs[$language][$key] = $cleanValue;
        
        // Current language ise backward compatibility için slugs'ı da güncelle
        if ($language === $this->currentLanguage) {
            $this->slugs[$key] = $cleanValue;
        }
        
        // Tab completion güncelle
        $this->updateTabCompletionStatus();
    }
    
    /**
     * Modül adını güncelle
     */
    public function updateModuleName($value, $language = null)
    {
        $language = $language ?? $this->currentLanguage;
        
        // Modül adını güncelle
        $this->multiLangNames[$language] = trim($value);
    }
    
    /**
     * Dil değiştirme fonksiyonu - SEO ile entegre
     */
    public function switchLanguage($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
            
            // Current language için slugs'ı güncelle
            $this->slugs = $this->multiLangSlugs[$language] ?? $this->defaultSlugs;
            
            // SEO data cache'i güncelle (her dil için ayrı veri)
            $this->allLanguagesSeoData = $this->seoDataCache;
            
            // Session'a kaydet
            session(['module_slug_settings_language' => $language]);
            
            // Tab completion güncelle
            $this->updateTabCompletionStatus();
        }
    }
    
    protected function cleanSlug($value)
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9\-_çğıöşüÇĞIÖŞÜ]/', '', $value)));
    }
    
    public function resetSlug($key, $language = null)
    {
        $language = $language ?? $this->currentLanguage;
        $defaultValue = $this->defaultSlugs[$key] ?? $key;
        $this->updateSlug($key, $defaultValue, $language);
        
        $this->dispatch('toast', [
            'title' => 'Sıfırlandı!',
            'message' => ucfirst($key) . ' URL\'i ' . strtoupper($language) . ' dilinde varsayılana döndürüldü.',
            'type' => 'info',
        ]);
    }
    
    public function resetAllSlugs($language = null)
    {
        $language = $language ?? $this->currentLanguage;
        
        // Belirtilen dil için tüm slug'ları sıfırla
        $this->multiLangSlugs[$language] = $this->defaultSlugs;
        
        // Belirtilen dil için modül başlığını da sıfırla (default adı kullan)
        $this->multiLangNames[$language] = ModuleSlugService::getDefaultModuleName($this->moduleName, $language);
        
        // Current language ise backward compatibility için slugs'ı da güncelle
        if ($language === $this->currentLanguage) {
            $this->slugs = $this->defaultSlugs;
        }
        
        // Tab completion güncelle
        $this->updateTabCompletionStatus();
    }
    
    protected function saveSettings()
    {
        $setting = ModuleTenantSetting::updateOrCreate(
            ['module_name' => $this->moduleName],
            [
                'settings' => [
                    'multiLangSlugs' => $this->multiLangSlugs
                ],
                'title' => $this->multiLangNames
            ]
        );
        
        // SEO ayarlarını kaydet - modül index sayfası için
        $this->saveSeoSettings();
    }
    
    /**
     * SEO ayarlarını kaydet - Page pattern'ından uyarlanan
     */
    protected function saveSeoSettings()
    {
        if (empty($this->seoDataCache)) {
            return;
        }
        
        // SEO verilerini prepare et
        $seoData = [
            'titles' => [],
            'descriptions' => [],
            'keywords' => [],
            'canonical_url' => ''
        ];
        
        foreach ($this->availableLanguages as $lang) {
            $langSeoData = $this->seoDataCache[$lang] ?? [];
            
            $seoData['titles'][$lang] = $langSeoData['seo_title'] ?? '';
            $seoData['descriptions'][$lang] = $langSeoData['seo_description'] ?? '';
            
            // Keywords string'i array'e çevir
            $keywordsString = $langSeoData['seo_keywords'] ?? '';
            $seoData['keywords'][$lang] = !empty($keywordsString) 
                ? array_map('trim', explode(',', $keywordsString))
                : [];
                
            if (!empty($langSeoData['canonical_url'])) {
                $seoData['canonical_url'] = $langSeoData['canonical_url'];
            }
        }
        
        // Global SEO service ile kaydet
        $this->seoRepository->saveSeoSettings($this->moduleName, 'index', $seoData);
    }
    
    /**
     * Manuel kaydetme - footer butonundan çağrılır
     */
    public function save()
    {
        try {
            // Cache'i validation öncesi temizle (stale data önlemi)
            ModuleSlugService::clearCache();
            
            // Her dil ve her slug için validation yap
            foreach ($this->availableLanguages as $language) {
                $langSlugs = $this->multiLangSlugs[$language] ?? [];
                
                foreach ($langSlugs as $key => $value) {
                    // Boş değer kontrolü
                    if (empty(trim($value))) {
                        $this->dispatch('toast', [
                            'title' => 'Hata!',
                            'message' => ucfirst($key) . " URL'i " . strtoupper($language) . " dilinde boş olamaz.",
                            'type' => 'error',
                        ]);
                        return;
                    }
                    
                    // Çakışma kontrolü (dil bazında) - DEBUG
                    $conflictResult = ModuleSlugService::isSlugConflict($value, $this->moduleName, $key, $language);
                    
                    // DEBUG: Log conflict check details
                    Log::info("Slug Conflict Check Debug", [
                        'slug' => $value,
                        'module' => $this->moduleName,
                        'key' => $key,
                        'language' => $language,
                        'conflict_result' => $conflictResult
                    ]);
                    
                    if ($conflictResult) {
                        $this->dispatch('toast', [
                            'title' => 'URL Çakışması! (DEBUG)',
                            'message' => "'{$value}' URL'i {$language} dilinde zaten başka bir modül tarafından kullanılıyor. (Module: {$this->moduleName}, Key: {$key})",
                            'type' => 'error',
                        ]);
                        return;
                    }
                }
                
                // Modül adı kontrolü
                $moduleName = $this->multiLangNames[$language] ?? '';
                if (empty(trim($moduleName))) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Modül adı ' . strtoupper($language) . ' dilinde boş olamaz.',
                        'type' => 'error',
                    ]);
                    return;
                }
            }
            
            $this->saveSettings();
            
            // Cache'i temizle
            ModuleSlugService::clearCache();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'URL ayarları kaydedildi.',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Kaydetme sırasında bir hata oluştu.',
                'type' => 'error',
            ]);
        }
    }
    
    /**
     * Kaydet ve geri dön
     */
    public function saveAndReturn()
    {
        // Önce normal save işlemini yap
        $this->save();
        
        // Eğer save başarılıysa redirect yap
        return redirect()->route('admin.modulemanagement.index');
    }
    
    public function render()
    {
        return view('modulemanagement::livewire.module-slug-settings-component');
    }
}