<?php
namespace Modules\Page\App\Http\Livewire\Admin;

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
        $this->perPage = (int) $this->perPage;
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
            // Eğer ana sayfa ise pasif yapılmasına izin verme
            if ($page->is_homepage && $page->is_active) {
                $this->dispatch('toast', [
                    'title' => t('common.warning'),
                    'message' => t('page::messages.homepage_cannot_be_deactivated'),
                    'type' => 'warning',
                ]);
                return;
            }
            $page->update(['is_active' => !$page->is_active]);
            
            log_activity(
                $page,
                $page->is_active ? t('common.activated') : t('common.deactivated')
            );
    
            $this->dispatch('toast', [
                'title' => t('common.success'),
                'message' => t($page->is_active ? 'page::messages.page_activated' : 'page::messages.page_deactivated', ['title' => $page->title]),
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
    
        return view('page::admin.livewire.page-component', [
            'pages' => $pages,
        ]);
    }
}