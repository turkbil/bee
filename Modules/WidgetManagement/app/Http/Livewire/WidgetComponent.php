<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Modules\WidgetManagement\app\Models\Widget;
use Modules\WidgetManagement\app\Services\WidgetService;

#[Layout('admin.layout')]
class WidgetComponent extends Component
{
    use WithPagination;
    
    public $search = '';
    public $typeFilter = '';
    public $activeOnly = true;
    
    protected $widgetService;
    
    public function boot(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }
    
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
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $widget->is_active ? 'Widget aktif edildi.' : 'Widget pasif edildi.',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        $query = Widget::query();
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }
        
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }
        
        if ($this->activeOnly) {
            $query->where('is_active', true);
        }
        
        $widgets = $query->orderBy('name')->paginate(12);
        
        return view('widgetmanagement::livewire.widget-component', [
            'widgets' => $widgets,
            'types' => [
                'static' => 'Statik',
                'dynamic' => 'Dinamik',
                'module' => 'Modül',
                'content' => 'İçerik'
            ]
        ]);
    }
}