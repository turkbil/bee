<?php

namespace Modules\ModuleManagement\App\Services;

use Modules\ModuleManagement\App\Models\ModuleTenantSetting;
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class TenantModuleSettingsService
{
    protected $cacheTime = 3600;

    public function getModuleConfig(string $moduleName): array
    {
        $cacheKey = "module_config_{$moduleName}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($moduleName) {
            $defaults = $this->getModuleDefaults($moduleName);
            $tenantSettings = $this->getTenantSettings($moduleName);
            
            return array_merge($defaults, $tenantSettings);
        });
    }

    public function getModuleDefaults(string $moduleName): array
    {
        $configPath = base_path("Modules/" . ucfirst($moduleName) . "/Config/module.config.php");
        
        if (File::exists($configPath)) {
            return include $configPath;
        }
        
        return ['routes' => []];
    }

    public function getTenantSettings(string $moduleName): array
    {
        $settings = ModuleTenantSetting::getForModule($moduleName);
        $result = [];
        
        foreach ($settings as $setting) {
            $keys = explode('.', $setting->setting_key);
            $this->setNestedValue($result, $keys, $setting->value);
        }
        
        return $result;
    }

    public function setSetting(string $moduleName, string $settingKey, $value, string $type = 'string', string $description = null): bool
    {
        try {
            ModuleTenantSetting::setSetting($moduleName, $settingKey, $value, $type, $description);
            $this->clearCache($moduleName);
            return true;
        } catch (\Exception $e) {
            \Log::error("Module setting error: " . $e->getMessage());
            return false;
        }
    }

    public function getRouteSlug(string $moduleName, string $routeType): string
    {
        $config = $this->getModuleConfig($moduleName);
        $defaults = $this->getModuleDefaults($moduleName);
        
        return $config['routes'][$routeType] ?? $defaults['routes'][$routeType] ?? $routeType;
    }

    public function isContentModule(string $moduleName): bool
    {
        $module = Module::where('name', $moduleName)->first();
        return $module && $module->type === 'content';
    }

    public function clearCache(string $moduleName = null): void
    {
        if ($moduleName) {
            Cache::forget("module_config_{$moduleName}");
        } else {
            $modules = Module::where('type', 'content')->pluck('name');
            foreach ($modules as $module) {
                Cache::forget("module_config_{$module}");
            }
        }
    }

    protected function setNestedValue(array &$array, array $keys, $value): void
    {
        $current = &$array;
        foreach ($keys as $key) {
            if (!isset($current[$key]) || !is_array($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }
        $current = $value;
    }
}