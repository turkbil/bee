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
    public $content;
    public $css;
    public $js;
    public $pageTitle;
    public $widgets = [];
    public $categories = [];
    
    public function mount($module, $id)
    {
        $this->module = $module;
        $this->moduleId = (int)$id;
        
        $this->loadContent();
        $this->loadWidgets();
    }
    
    protected function loadContent()
    {
        try {
            $editorService = app(EditorService::class);
            $data = $editorService->loadContent($this->module, $this->moduleId);
            
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
        if (is_array($value)) {
            // Eğer translatable array ise, mevcut locale'yi al
            $locale = app()->getLocale();
            return $value[$locale] ?? $value['tr'] ?? $value['en'] ?? (string) reset($value);
        }
        
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
            'content' => $this->content,
            'css' => $this->css,
            'js' => $this->js,
            'widgets' => $this->widgets,
            'categories' => $this->categories
        ]);
    }
}