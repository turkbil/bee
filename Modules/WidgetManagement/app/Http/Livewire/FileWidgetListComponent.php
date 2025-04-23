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
    public $perPage = 12;
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        // Tüm kategorileri getir
        $categories = WidgetCategory::where('is_active', true)
            ->orderBy('order')
            ->get();
        
        // File tipindeki widgetları getir
        $query = Widget::where('widgets.is_active', true)
            ->where('type', 'file')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('file_path', 'like', "%{$this->search}%");
            })
            ->when($this->categoryFilter, function ($q) {
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
            'categories' => $categories
        ]);
    }
}