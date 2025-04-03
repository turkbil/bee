<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;

#[Layout('admin.layout')]
class WidgetComponent extends Component
{
    use WithPagination;
    
    #[Url]
    public $search = '';
    
    #[Url]
    public $typeFilter = '';
    
    #[Url]
    public $perPage = 12;
    
    public $viewMode = 'active'; // active veya gallery
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedTypeFilter()
    {
        $this->resetPage();
    }
    
    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }
    
    public function createInstance($widgetId)
    {
        // Widget şablonundan yeni bir TenantWidget oluştur
        $widget = Widget::findOrFail($widgetId);
        
        // Mevcut sıra numarasını bul
        $maxOrder = TenantWidget::max('order') ?? 0;
        
        // Yeni widget oluştur
        $tenantWidget = TenantWidget::create([
            'widget_id' => $widgetId,
            'position' => 'content',
            'order' => $maxOrder + 1,
            'settings' => [
                'unique_id' => (string) \Illuminate\Support\Str::uuid(),
                'title' => $widget->name
            ]
        ]);
        
        if ($tenantWidget) {
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Yeni bileşen oluşturuldu.',
                'type' => 'success'
            ]);
            
            // Aktif bileşenler görünümüne geç
            $this->viewMode = 'active';
        }
    }
    
    public function deleteInstance($tenantWidgetId)
    {
        $tenantWidget = TenantWidget::findOrFail($tenantWidgetId);
        $name = $tenantWidget->settings['title'] ?? 'Bileşen';
        
        if ($tenantWidget->delete()) {
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "$name silindi.",
                'type' => 'success'
            ]);
        }
    }
    
    public function render()
    {
        if ($this->viewMode == 'active') {
            // Aktif kullanılan tüm tenant widget'ları getir
            $query = TenantWidget::with('widget')
                ->when($this->search, function ($q) {
                    $q->where('settings->title', 'like', "%{$this->search}%")
                      ->orWhereHas('widget', function($wq) {
                          $wq->where('name', 'like', "%{$this->search}%");
                      });
                })
                ->when($this->typeFilter, function ($q) {
                    $q->whereHas('widget', function($wq) {
                        $wq->where('type', $this->typeFilter);
                    });
                });
                
            $instances = $query->orderBy('updated_at', 'desc')
                ->paginate($this->perPage);
                
            $entities = $instances;
        } else {
            // Kullanılabilir şablonları getir
            $query = Widget::where('is_active', true)
                ->when($this->search, function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%");
                })
                ->when($this->typeFilter, function ($q) {
                    $q->where('type', $this->typeFilter);
                });
                
            $templates = $query->orderBy('name')
                ->paginate($this->perPage);
                
            $entities = $templates;
        }
            
        return view('widgetmanagement::livewire.widget-component', [
            'entities' => $entities,
            'types' => [
                'static' => 'Statik',
                'dynamic' => 'Dinamik',
                'module' => 'Modül',
                'content' => 'İçerik'
            ]
        ]);
    }
}