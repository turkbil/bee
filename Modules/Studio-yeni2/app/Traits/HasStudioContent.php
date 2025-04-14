<?php

namespace Modules\Studio\App\Traits;

use Modules\Studio\App\Models\StudioSetting;

trait HasStudioContent
{
    /**
     * Studio ayarlarını döndürür
     */
    public function studioSettings()
    {
        $module = $this->getStudioModuleName();
        $id = $this->getStudioModuleId();
        
        return StudioSetting::where('module', $module)
            ->where('module_id', $id)
            ->first();
    }
    
    /**
     * Studio içeriğine sahip mi?
     * 
     * @return bool
     */
    public function hasStudioContent(): bool
    {
        return $this->studioSettings() !== null;
    }
    
    /**
     * Studio içerik verisini döndürür
     * 
     * @return array
     */
    public function getStudioContentAttribute(): array
    {
        $settings = $this->studioSettings();
        
        if (!$settings) {
            return [
                'theme' => config('studio.themes.default', 'default'),
                'header_template' => null,
                'footer_template' => null,
                'settings' => [],
            ];
        }
        
        return [
            'theme' => $settings->theme,
            'header_template' => $settings->header_template,
            'footer_template' => $settings->footer_template,
            'settings' => $settings->settings ?? [],
        ];
    }
    
    /**
     * Studio içerik verisini ayarlar
     * 
     * @param array $value
     * @return void
     */
    public function setStudioContentAttribute(array $value): void
    {
        $module = $this->getStudioModuleName();
        $id = $this->getStudioModuleId();
        
        StudioSetting::updateOrCreate(
            ['module' => $module, 'module_id' => $id],
            [
                'theme' => $value['theme'] ?? config('studio.themes.default', 'default'),
                'header_template' => $value['header_template'] ?? null,
                'footer_template' => $value['footer_template'] ?? null,
                'settings' => $value['settings'] ?? [],
            ]
        );
    }
    
    /**
     * Studio modül adını döndürür
     * 
     * @return string
     */
    protected function getStudioModuleName(): string
    {
        // Varsayılan olarak model sınıf adını kullan
        // Modeller Studio modül adını override edebilir
        if (method_exists($this, 'getStudioModule')) {
            return $this->getStudioModule();
        }
        
        // Sınıf adından modül adını çıkar
        $className = class_basename($this);
        return strtolower($className);
    }
    
    /**
     * Studio modül ID'sini döndürür
     * 
     * @return int
     */
    protected function getStudioModuleId(): int
    {
        // Birincil anahtar alanını kullan (varsayılan olarak 'id')
        return $this->getKey();
    }
    
    /**
     * Studio içeriğini render eder
     * 
     * @return string
     */
    public function renderStudioContent(): string
    {
        $settings = $this->studio_content;
        
        // Tema ve şablonları kontrol et
        $theme = $settings['theme'] ?? config('studio.themes.default', 'default');
        $headerTemplate = $settings['header_template'] ?? null;
        $footerTemplate = $settings['footer_template'] ?? null;
        
        // İçeriği al
        $content = $this->body ?? '';
        
        // Header ve footer şablonlarını ekle
        $output = '';
        
        if ($headerTemplate && view()->exists($headerTemplate)) {
            $output .= view($headerTemplate, ['settings' => $settings['settings'] ?? []])->render();
        }
        
        $output .= $content;
        
        if ($footerTemplate && view()->exists($footerTemplate)) {
            $output .= view($footerTemplate, ['settings' => $settings['settings'] ?? []])->render();
        }
        
        return $output;
    }
}