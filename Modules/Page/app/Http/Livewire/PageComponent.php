<?php
namespace Modules\Page\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Page\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Page\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Page\App\Models\Page;

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
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            return;
        }

        $page = Page::where('page_id', $id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($page) {
            $page->is_active = ! $page->is_active;
            $page->save();
            $status = $page->is_active ? 'aktif' : 'pasif';

            log_activity(
                'Sayfa',
                "\"{$page->title}\" {$status} yapıldı.",
                $page,
                [],
                $status
            );

            $this->dispatch('toast', [
                'title'   => 'Başarılı!',
                'message' => "\"{$page->title}\" {$status} yapıldı.",
                'type'    => 'success',
            ]);
        }
    }

    public function render()
    {
        $tenant = tenancy()->tenant;
    
        $query = Page::where('tenant_id', $tenant->id)
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
    
        $pages = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    
        return view('page::livewire.page-component', [
            'pages' => $pages,
        ])->layout('admin.layout');
    }
}
