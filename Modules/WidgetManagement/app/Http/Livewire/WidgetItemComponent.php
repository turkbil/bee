<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Modules\WidgetManagement\app\Services\WidgetItemService;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class WidgetItemComponent extends Component
{
    use WithFileUploads;
    
    public $tenantWidgetId;
    public $tenantWidget = null;
    public $items = [];
    public $isStaticWidget = false;
    
    protected $itemService;
    
    protected $listeners = [
        'updateItemOrder',
        'itemOrderUpdated' => 'updateItemOrder'
    ];
    
    public function boot(WidgetItemService $itemService)
    {
        $this->itemService = $itemService;
    }
    
    public function mount($tenantWidgetId)
    {
        $this->tenantWidgetId = $tenantWidgetId;
        $this->loadItems();
        
        $this->tenantWidget = TenantWidget::with('widget')->findOrFail($tenantWidgetId);
        
        // Widget tipini belirle - önemli değişken
        $this->isStaticWidget = $this->tenantWidget->widget->type === 'static';
        
        // Statik bileşen kontrolü
        if ($this->isStaticWidget) {
            // Öğe yoksa otomatik bir içerik öğesi oluştur
            if ($this->items->isEmpty()) {
                $content = [
                    'title' => $this->tenantWidget->settings['title'] ?? $this->tenantWidget->widget->name,
                    'is_active' => true,
                    'unique_id' => (string) Str::uuid()
                ];
                
                $this->itemService->addItem($this->tenantWidgetId, $content);
                $this->loadItems();
            }
        }
    }
    
    public function loadItems()
    {
        $this->items = $this->itemService->getItemsForWidget($this->tenantWidgetId);
    }

    public function toggleItemActive($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $content = $item->content;
        
        // Aktif/pasif durumunu tersine çevirme
        $content['is_active'] = !($content['is_active'] ?? false);
        
        // İçerik güncelleme
        $this->itemService->updateItem($itemId, $content);
        
        // Öğeleri yeniden yükle
        $this->loadItems();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'İçerik durumu ' . ($content['is_active'] ? 'aktif' : 'pasif') . ' olarak güncellendi.',
            'type' => 'success'
        ]);
    }
    
    public function deleteItem($itemId)
    {
        try {
            // Statik bileşen kontrolü - tek öğe varsa silinemez
            if ($this->isStaticWidget && $this->items->count() <= 1) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Statik bileşenin tek içerik öğesi silinemez.',
                    'type' => 'error'
                ]);
                return;
            }
            
            $this->itemService->deleteItem($itemId);
            
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğe başarıyla silindi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Öğe silinirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function updateItemOrder($items)
    {
        // Sadece geçerli bir item dizisi ise devam et
        if (!is_array($items) || empty($items)) {
            return;
        }
        
        try {
            $this->itemService->reorderItems($this->tenantWidgetId, $items);
            
            $this->loadItems();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Öğeler başarıyla sıralandı.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Öğeler sıralanırken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        // Statik widget kontrolü - doğrudan düzenleme sayfasına yönlendir
        if ($this->isStaticWidget && !empty($this->items) && $this->items->count() > 0) {
            $firstItem = $this->items->first();
            return redirect()->route('admin.widgetmanagement.item.manage', [$this->tenantWidgetId, $firstItem->id]);
        }
        
        return view('widgetmanagement::livewire.widget-item-component');
    }
}