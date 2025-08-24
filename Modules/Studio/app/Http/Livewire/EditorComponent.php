<?php

namespace Modules\Studio\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Studio\App\Services\EditorService;
use Modules\Studio\App\Services\WidgetService;
use Illuminate\Support\Facades\Log;

#[Layout('studio::layouts.editor')]
class EditorComponent extends Component
{
    public $module;
    public $moduleId;
    public $locale;
    public $content;
    public $css;
    public $js;
    public $pageTitle;
    public $widgets = [];
    public $categories = [];
    public $availableLocales = [];
    
    public function mount($module, $id, $locale = null)
    {
        $this->module = $module;
        $this->moduleId = (int)$id;
        
        // Locale belirle - URL'den gelen ya da varsayılan
        $this->locale = $locale ?: $this->getDefaultLocale();
        
        // Tenant'ın aktif dillerini yükle
        $this->loadAvailableLocales();
        
        $this->loadContent();
        $this->loadWidgets();
    }
    
    protected function getDefaultLocale()
    {
        // Tenant'ın varsayılan dilini al
        try {
            $defaultLocale = \DB::table('tenant_languages')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->value('code');
            return $defaultLocale ?: 'tr';
        } catch (\Exception $e) {
            return 'tr';
        }
    }
    
    protected function loadAvailableLocales()
    {
        try {
            $this->availableLocales = \DB::table('tenant_languages')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->select('code', 'name', 'native_name', 'flag_icon')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            $this->availableLocales = [
                (object) ['code' => 'tr', 'name' => 'Turkish', 'native_name' => 'Türkçe', 'flag_icon' => '🇹🇷']
            ];
        }
    }
    
    protected function loadContent()
    {
        try {
            $editorService = app(EditorService::class);
            
            // Debug log - Component seviyesinde locale kontrolü
            Log::info("EditorComponent loadContent", [
                'module' => $this->module,
                'moduleId' => $this->moduleId,
                'component_locale' => $this->locale,
                'app_locale' => app()->getLocale()
            ]);
            
            $data = $editorService->loadContent($this->module, $this->moduleId, $this->locale);
            
            // Array değerleri safe string'e dönüştür
            $this->content = $this->safeStringCast($data['content'] ?? '');
            $this->css = $this->safeStringCast($data['css'] ?? '');
            $this->js = $this->safeStringCast($data['js'] ?? '');
            $this->pageTitle = $this->safeStringCast($data['title'] ?? 'Studio Editör');
        } catch (\Exception $e) {
            Log::error('İçerik yüklenirken hata: ' . $e->getMessage());
            session()->flash('error', 'İçerik yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    /**
     * Array değerleri safe string'e dönüştür
     *
     * @param mixed $value
     * @return string
     */
    protected function safeStringCast($value): string
    {
        // EditorService zaten doğru locale'i döndürüyor, sadece string'e çevir
        return (string) $value;
    }
    
    protected function loadWidgets()
    {
        try {
            $widgetService = app(WidgetService::class);
            $this->widgets = $widgetService->getAllWidgets();
            $this->categories = $widgetService->getCategories();
        } catch (\Exception $e) {
            Log::error('Widget verileri yüklenirken hata: ' . $e->getMessage());
        }
    }
    
    public function save()
    {
        try {
            $editorService = app(EditorService::class);
            $result = $editorService->saveContent($this->module, $this->moduleId, $this->content, $this->css, $this->js);
            
            if ($result) {
                session()->flash('success', 'İçerik başarıyla kaydedildi.');
            } else {
                session()->flash('error', 'İçerik kaydedilemedi.');
            }
        } catch (\Exception $e) {
            Log::error('İçerik kaydedilirken hata: ' . $e->getMessage());
            session()->flash('error', 'İçerik kaydedilirken hata oluştu: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('studio::livewire.editor', [
            'pageTitle' => $this->pageTitle,
            'moduleType' => $this->module,
            'moduleId' => $this->moduleId,
            'locale' => $this->locale,
            'availableLocales' => $this->availableLocales,
            'content' => $this->content,
            'css' => $this->css,
            'js' => $this->js,
            'widgets' => $this->widgets,
            'categories' => $this->categories
        ]);
    }
}