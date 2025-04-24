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
        
        // Tenant widget'ları - file ve module olmayanları bul (sayılar için)
        $standardWidgetsQuery = TenantWidget::whereHas('widget', function($q) {
            $q->where('type', '!=', 'file')->where('type', '!=', 'module');
        });
        
        // Ana kategorileri getir
        $parentCategories = WidgetCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->withCount(['widgets' => function($query) use ($standardWidgetsQuery) {
                $query->whereHas('tenantWidgets', function($q) use ($standardWidgetsQuery) {
                    $q->whereIn('id', $standardWidgetsQuery->pluck('id'));
                })->where('type', '!=', 'file')->where('type', '!=', 'module');
            }, 'children'])
            ->orderBy('order')
            ->get();
        
        // Her ana kategori için toplam widget sayısını hesapla
        $parentCategories->each(function($category) use ($standardWidgetsQuery) {
            // Alt kategorilerin ID'lerini al
            $childCategoryIds = $category->children->pluck('widget_category_id');
            
            // Alt kategorilere ait widgetlara bağlı tenant widgetları say
            $childWidgetsCount = TenantWidget::whereHas('widget', function($q) use ($childCategoryIds) {
                $q->whereIn('widget_category_id', $childCategoryIds)
                  ->where('type', '!=', 'file')
                  ->where('type', '!=', 'module');
            })->count();
            
            // Ana kategorideki widget sayısı + alt kategorilerdeki widget sayısı
            $category->total_widgets_count = $category->widgets_count + $childWidgetsCount;
        });
        
        // Eğer bir ana kategori seçilmişse, o kategorinin alt kategorilerini getir
        $childCategories = collect([]);
        if ($this->parentCategoryFilter) {
            $childCategories = WidgetCategory::where('parent_id', $this->parentCategoryFilter)
                ->where('is_active', true)
                ->withCount(['widgets' => function($query) use ($standardWidgetsQuery) {
                    $query->whereHas('tenantWidgets', function($q) use ($standardWidgetsQuery) {
                        $q->whereIn('id', $standardWidgetsQuery->pluck('id'));
                    })->where('type', '!=', 'file')->where('type', '!=', 'module');
                }])
                ->orderBy('order')
                ->get();
        }
        
        // Aktif kullanılan tüm tenant widget'ları getir
        $query = TenantWidget::with(['widget', 'items'])
            ->whereHas('widget', function($q) {
                $q->where('type', '!=', 'file') // file tipindeki widgetları hariç tut
                  ->where('type', '!=', 'module'); // module tipindeki widgetları hariç tut
            })
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
            ->when($this->parentCategoryFilter, function ($q) {
                if ($this->categoryFilter) {
                    // Eğer alt kategori seçilmişse, sadece o kategoriyi filtrele
                    $q->whereHas('widget', function($wq) {
                        $wq->where('widget_category_id', $this->categoryFilter);
                    });
                } else {
                    // Ana kategori seçilmişse ve alt kategori seçilmemişse
                    // Ana kategoriye ait tüm alt kategorileri dahil et
                    $q->whereHas('widget', function($wq) {
                        $wq->where(function($query) {
                            $query->where('widget_category_id', $this->parentCategoryFilter)
                                  ->orWhereHas('category', function($cq) {
                                      $cq->where('parent_id', $this->parentCategoryFilter);
                                  });
                        });
                    });
                }
            })
            ->when(!$this->parentCategoryFilter && $this->categoryFilter, function ($q) {
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
                'content' => 'İçerik'
            ],
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories,
            'hasRootPermission' => $hasRootPermission
        ]);
    }
}