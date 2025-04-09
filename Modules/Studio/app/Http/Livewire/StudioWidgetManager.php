<?php

namespace Modules\Studio\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Studio\App\Services\StudioWidgetService;
use Modules\WidgetManagement\App\Models\Widget;

#[Layout('admin.layout')]
class StudioWidgetManager extends Component
{
    public $widgets = [];
    public $categories = [];
    public $selectedWidget = null;
    public $widgetContent = '';
    public $widgetCss = '';
    public $widgetJs = '';
    
    public function mount()
    {
        $this->loadWidgets();
        $this->loadCategories();
    }
    
    protected function loadWidgets()
    {
        $widgetService = app(StudioWidgetService::class);
        $this->widgets = $widgetService->getAllWidgets();
    }
    
    protected function loadCategories()
    {
        $widgetService = app(StudioWidgetService::class);
        $this->categories = $widgetService->getCategories();
    }
    
    public function selectWidget($widgetId)
    {
        if (class_exists('Modules\WidgetManagement\App\Models\Widget')) {
            $this->selectedWidget = Widget::find($widgetId);
            if ($this->selectedWidget) {
                $this->widgetContent = $this->selectedWidget->content_html;
                $this->widgetCss = $this->selectedWidget->content_css;
                $this->widgetJs = $this->selectedWidget->content_js;
            }
        }
    }
    
    public function saveWidget()
    {
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
        
        log_activity($this->selectedWidget, 'studio ile düzenlendi');
        
        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Widget içeriği başarıyla kaydedildi',
            'type' => 'success'
        ]);
        
        $this->loadWidgets();
    }
    
    public function render()
    {
        return view('studio::livewire.studio-widget-manager');
    }
}