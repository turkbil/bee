<?php

namespace Modules\ModuleManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\ModuleTenantSetting;
use App\Services\ModuleSlugService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Facades\Log;

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
    
    public function mount($module)
    {
        $this->moduleName = $module;
        $this->loadAvailableLanguages();
        $this->loadModuleData();
        $this->loadCurrentSlugs();
        $this->loadTabConfiguration();
    }
    
    /**
     * Tab konfigürasyonunu yükle
     */
    protected function loadTabConfiguration()
    {
        $this->tabConfig = [
            ['key' => 'basic', 'name' => 'URL Ayarları', 'icon' => 'fas fa-link']
        ];
        
        $this->updateTabCompletionStatus();
    }
    
    /**
     * Tab completion durumunu güncelle
     */
    protected function updateTabCompletionStatus()
    {
        // Her dil için en az bir slug dolu mu kontrol et
        $isCompleted = false;
        foreach ($this->availableLanguages as $lang) {
            $langSlugs = $this->multiLangSlugs[$lang] ?? [];
            if (!empty($langSlugs)) {
                $isCompleted = true;
                break;
            }
        }
        
        $this->tabCompletionStatus = [
            'basic' => $isCompleted
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
        
        // İlk dil default olsun
        $defaultLanguage = session('site_default_language', 'tr');
        $this->currentLanguage = in_array($defaultLanguage, $this->availableLanguages) ? $defaultLanguage : 'tr';
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
        return session('admin_locale', 'tr');
    }
    
    #[Computed] 
    public function siteLocale(): string
    {
        return session('site_locale', 'tr');
    }
    
    protected function loadModuleData()
    {
        $configPath = base_path("Modules/{$this->moduleName}/config/config.php");
        
        if (file_exists($configPath)) {
            $config = include $configPath;
            $this->moduleDisplayName = $config['name'] ?? $this->moduleName;
            $this->defaultSlugs = $config['slugs'] ?? [];
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
        
        // Multi-language names
        if ($setting && isset($setting->settings['multiLangNames'])) {
            $this->multiLangNames = $setting->settings['multiLangNames'];
        } else {
            // Initialize with default names
            foreach ($this->availableLanguages as $language) {
                $this->multiLangNames[$language] = ModuleSlugService::getDefaultModuleName($this->moduleName, $language);
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
        
        // Boş değer kontrolü
        if (empty(trim($value))) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'URL boş olamaz.',
                'type' => 'error',
            ]);
            return;
        }
        
        // URL temizleme
        $cleanValue = $this->cleanSlug($value);
        
        // Çakışma kontrolü (dil bazında)
        if (ModuleSlugService::isSlugConflict($cleanValue, $this->moduleName, $key, $language)) {
            $this->dispatch('toast', [
                'title' => 'URL Çakışması!',
                'message' => "'{$cleanValue}' URL'i {$language} dilinde zaten başka bir modül tarafından kullanılıyor.",
                'type' => 'error',
            ]);
            return;
        }
        
        // Multi-language slug'ı güncelle
        if (!isset($this->multiLangSlugs[$language])) {
            $this->multiLangSlugs[$language] = $this->defaultSlugs;
        }
        $this->multiLangSlugs[$language][$key] = $cleanValue;
        
        // Current language ise backward compatibility için slugs'ı da güncelle
        if ($language === $this->currentLanguage) {
            $this->slugs[$key] = $cleanValue;
        }
        
        $this->saveSettings();
        
        // Cache'i temizle ki değişiklik anında görünsün
        ModuleSlugService::clearCache();
        
        // Tab completion güncelle
        $this->updateTabCompletionStatus();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => ucfirst($key) . ' URL\'i ' . strtoupper($language) . ' dilinde güncellendi.',
            'type' => 'success',
        ]);
    }
    
    /**
     * Modül adını güncelle
     */
    public function updateModuleName($value, $language = null)
    {
        $language = $language ?? $this->currentLanguage;
        
        // Boş değer kontrolü
        if (empty(trim($value))) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Modül adı boş olamaz.',
                'type' => 'error',
            ]);
            return;
        }
        
        // Modül adını güncelle
        $this->multiLangNames[$language] = trim($value);
        
        $this->saveSettings();
        
        // Cache'i temizle
        ModuleSlugService::clearCache();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Modül adı ' . strtoupper($language) . ' dilinde güncellendi.',
            'type' => 'success',
        ]);
    }
    
    /**
     * Dil değiştirme fonksiyonu
     */
    public function switchLanguage($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;
            
            // Current language için slugs'ı güncelle
            $this->slugs = $this->multiLangSlugs[$language] ?? $this->defaultSlugs;
            
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
        
        // Current language ise backward compatibility için slugs'ı da güncelle
        if ($language === $this->currentLanguage) {
            $this->slugs = $this->defaultSlugs;
        }
        
        $this->saveSettings();
        
        // Cache'i temizle
        ModuleSlugService::clearCache();
        
        // Tab completion güncelle
        $this->updateTabCompletionStatus();
        
        $this->dispatch('toast', [
            'title' => 'Tümü Sıfırlandı!',
            'message' => strtoupper($language) . ' dilindeki tüm URL\'ler varsayılana döndürüldü.',
            'type' => 'info',
        ]);
    }
    
    protected function saveSettings()
    {
        $setting = ModuleTenantSetting::updateOrCreate(
            ['module_name' => $this->moduleName],
            [
                'settings' => [
                    'multiLangSlugs' => $this->multiLangSlugs,
                    'multiLangNames' => $this->multiLangNames
                ]
            ]
        );
        
        // Settings saved
    }
    
    /**
     * Fake save method for form compatibility
     */
    public function save()
    {
        // Bu component otomatik kaydettiği için burada bir şey yapmıyoruz
        $this->dispatch('toast', [
            'title' => 'Bilgi',
            'message' => 'URL ayarları otomatik olarak kaydedilir.',
            'type' => 'info',
        ]);
    }
    
    public function render()
    {
        return view('modulemanagement::livewire.module-slug-settings-component');
    }
}