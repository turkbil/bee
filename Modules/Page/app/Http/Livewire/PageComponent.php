<?php
namespace Modules\Page\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Page\App\Models\Page;

class PageComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $pageId;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    protected $listeners = [
        'deleteConfirmed' => 'deletePage',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->pageId = $id;
        $this->emit('openDeleteModal');
    }

    public function deletePage()
    {
        $page = Page::findOrFail($this->pageId);

        $page->delete();

        $this->emit('toast', 'success', "{$page->title} başarıyla silindi.");
        $this->resetPage();
    }

    public function render()
    {
        $tenantId = tenancy()->tenant->id;
        $pages    = Page::where('tenant_id', $tenantId)
            ->where('title', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('page::livewire.page-component', compact('pages'));
    }
}
