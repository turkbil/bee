<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Spatie\Permission\Models\Role;

#[Layout('admin.layout')]
class WidgetComponent extends Component
{
    use WithPagination;
    
    #[Url]
    public $search = '';
    
    #[Url]
    public $typeFilter = '';
    
    #[Url]
    public $categoryFilter = '';
    
    #[Url]
    public $perPage = 12;
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedTypeFilter()
    {
        $this->resetPage();
    }
    
    public function updatedCategoryFilter()
    {
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
    
    public function toggleActive($id)
    {
        $tenantWidget = TenantWidget::findOrFail($id);
        
        // TenantWidget'ın aktif durumunu değiştir
        $tenantWidget->is_active = !$tenantWidget->is_active;
        $tenantWidget->save();
        
        $status = $tenantWidget->is_active ? 'aktifleştirildi' : 'devre dışı bırakıldı';
        $type = $tenantWidget->is_active ? 'success' : 'warning';
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => "Bileşen $status.",
            'type' => $type
        ]);
    }
    
    public function render()
    {
        // Root yetkisine sahip olup olmadığını kontrol et
        $user = Auth::user();
        $hasRootPermission = false;
        
        // Kullanıcı oturum açmışsa ve hasRole metodu tanımlıysa kontrol et
        if ($user) {
            if (method_exists($user, 'hasRole')) {
                $hasRootPermission = $user->hasRole('root');
            } else {
                $hasRootPermission = false;
            }
        }
        
        // Tüm widget kategorilerini getir
        $categories = WidgetCategory::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Aktif kullanılan tüm tenant widget'ları getir
        $query = TenantWidget::with(['widget', 'items'])
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
            })
            ->when($this->categoryFilter, function ($q) {
                $q->whereHas('widget', function($wq) {
                    $wq->where('widget_category_id', $this->categoryFilter);
                });
            });
            
        $instances = $query->orderBy('updated_at', 'desc')
            ->paginate($this->perPage);
            
        $entities = $instances;
            
        return view('widgetmanagement::livewire.widget-component', [
            'entities' => $entities,
            'types' => [
                'static' => 'Statik',
                'dynamic' => 'Dinamik',
                'module' => 'Modül',
                'content' => 'İçerik'
            ],
            'categories' => $categories,
            'hasRootPermission' => $hasRootPermission
        ]);
    }
}