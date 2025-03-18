<?php
namespace Modules\Page\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Page\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Page\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Page\App\Models\Page;

#[Layout('admin.layout')]
class PageComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'page_id';

    #[Url]
    public $sortDirection = 'desc';

    protected function getModelClass()
    {
        return Page::class;
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
        $page = Page::where('page_id', $id)->first();
    
        if ($page) {
            $page->update(['is_active' => !$page->is_active]);
            
            log_activity(
                $page,
                $page->is_active ? 'aktif edildi' : 'pasif edildi'
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$page->title}\" " . ($page->is_active ? 'aktif' : 'pasif') . " edildi.",
                'type' => $page->is_active ? 'success' : 'warning',
            ]);
        }
    }

    public function render()
    {
        $query = Page::where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
    
        $pages = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    
        return view('page::livewire.page-component', [
            'pages' => $pages,
        ]);
    }
}
