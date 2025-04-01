<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Services\WidgetService;
use Modules\Page\app\Models\Page;

class WidgetSectionComponent extends Component
{
    public $pageId;
    public $module;
    public $position;
    public $widgets = [];
    
    protected $widgetService;
    
    protected $listeners = [
        'addWidget' => 'addWidget',
        'widgetOrderUpdated' => 'updateWidgetOrder',
        'widgetSettingsUpdated' => 'refreshWidgets'
    ];
    
    public function boot(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }
    
    public function mount($pageId = null, $module = null, $position = 'top')
    {
        $this->pageId = $pageId;
        $this->module = $module;
        $this->position = $position;
        
        $this->loadWidgets();
    }
    
    public function loadWidgets()
    {
        $this->widgets = TenantWidget::when($this->pageId, function ($query) {
                $query->where('page_id', $this->pageId);
            })
            ->when($this->module, function ($query) {
                $query->where('module', $this->module);
            })
            ->when($this->position, function ($query) {
                $query->where('position', $this->position);
            })
            ->orderBy('order')
            ->get();
    }
    
    public function addWidget($widgetId)
    {
        // Widget maksimum sıra numarasını al
        $maxOrder = TenantWidget::when($this->pageId, function ($query) {
                $query->where('page_id', $this->pageId);
            })
            ->when($this->module, function ($query) {
                $query->where('module', $this->module);
            })
            ->where('position', $this->position)
            ->max('order') ?? 0;
        
        // Yeni widget ekle
        $tenantWidget = TenantWidget::create([
            'widget_id' => $widgetId,
            'page_id' => $this->pageId,
            'module' => $this->module,
            'position' => $this->position,
            'order' => $maxOrder + 1
        ]);
        
        // Widgetları yeniden yükle
        $this->loadWidgets();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget eklendi.',
            'type' => 'success'
        ]);
    }
    
    public function removeWidget($tenantWidgetId)
    {
        $tenantWidget = TenantWidget::findOrFail($tenantWidgetId);
        $tenantWidget->delete();
        
        // Widgetları yeniden yükle
        $this->loadWidgets();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget kaldırıldı.',
            'type' => 'success'
        ]);
    }
    
    public function updateWidgetOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            TenantWidget::where('id', $id)
                ->update(['order' => $index + 1]);
        }
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget sıralaması güncellendi.',
            'type' => 'success'
        ]);
    }
    
    public function openWidgetSettings($tenantWidgetId)
    {
        $this->dispatch('openWidgetSettings', $tenantWidgetId);
    }
    
    public function refreshWidgets()
    {
        $this->loadWidgets();
    }
    
    public function render()
    {
        // Kullanılabilir widget'ları getir
        $availableWidgets = [];
        
        if ($this->module) {
            // Modül için uygun widget'lar
            $moduleId = app('module.service')->getModuleIdByName($this->module);
            $availableWidgets = $this->widgetService->getWidgetsForModule($moduleId);
        } else {
            // Sayfa için tüm aktif widget'lar
            $availableWidgets = $this->widgetService->getActiveWidgets();
        }
        
        $page = null;
        if ($this->pageId) {
            $page = Page::find($this->pageId);
        }
        
        return view('widgetmanagement::livewire.widget-section-component', [
            'availableWidgets' => $availableWidgets,
            'page' => $page,
            'positionLabels' => [
                'top' => 'Üst Alan',
                'left' => 'Sol Kenar',
                'right' => 'Sağ Kenar',
                'bottom' => 'Alt Alan',
                'center' => 'Merkez'
            ]
        ]);
    }
}