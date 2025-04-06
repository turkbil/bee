<?php
namespace Modules\ThemeManagement\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ThemeManagement\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\ThemeManagement\App\Models\Theme;

#[Layout('admin.layout')]
class ThemeManagementComponent extends Component
{
    use WithPagination, InlineEditTitle;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 12;

    #[Url]
    public $sortField = 'theme_id';

    #[Url]
    public $sortDirection = 'desc';

    protected function getModelClass()
    {
        return Theme::class;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $theme = Theme::where('theme_id', $id)->first();
    
        if ($theme) {
            $theme->update(['is_active' => !$theme->is_active]);
            
            log_activity(
                $theme,
                $theme->is_active ? 'aktif edildi' : 'pasif edildi'
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$theme->title}\" " . ($theme->is_active ? 'aktif' : 'pasif') . " edildi.",
                'type' => $theme->is_active ? 'success' : 'warning',
            ]);
        }
    }
    
    public function setDefault($id)
    {
        // Önce tüm temaları varsayılan olmaktan çıkar
        Theme::where('is_default', true)->update(['is_default' => false]);
        
        // Seçilen temayı varsayılan yap
        $theme = Theme::findOrFail($id);
        $theme->update(['is_default' => true]);
        
        log_activity(
            $theme,
            'varsayılan tema olarak ayarlandı'
        );
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => "\"{$theme->title}\" varsayılan tema olarak ayarlandı.",
            'type' => 'success',
        ]);
    }

    public function render()
    {
        $query = Theme::where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('folder_name', 'like', '%' . $this->search . '%');
            });
    
        $themes = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    
        return view('thememanagement::livewire.theme-management-component', [
            'themes' => $themes,
        ]);
    }
}