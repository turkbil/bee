<?php

namespace Modules\Studio\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Studio\App\Services\StudioWidgetService;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class StudioWidgetManager extends Component
{
    public $widgets = [];
    public $categories = [];
    public $selectedWidget = null;
    public $widgetContent = '';
    public $widgetCss = '';
    public $widgetJs = '';
    public $search = '';
    
    /**
     * Bileşen monte ediliyor
     */
    public function mount()
    {
        $this->loadWidgets();
        $this->loadCategories();
    }
    
    /**
     * Widgetları yükle
     */
    protected function loadWidgets()
    {
        try {
            $widgetService = app(StudioWidgetService::class);
            $this->widgets = $widgetService->getAllWidgets();
        } catch (\Exception $e) {
            Log::error('Widget yüklenirken hata: ' . $e->getMessage());
            $this->widgets = [];
        }
    }
    
    /**
     * Kategorileri yükle
     */
    protected function loadCategories()
    {
        try {
            $widgetService = app(StudioWidgetService::class);
            $this->categories = $widgetService->getCategories();
        } catch (\Exception $e) {
            Log::error('Kategori yüklenirken hata: ' . $e->getMessage());
            $this->categories = [];
        }
    }
    
    /**
     * Widget seç
     * @param int $widgetId
     */
    public function selectWidget($widgetId)
    {
        try {
            if (!class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Widget Yönetim modülü bulunamadı',
                    'type' => 'error'
                ]);
                return;
            }
            
            $this->selectedWidget = \Modules\WidgetManagement\App\Models\Widget::find($widgetId);
            
            if ($this->selectedWidget) {
                $this->widgetContent = $this->selectedWidget->content_html;
                $this->widgetCss = $this->selectedWidget->content_css;
                $this->widgetJs = $this->selectedWidget->content_js;
            } else {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Widget bulunamadı',
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Widget seçilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Widget seçilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    /**
     * Widget kaydet
     */
    public function saveWidget()
    {
        try {
            if (!$this->selectedWidget) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Lütfen bir widget seçin',
                    'type' => 'error'
                ]);
                return;
            }
            
            $this->selectedWidget->content_html = $this->widgetContent;
            $this->selectedWidget->content_css = $this->widgetCss;
            $this->selectedWidget->content_js = $this->widgetJs;
            $this->selectedWidget->save();
            
            // Aktivite kaydı
            if (function_exists('activity')) {
                activity()
                    ->performedOn($this->selectedWidget)
                    ->withProperties(['studio' => true])
                    ->log('studio ile düzenlendi');
            }
            
            // Widget önbelleğini temizle
            app(StudioWidgetService::class)->clearCache();
            
            // Widget güncellenme olayını tetikle
            event(new \Modules\Studio\Events\StudioWidgetUpdated(
                $this->selectedWidget->id,
                auth()->id()
            ));
            
            // Widgetları yeniden yükle
            $this->loadWidgets();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Widget içeriği başarıyla kaydedildi',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Widget kaydedilirken hata: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Widget kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    /**
     * Widget aralarken uygulanan filtre
     */
    public function getFilteredWidgetsProperty()
    {
        if (empty($this->search)) {
            return $this->widgets;
        }
        
        $search = strtolower($this->search);
        
        return array_filter($this->widgets, function($widget) use ($search) {
            return stripos($widget['name'], $search) !== false || 
                   stripos($widget['description'], $search) !== false;
        });
    }
    
    /**
     * Görünüm oluşturuluyor
     */
    public function render()
    {
        return view('studio::livewire.studio-widget-manager', [
            'filteredWidgets' => $this->getFilteredWidgetsProperty()
        ]);
    }
}