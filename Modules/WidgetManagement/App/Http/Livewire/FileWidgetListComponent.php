<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Component as LWComponent;
use Illuminate\Support\Facades\Auth;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetCategory;

#[Layout('admin.layout')]
#[LWComponent('modules.widget-management.app.http.livewire.file-widget-list-component')]
class FileWidgetListComponent extends Component
{
    use WithPagination;
    
    #[Url]
    public $search = '';
    
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
    
    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
    
    public function updatedParentCategoryFilter()
    {
        $this->categoryFilter = '';
        $this->resetPage();
    }
    
    public function render()
    {
        // File tipindeki widgetları getir (sayılar için)
        $fileWidgetsQuery = Widget::where('type', 'file')->where('is_active', true);
        
        // File widget IDs'lerini önce çek (tek query)
        $fileWidgetIds = $fileWidgetsQuery->pluck('id')->toArray();

        // Ana kategorileri getir (EAGER LOADING ile N+1 önlendi)
        $parentCategories = WidgetCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->where('widget_category_id', '!=', 1) // Moduller (1 nolu kategori) hariç tut
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->withCount(['widgets' => function($query) use ($fileWidgetIds) {
                $query->whereIn('id', $fileWidgetIds);
            }, 'children'])
            ->orderBy('order')
            ->get();

        // Alt kategorilerdeki widget sayılarını TEK query ile al (N+1 önlendi)
        $childCategoryIds = $parentCategories->pluck('children')->flatten()->pluck('widget_category_id')->toArray();

        if (!empty($childCategoryIds)) {
            $childWidgetCounts = Widget::whereIn('widget_category_id', $childCategoryIds)
                ->whereIn('id', $fileWidgetIds)
                ->selectRaw('widget_category_id, COUNT(*) as count')
                ->groupBy('widget_category_id')
                ->pluck('count', 'widget_category_id');

            // Her ana kategori için toplam widget sayısını hesapla
            $parentCategories->each(function($category) use ($childWidgetCounts) {
                $childWidgetsCount = $category->children->sum(function($child) use ($childWidgetCounts) {
                    return $childWidgetCounts[$child->widget_category_id] ?? 0;
                });

                $category->total_widgets_count = $category->widgets_count + $childWidgetsCount;
            });
        } else {
            // Alt kategori yoksa sadece kendi widget sayısını kullan
            $parentCategories->each(function($category) {
                $category->total_widgets_count = $category->widgets_count;
            });
        }
        
        // Eğer bir ana kategori seçilmişse, o kategorinin alt kategorilerini getir
        $childCategories = collect([]);
        if ($this->parentCategoryFilter) {
            $childCategories = WidgetCategory::where('parent_id', $this->parentCategoryFilter)
                ->where('is_active', true)
                ->withCount(['widgets' => function($query) use ($fileWidgetIds) {
                    $query->whereIn('id', $fileWidgetIds);
                }])
                ->orderBy('order')
                ->get();
        }
        
        // File tipindeki widgetları getir
        $query = Widget::where('widgets.is_active', true)
            ->where('type', 'file')
            ->whereHas('category', function($cq) {
                $cq->where('widget_category_id', '!=', 1)
                   ->whereNull('parent_id')
                   ->orWhere('parent_id', '!=', 1); // Moduller kategorisi (1 nolu) ve alt kategorileri hariç tut
            })
            ->when($this->search, function ($q) {
                $q->where('widgets.name', 'like', "%{$this->search}%")
                  ->orWhere('widgets.description', 'like', "%{$this->search}%")
                  ->orWhere('widgets.file_path', 'like', "%{$this->search}%");
            })
            ->when($this->parentCategoryFilter, function ($q) {
                if ($this->categoryFilter) {
                    // Eğer alt kategori seçilmişse, sadece o kategoriyi filtrele
                    $q->where('widgets.widget_category_id', $this->categoryFilter);
                } else {
                    // Ana kategori seçilmişse ve alt kategori seçilmemişse
                    // Ana kategoriye ait tüm alt kategorileri dahil et
                    $q->where(function($query) {
                        $query->where('widgets.widget_category_id', $this->parentCategoryFilter)
                              ->orWhereHas('category', function($cq) {
                                  $cq->where('parent_id', $this->parentCategoryFilter);
                              });
                    });
                }
            })
            ->when(!$this->parentCategoryFilter && $this->categoryFilter, function ($q) {
                $q->where('widgets.widget_category_id', $this->categoryFilter);
            });
            
        // Önce kategoriye göre, sonra alfabetik olarak sırala
        $widgets = $query->join('widget_categories', 'widgets.widget_category_id', '=', 'widget_categories.widget_category_id')
            ->orderBy('widget_categories.order')
            ->orderBy('widgets.name')
            ->select('widgets.*')
            ->paginate($this->perPage);
        
        return view('widgetmanagement::livewire.file-widget-list-component', [
            'widgets' => $widgets,
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories
        ]);
    }
}