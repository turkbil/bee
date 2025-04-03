<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Services\WidgetService;
use Modules\Page\app\Models\Page;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
            'order' => $maxOrder + 1,
            'settings' => [
                'unique_id' => (string) \Illuminate\Support\Str::uuid(),
                'title' => Widget::find($widgetId)->name ?? 'Yeni Widget'
            ]
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
    public function updateOrder(array $list)
    {
        $updatedItems = [];
        $errors = [];

        if (empty($list)) {
            Log::warning('Boş veya geçersiz liste alındı.', [
                'user_id' => Auth::id(),
                'page_id' => $this->pageId,
                'module' => $this->module,
                'position' => $this->position,
            ]);
            return;
        }

        try {
            foreach ($list as $item) {
                if (!isset($item['value']) || !isset($item['order'])) {
                    Log::warning('Widget ID veya sıra bilgisi eksik', [
                        'item' => $item,
                        'user_id' => Auth::id(),
                    ]);
                    $errors[] = ['item' => $item, 'message' => 'Eksik bilgi: ID veya sıra.'];
                    continue; // Bu öğeyi atla ve devam et
                }

                $widgetId = $item['value'];
                $newOrder = $item['order'];

                $widget = TenantWidget::find($widgetId);

                if ($widget) {
                    $oldOrder = $widget->order;
                    $widget->order = $newOrder;
                    $widget->save();
                    $updatedItems[] = ['id' => $widget->id, 'old_order' => $oldOrder, 'new_order' => $newOrder];
                } else {
                    Log::warning('Widget bulunamadı', ['widget_id' => $widgetId, 'user_id' => Auth::id()]);
                    $errors[] = ['id' => $widgetId, 'message' => 'Widget bulunamadı.'];
                }
            }

            // Widget önbelleğini temizle
            $this->widgetService->clearWidgetCache();
            
            // Widgetları yeniden yükle
            $this->loadWidgets();

        } catch (\Exception $e) {
            Log::error('Widget güncellenirken hata oluştu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'list' => $list
            ]);
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Widget sıralaması güncellenirken bir hata oluştu.',
                'type' => 'error'
            ]);
            
            return;
        }

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Widget sıralaması güncellendi.',
            'type' => 'success'
        ]);
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