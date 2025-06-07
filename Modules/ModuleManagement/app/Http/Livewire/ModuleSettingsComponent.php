<?php

namespace Modules\ModuleManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\ModuleManagement\App\Models\Module;
use Modules\ModuleManagement\App\Models\ModuleTenantSetting;
use Modules\ModuleManagement\App\Services\TenantModuleSettingsService;

#[Layout('admin.layout')]
class ModuleSettingsComponent extends Component
{
    public $moduleId;
    public $module;
    public $routeSettings = [];
    public $defaultRoutes = [];

    protected $settingsService;

    public function boot(TenantModuleSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function mount($moduleId)
    {
        $this->moduleId = $moduleId;
        $this->module = Module::findOrFail($moduleId);
        
        if ($this->module->type !== 'content') {
            abort(404, 'Bu modül için slug ayarları yapılandırılamaz.');
        }
        
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->defaultRoutes = $this->settingsService->getModuleDefaults($this->module->name)['routes'] ?? [];
        $config = $this->settingsService->getModuleConfig($this->module->name);
        $this->routeSettings = $config['routes'] ?? $this->defaultRoutes;
        // routeSettings güncellendiğinde Blade inputları da güncellenir
    }

    public function saveRouteSetting($key, $value)
    {
        if (empty(trim($value))) {
            $this->dispatch('toast', [
                'title' => 'Uyarı!',
                'message' => 'Slug boş olamaz.',
                'type' => 'warning',
            ]);
            $this->loadSettings();
            return;
        }

        $settingKey = "routes.{$key}";
        $this->settingsService->setSetting($this->module->name, $settingKey, trim($value), 'string', "Route slug for {$key}");
        // routeSettings dizisini güncelle
        $this->routeSettings[$key] = trim($value);
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Route ayarı güncellendi.',
            'type' => 'success',
        ]);
        
        $this->loadSettings();
    }

    public function resetToDefaults()
    {
        ModuleTenantSetting::where('module_name', $this->module->name)
            ->where('setting_key', 'like', 'routes.%')
            ->delete();
            
        $this->settingsService->clearCache($this->module->name);
        
        $this->dispatch('toast', [
            'title' => 'Sıfırlandı!',
            'message' => 'Route ayarları varsayılana sıfırlandı.',
            'type' => 'success',
        ]);
        $this->routeSettings = $this->defaultRoutes;
        $this->loadSettings();
    }

    public function render()
    {
        return view('modulemanagement::livewire.module-settings-component');
    }
}