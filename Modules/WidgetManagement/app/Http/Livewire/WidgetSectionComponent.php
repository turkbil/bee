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
    public $position;
    public $widgets = [];
    public $allPositionsWidgets = [];
    public $showAllPositions = false;
    
    protected $widgetService;
    
    protected $listeners = [
        'addWidget' => 'addWidget',
        'refreshWidgets' => 'refreshWidgets',
    ];
    
    public function boot(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }
    
    public function mount($position = 'top', $widgetId = null, $overview = false)
    {
        $this->position = $position;
        $this->showAllPositions = $overview;
        
        if ($this->showAllPositions) {
            $this->loadAllPositionsWidgets();
        } else {
            $this->loadWidgets();
        }
        
        // Eğer widgetId parametresi geldiyse, widget ekleme modalını otomatik aç
        if ($widgetId) {
            // Gelen widget ID'ye göre işlem yap
            $this->dispatch('openAddWidgetModal', ['preSelectedWidgetId' => $widgetId]);
        }
    }
    
    public function loadWidgets()
    {
        $this->widgets = TenantWidget::when($this->position, function ($query) {
                $query->where('position', $this->position);
            })
            ->with('widget')
            ->orderBy('order')
            ->get();
    }
    
    public function loadAllPositionsWidgets()
    {
        // Tüm konumlar için widget'ları yükle
        $positions = ['top', 'center-top', 'left', 'center', 'right', 'center-bottom', 'bottom'];
        
        foreach ($positions as $pos) {
            $this->allPositionsWidgets[$pos] = TenantWidget::where('position', $pos)
                ->with('widget')
                ->orderBy('order')
                ->get();
        }
        
        // Geçerli konum için normal widgets'ı da yükle
        $this->loadWidgets();
    }
    
    public function addWidget($widgetId, $targetPosition = null)
    {
        // Widget için kullanılacak position değerini belirle
        $position = $targetPosition ?? $this->position;
        
        // Genel bakış sayfasında pozisyon null olabilir, client tarafından gelen değeri kullan
        if ($this->showAllPositions && !$position) {
            // JS tarafından gönderilen position değerini kullanmalıyız
            return $this->addScriptToGetPosition($widgetId);
        }
        
        // Position null kontrolü
        if (!$position) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Widget eklemek için geçerli bir konum gereklidir.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Widget maksimum sıra numarasını al
        $maxOrder = TenantWidget::where('position', $position)
            ->max('order') ?? 0;
        
        // Yeni widget ekle
        $tenantWidget = TenantWidget::create([
            'widget_id' => $widgetId,
            'position' => $position,
            'order' => $maxOrder + 1,
            'settings' => [
                'unique_id' => (string) \Illuminate\Support\Str::uuid(),
                'title' => Widget::find($widgetId)->name ?? 'Yeni Widget'
            ]
        ]);
        
        // Widgetları yeniden yükle
        if ($this->showAllPositions) {
            $this->loadAllPositionsWidgets();
        } else {
            $this->loadWidgets();
        }
        
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
        if ($this->showAllPositions) {
            $this->loadAllPositionsWidgets();
        } else {
            $this->loadWidgets();
        }
    }
    
    /**
     * JavaScript'ten pozisyon bilgisini almak için script ekler ve çalıştırır
     */
    private function addScriptToGetPosition($widgetId)
    {
        // Client tarafında position değerini al ve sunucuya gönder
        $script = "if (window.widgetPositionToAdd) {\n";
        $script .= "    $this->dispatchSelf('addWidgetWithPosition', {widgetId: $widgetId, position: window.widgetPositionToAdd});\n";
        $script .= "} else {\n";
        $script .= "    alert('Lütfen önce bir widget konumu seçin!');\n";
        $script .= "}";
        
        $this->dispatch('eval', ['js' => $script]);
    }
    
    /**
     * JavaScript'ten gelen position bilgisiyle widget ekler
     */
    public function addWidgetWithPosition($data)
    {
        $this->addWidget($data['widgetId'], $data['position']);
    }
    
    /**
     * Widget'i sil
     */
    public function deleteWidget($widgetId)
    {
        $widget = TenantWidget::find($widgetId);
        if ($widget) {
            $widgetName = $widget->settings['title'] ?? 'Widget';
            $widget->delete();
            
            // Tüm widgetları yeniden yükle
            if ($this->showAllPositions) {
                $this->loadAllPositionsWidgets();
            } else {
                $this->loadWidgets();
            }
            
            // Başarı mesajı göster
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"$widgetName\" widget'ı silindi.",
                'type' => 'success'
            ]);
            
            return true;
        }
        
        // Hata mesajı göster
        $this->dispatch('toast', [
            'title' => 'Hata!',
            'message' => 'Widget bulunamadı.',
            'type' => 'error'
        ]);
        
        return false;
    }

    public function moveWidgetToPosition($widgetId, $newPosition)
    {
        // Null position kontrolü
        if (empty($newPosition)) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Taşımak için geçerli bir hedef konum gereklidir.',
                'type' => 'error'
            ]);
            return;
        }
        
        $widget = TenantWidget::find($widgetId);
        if ($widget) {
            $oldPosition = $widget->position;
            
            // Yeni pozisyondaki maksimum sıra numarası
            $maxOrder = TenantWidget::where('position', $newPosition)->max('order') ?? 0;
            
            // Widget'ı güncelle
            $widget->update([
                'position' => $newPosition,
                'order' => $maxOrder + 1
            ]);
            
            // Toast mesajı göster
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Widget başarıyla taşındı.',
                'type' => 'success'
            ]);
            
            // Tüm pozisyonların widget'larını yeniden yükle
            $this->loadAllPositionsWidgets();
        }
    }
    
    public function render()
    {
        // Kullanılabilir widget'ları getir
        $availableWidgets = $this->widgetService->getActiveWidgets();
        
        return view('widgetmanagement::livewire.widget-section-component', [
            'availableWidgets' => $availableWidgets,
            'positionLabels' => [
                'top' => 'Üst Alan',
                'center-top' => 'Merkez-Üst', 
                'left' => 'Sol Kenar',
                'center' => 'Merkez',
                'right' => 'Sağ Kenar',
                'center-bottom' => 'Merkez-Alt',
                'bottom' => 'Alt Alan'
            ]
        ]);
    }
}