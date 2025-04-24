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
    public $perPage = 12;
    
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
        // Ana kategorileri getir
        $parentCategories = WidgetCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->withCount(['widgets' => function($query) {
                $query->where('type', 'file');
            }, 'children'])
            ->orderBy('order')
            ->get();
        
        // Eğer bir ana kategori seçilmişse, o kategorinin alt kategorilerini getir
        $childCategories = collect([]);
        if ($this->parentCategoryFilter) {
            $childCategories = WidgetCategory::where('parent_id', $this->parentCategoryFilter)
                ->where('is_active', true)
                ->withCount(['widgets' => function($query) {
                    $query->where('type', 'file');
                }])
                ->orderBy('order')
                ->get();
        }
        
        // File tipindeki widgetları getir
        $query = Widget::where('widgets.is_active', true)
            ->where('type', 'file')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('file_path', 'like', "%{$this->search}%");
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