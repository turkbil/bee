<?php

namespace Modules\Studio\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Studio\App\Services\WidgetService;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class WidgetManagerComponent extends Component
{
    public $widgets = [];
    public $categories = [];
    public $selectedWidgetId = null;
    public $widgetContent = '';
    public $widgetCss = '';
    public $widgetJs = '';
    public $search = '';
    
    public function mount()
    {
        $this->loadWidgets();
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
    
    public function selectWidget($widgetId)
    {
        try {
            $this->selectedWidgetId = $widgetId;
            $widgetService = app(WidgetService::class);
            $content = $widgetService->getWidgetContent($widgetId);
            
            if ($content) {
                $this->widgetContent = $content['html'] ?? '';
                $this->widgetCss = $content['css'] ?? '';
                $this->widgetJs = $content['js'] ?? '';
            } else {
                $this->widgetContent = '';
                $this->widgetCss = '';
                $this->widgetJs = '';
            }
        } catch (\Exception $e) {
            Log::error('Widget seçilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Widget seçilirken hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function saveWidget()
    {
        if (!$this->selectedWidgetId) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Lütfen bir widget seçin',
                'type' => 'error'
            ]);
            return;
        }
        
        try {
            $widgetService = app(WidgetService::class);
            $result = $widgetService->updateWidgetContent(
                $this->selectedWidgetId,
                $this->widgetContent,
                $this->widgetCss,
                $this->widgetJs
            );
            
            if ($result) {
                $this->dispatch('toast', [
                    'title' => 'Başarılı',
                    'message' => 'Widget içeriği başarıyla kaydedildi',
                    'type' => 'success'
                ]);
                
                $this->loadWidgets();
            } else {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Widget içeriği kaydedilemedi',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Widget kaydedilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Widget kaydedilirken hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        // Arama yapmak için widgets filtreleniyor
        $filteredWidgets = $this->widgets;
        
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $filteredWidgets = array_filter($this->widgets, function($widget) use ($search) {
                return str_contains(strtolower($widget['name']), $search) || 
                       str_contains(strtolower($widget['description'] ?? ''), $search);
            });
        }
        
        return view('studio::livewire.widget-manager', [
            'filteredWidgets' => $filteredWidgets
        ]);
    }
}