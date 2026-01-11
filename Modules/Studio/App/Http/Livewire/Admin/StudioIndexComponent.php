<?php
namespace Modules\Studio\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Page\App\Models\Page;

#[Layout('admin.layout')]
class StudioIndexComponent extends Component
{
    public function render()
    {
        $recentPages = [];
        $totalPages = 0;
        $activeTenantWidgets = 0;
        
        if (class_exists('Modules\Page\App\Models\Page')) {
            $recentPages = Page::where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
            $totalPages = Page::count();
        }
        
        if (class_exists('Modules\WidgetManagement\App\Models\TenantWidget')) {
            $activeTenantWidgets = \Modules\WidgetManagement\App\Models\TenantWidget::where('is_active', true)->count();
        }
        
        return view('studio::admin.livewire.studio-index-component', [
            'recentPages' => $recentPages,
            'totalPages' => $totalPages,
            'activeTenantWidgets' => $activeTenantWidgets
        ]);
    }
}