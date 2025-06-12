<?php

namespace Modules\ModuleManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ModuleTenantSetting;
use App\Services\ModuleSlugService;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class ModuleSlugSettingsComponent extends Component
{
    public $moduleName;
    public $moduleDisplayName;
    public $slugs = [];
    public $defaultSlugs = [];
    
    public function mount($module)
    {
        $this->moduleName = $module;
        $this->loadModuleData();
        $this->loadCurrentSlugs();
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
        
        if ($setting && isset($setting->settings['slugs'])) {
            $this->slugs = array_merge($this->defaultSlugs, $setting->settings['slugs']);
        }
    }
    
    public function updateSlug($key, $value)
    {
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
        
        // Çakışma kontrolü
        if (ModuleSlugService::isSlugConflict($cleanValue, $this->moduleName, $key)) {
            $this->dispatch('toast', [
                'title' => 'URL Çakışması!',
                'message' => "'{$cleanValue}' URL'i zaten başka bir modül tarafından kullanılıyor.",
                'type' => 'error',
            ]);
            return;
        }
        
        $this->slugs[$key] = $cleanValue;
        $this->saveSettings();
        
        // Cache'i temizle ki değişiklik anında görünsün
        ModuleSlugService::clearCache();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => ucfirst($key) . ' URL\'i güncellendi.',
            'type' => 'success',
        ]);
    }
    
    protected function cleanSlug($value)
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9\-_çğıöşüÇĞIÖŞÜ]/', '', $value)));
    }
    
    public function resetSlug($key)
    {
        $defaultValue = $this->defaultSlugs[$key] ?? $key;
        $this->updateSlug($key, $defaultValue);
        
        $this->dispatch('toast', [
            'title' => 'Sıfırlandı!',
            'message' => ucfirst($key) . ' URL\'i varsayılana döndürüldü.',
            'type' => 'info',
        ]);
    }
    
    public function resetAllSlugs()
    {
        $this->slugs = $this->defaultSlugs;
        $this->saveSettings();
        
        // Cache'i temizle
        ModuleSlugService::clearCache();
        
        $this->dispatch('toast', [
            'title' => 'Tümü Sıfırlandı!',
            'message' => 'Tüm URL\'ler varsayılana döndürüldü.',
            'type' => 'info',
        ]);
    }
    
    protected function saveSettings()
    {
        $setting = ModuleTenantSetting::updateOrCreate(
            ['module_name' => $this->moduleName],
            [
                'settings' => [
                    'slugs' => $this->slugs
                ]
            ]
        );
        
        // Settings saved
    }
    
    public function render()
    {
        return view('modulemanagement::livewire.module-slug-settings-component');
    }
}