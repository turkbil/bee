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
        $query = Widget::where('is_active', true)
            ->where('type', 'file')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('file_path', 'like', "%{$this->search}%");
            })
            ->when($this->categoryFilter, function ($q) {
                $q->where('widget_category_id', $this->categoryFilter);
            });
            
        $widgets = $query->orderBy('name')
            ->paginate($this->perPage);
        
        return view('widgetmanagement::livewire.file-widget-list-component', [
            'widgets' => $widgets,
            'categories' => $categories
        ]);
    }
}