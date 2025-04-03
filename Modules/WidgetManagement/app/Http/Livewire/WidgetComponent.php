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
    public $sortField = 'name';
    
    #[Url]
    public $sortDirection = 'asc';
    
    #[Url]
    public $perPage = 12;
    
    #[Url]
    public $activeOnly = true;
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedTypeFilter()
    {
        $this->resetPage();
    }
    
    public function updatedActiveOnly()
    {
        $this->resetPage();
    }
    
    public function toggleActive($id)
    {
        $widget = Widget::findOrFail($id);
        $widget->is_active = !$widget->is_active;
        $widget->save();
        
        log_activity(
            $widget,
            $widget->is_active ? 'aktif edildi' : 'pasif edildi'
        );
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $widget->is_active ? 'Widget aktif edildi.' : 'Widget pasif edildi.',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        // Önce tenant'ta kullanılan widget'ları bul
        $usedWidgetIds = TenantWidget::pluck('widget_id')->unique()->toArray();
        
        $query = Widget::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->when($this->typeFilter, function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->when($this->activeOnly, function ($q) {
                $q->where('is_active', true);
            });
            
        // Sıralama: Önce kullanılanlar, sonra alfabetik
        if (!empty($usedWidgetIds)) {
            $query->orderByRaw("CASE WHEN id IN (" . implode(',', $usedWidgetIds) . ") THEN 0 ELSE 1 END");
        }
        
        $widgets = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        return view('widgetmanagement::livewire.widget-component', [
            'widgets' => $widgets,
            'usedWidgetIds' => $usedWidgetIds,
            'types' => [
                'static' => 'Statik',
                'dynamic' => 'Dinamik',
                'module' => 'Modül',
                'content' => 'İçerik'
            ]
        ]);
    }
}