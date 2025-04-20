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

#[Layout('admin.layout')]
class WidgetGalleryComponent extends Component
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
    
    public $showNameModal = false;
    public $selectedWidgetId = null;
    public $newWidgetName = '';
    
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
        // Seçilen widget ID'sini kaydet ve modal'ı göster
        $this->selectedWidgetId = $widgetId;
        $widget = Widget::findOrFail($widgetId);
        $this->newWidgetName = $widget->name;
        $this->showNameModal = true;
    }
    
    public function createInstanceWithName()
    {
        if (!$this->selectedWidgetId || empty($this->newWidgetName)) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Bileşen adı boş olamaz.',
                'type' => 'error'
            ]);
            return;
        }
        
        // Widget şablonundan yeni bir TenantWidget oluştur
        $widget = Widget::findOrFail($this->selectedWidgetId);
        
        // Mevcut sıra numarasını bul
        $maxOrder = TenantWidget::max('order') ?? 0;
        
        // Yeni widget oluştur
        $tenantWidget = TenantWidget::create([
            'widget_id' => $this->selectedWidgetId,
            'order' => $maxOrder + 1,
            'settings' => [
                'unique_id' => (string) \Illuminate\Support\Str::uuid(),
                'title' => $this->newWidgetName
            ]
        ]);
        
        if ($tenantWidget) {
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Yeni bileşen oluşturuldu.',
                'type' => 'success'
            ]);
            
            // Modal'ı kapat ve değerleri temizle
            $this->resetModal();
            
            return redirect()->route('admin.widgetmanagement.index');
        }
    }
    
    public function resetModal()
    {
        $this->showNameModal = false;
        $this->selectedWidgetId = null;
        $this->newWidgetName = '';
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
        
        // Tüm kategorileri getir
        $categories = WidgetCategory::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // Kullanılabilir şablonları getir
        $query = Widget::where('is_active', true)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->when($this->typeFilter, function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->when($this->categoryFilter, function ($q) {
                $q->where('widget_category_id', $this->categoryFilter);
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
            'categories' => $categories,
            'hasRootPermission' => $hasRootPermission
        ]);
    }
}