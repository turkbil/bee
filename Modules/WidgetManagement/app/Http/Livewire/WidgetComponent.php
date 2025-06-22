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
    public $parentCategoryFilter = '';
    
    #[Url]
    public $perPage = 100;
    
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
    
    public function updatedParentCategoryFilter()
    {
        $this->categoryFilter = '';
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
        
        // Widget instance oluşturma log'u
        if ($tenantWidget && function_exists('log_activity')) {
            log_activity($tenantWidget, t('widgetmanagement.actions.created'));
        }
        
        if ($tenantWidget) {
            $this->dispatch('toast', [
                'title' => t('widgetmanagement.messages.success'),
                'message' => t('widgetmanagement.messages.widget_created'),
                'type' => 'success'
            ]);
        }
    }
    
    public function deleteInstance($tenantWidgetId)
    {
        $tenantWidget = TenantWidget::findOrFail($tenantWidgetId);
        $name = $tenantWidget->settings['title'] ?? t('widgetmanagement.widget.default_name');
        
        // Widget instance silme log'u
        if (function_exists('log_activity')) {
            log_activity($tenantWidget, t('widgetmanagement.actions.deleted'));
        }
        
        if ($tenantWidget->delete()) {
            $this->dispatch('toast', [
                'title' => t('widgetmanagement.messages.success'),
                'message' => "$name " . t('widgetmanagement.messages.widget_deleted'),
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
        
        // Widget toggle log'u
        if (function_exists('log_activity')) {
            log_activity($tenantWidget, $tenantWidget->is_active ? t('widgetmanagement.actions.activated') : t('widgetmanagement.actions.deactivated'));
        }
        
        $status = $tenantWidget->is_active ? t('widgetmanagement.messages.widget_activated') : t('widgetmanagement.messages.widget_deactivated');
        $type = $tenantWidget->is_active ? 'success' : 'warning';
        
        $this->dispatch('toast', [
            'title' => t('widgetmanagement.messages.success'),
            'message' => t('widgetmanagement.widget.component') . " $status.",
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
        
        // Önce central DB'den uygun widget ID'lerini çek
        $validWidgetIds = Widget::where('type', '!=', 'file')
            ->where('type', '!=', 'module')
            ->pluck('id')
            ->toArray();
            
        // Tenant widget'ları - file ve module olmayanları bul (sayılar için)
        $standardWidgetsQuery = TenantWidget::whereIn('widget_id', $validWidgetIds);
        
        // Ana kategorileri getir
        $parentCategories = WidgetCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->where('widget_category_id', '!=', 1) // Moduller (1 nolu kategori) hariç tut
            ->withCount(['widgets' => function($query) use ($validWidgetIds) {
                $query->whereIn('id', $validWidgetIds);
            }, 'children'])
            ->orderBy('order')
            ->get();
        
        // Her ana kategori için toplam widget sayısını hesapla
        $parentCategories->each(function($category) use ($validWidgetIds) {
            // Alt kategorilerin ID'lerini al
            $childCategoryIds = $category->children->pluck('widget_category_id');
            
            // Alt kategorilere ait widget ID'lerini bul
            $childWidgetIds = Widget::whereIn('widget_category_id', $childCategoryIds)
                ->whereIn('id', $validWidgetIds)
                ->pluck('id')
                ->toArray();
            
            // Alt kategorilere ait widgetlara bağlı tenant widgetları say
            $childWidgetsCount = TenantWidget::whereIn('widget_id', $childWidgetIds)->count();
            
            // Ana kategorideki widget sayısı + alt kategorilerdeki widget sayısı
            $category->total_widgets_count = $category->widgets_count + $childWidgetsCount;
        });
        
        // Eğer bir ana kategori seçilmişse, o kategorinin alt kategorilerini getir
        $childCategories = collect([]);
        if ($this->parentCategoryFilter) {
            $childCategories = WidgetCategory::where('parent_id', $this->parentCategoryFilter)
                ->where('is_active', true)
                ->withCount(['widgets' => function($query) use ($validWidgetIds) {
                    $query->whereIn('id', $validWidgetIds);
                }])
                ->orderBy('order')
                ->get();
        }
        
        // Moduller kategorisi hariç widget ID'lerini al
        $excludeModuleWidgetIds = Widget::whereHas('category', function($cq) {
            $cq->where('widget_category_id', '!=', 1); // Moduller kategorisini (1 nolu) hariç tut
        })->whereIn('id', $validWidgetIds)->pluck('id')->toArray();
        
        // Aktif kullanılan tüm tenant widget'ları getir
        $query = TenantWidget::with(['items'])
            ->whereIn('widget_id', $excludeModuleWidgetIds)
            ->when($this->search, function ($q) use ($validWidgetIds) {
                // Eşleşen widget ID'lerini bul
                $searchWidgetIds = Widget::where('name', 'like', "%{$this->search}%")
                    ->whereIn('id', $validWidgetIds)
                    ->pluck('id')
                    ->toArray();
                    
                $q->where(function($query) use ($searchWidgetIds) {
                    $query->where('settings->title', 'like', "%{$this->search}%")
                          ->orWhereIn('widget_id', $searchWidgetIds);
                });
            })
            ->when($this->typeFilter, function ($q) use ($validWidgetIds) {
                $typeWidgetIds = Widget::where('type', $this->typeFilter)
                    ->whereIn('id', $validWidgetIds)
                    ->pluck('id')
                    ->toArray();
                $q->whereIn('widget_id', $typeWidgetIds);
            })
            ->when($this->parentCategoryFilter, function ($q) use ($validWidgetIds) {
                if ($this->categoryFilter) {
                    // Eğer alt kategori seçilmişse, sadece o kategoriyi filtrele
                    $categoryWidgetIds = Widget::where('widget_category_id', $this->categoryFilter)
                        ->whereIn('id', $validWidgetIds)
                        ->pluck('id')
                        ->toArray();
                    $q->whereIn('widget_id', $categoryWidgetIds);
                } else {
                    // Ana kategori seçilmişse ve alt kategori seçilmemişse
                    // Ana kategoriye ait tüm alt kategorileri dahil et
                    $childCategoryIds = WidgetCategory::where('parent_id', $this->parentCategoryFilter)
                        ->pluck('widget_category_id')
                        ->toArray();
                    $childCategoryIds[] = $this->parentCategoryFilter;
                    
                    $categoryWidgetIds = Widget::whereIn('widget_category_id', $childCategoryIds)
                        ->whereIn('id', $validWidgetIds)
                        ->pluck('id')
                        ->toArray();
                    $q->whereIn('widget_id', $categoryWidgetIds);
                }
            })
            ->when(!$this->parentCategoryFilter && $this->categoryFilter, function ($q) use ($validWidgetIds) {
                $categoryWidgetIds = Widget::where('widget_category_id', $this->categoryFilter)
                    ->whereIn('id', $validWidgetIds)
                    ->pluck('id')
                    ->toArray();
                $q->whereIn('widget_id', $categoryWidgetIds);
            });
            
        $instances = $query->orderBy('updated_at', 'desc')
            ->paginate($this->perPage);
            
        // Widget verilerini cache'leyerek template'e gönder
        $widgetIds = $instances->pluck('widget_id')->unique()->toArray();
        $widgets = Widget::with('category')->whereIn('id', $widgetIds)->get()->keyBy('id');
        
        $entities = $instances;
            
        return view('widgetmanagement::livewire.widget-component', [
            'entities' => $entities,
            'widgets' => $widgets,
            'types' => [
                'static' => t('widgetmanagement.types.static'),
                'dynamic' => t('widgetmanagement.types.dynamic'),
                'content' => t('widgetmanagement.types.content')
            ],
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories,
            'hasRootPermission' => $hasRootPermission
        ]);
    }
}