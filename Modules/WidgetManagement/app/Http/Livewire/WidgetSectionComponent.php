<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Services\WidgetService;
use Modules\Page\app\Models\Page;
use Livewire\Attributes\On;

#[Layout('admin.layout')]
class WidgetSectionComponent extends Component
{
    public $pageId;
    public $module;
    public $position;
    public $widgets = [];
    
    protected $widgetService;
    
    protected $listeners = [
        'addWidget' => 'addWidget',
        'refreshWidgets' => 'refreshWidgets',
        'openWidgetSettings' => 'redirectToWidgetSettings'
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
            ->with('widget')
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
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache();
        
        // Widgetları yeniden yükle
        $this->loadWidgets();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget kaldırıldı.',
            'type' => 'success'
        ]);
    }
    
    #[On('updateOrder')]
    public function updateOrder($list)
    {
        if (!is_array($list)) {
            return;
        }

        foreach ($list as $item) {
            if (!isset($item['value'], $item['order'])) {
                continue;
            }

            TenantWidget::where('id', $item['value'])
                ->update(['order' => $item['order']]);
        }
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache();
        
        // Widgetları yeniden yükle
        $this->loadWidgets();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget sıralaması güncellendi.',
            'type' => 'success'
        ]);
    }
    
    public function redirectToWidgetSettings($tenantWidgetId)
    {
        return redirect()->route('admin.widgetmanagement.settings', $tenantWidgetId);
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
            try {
                // Modül için uygun widget'lar
                $moduleId = null;
                if (app()->bound('module.service')) {
                    $moduleId = app('module.service')->getModuleIdByName($this->module);
                }
                
                if ($moduleId) {
                    $availableWidgets = $this->widgetService->getWidgetsForModule($moduleId);
                } else {
                    $availableWidgets = $this->widgetService->getActiveWidgets();
                }
            } catch (\Exception $e) {
                // Hata durumunda aktif tüm widget'ları getir
                $availableWidgets = $this->widgetService->getActiveWidgets();
            }
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