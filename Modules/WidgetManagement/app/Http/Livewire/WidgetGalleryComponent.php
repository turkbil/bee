<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;

#[Layout('admin.layout')]
class WidgetGalleryComponent extends Component
{
    use WithPagination;
    
    #[Url]
    public $search = '';
    
    #[Url]
    public $typeFilter = '';
    
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
            
            return redirect()->route('admin.widgetmanagement.index');
        }
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
        
        return view('widgetmanagement::livewire.widget-gallery-component', [
            'templates' => $templates,
            'types' => [
                'static' => 'Statik',
                'dynamic' => 'Dinamik',
                'module' => 'Modül',
                'content' => 'İçerik'
            ],
            'hasRootPermission' => $hasRootPermission
        ]);
    }
}