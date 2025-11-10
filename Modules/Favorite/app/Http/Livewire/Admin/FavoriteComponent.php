<?php

declare(strict_types=1);

namespace Modules\Favorite\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Favorite\App\Services\FavoriteService;
use Modules\Favorite\App\Models\Favorite;

#[Layout('admin.layout')]
class FavoriteComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage;

    #[Url]
    public $sortField = 'id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $filterType = 'all'; // all, user, model

    private FavoriteService $favoriteService;

    public function boot(FavoriteService $favoriteService): void
    {
        $this->favoriteService = $favoriteService;
        $this->perPage = $this->perPage ?? config('modules.pagination.admin_per_page', 10);
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

    public function updatedFilterType()
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

    public function deleteFavorite(int $id): void
    {
        try {
            $favorite = Favorite::findOrFail($id);
            $favorite->delete();

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Favori ba_ar1yla silindi',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = Favorite::with(['user', 'favoritable'])
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType !== 'all', function ($q) {
                if ($this->filterType === 'user') {
                    $q->whereNotNull('user_id');
                } elseif ($this->filterType === 'model') {
                    $q->whereNotNull('favoritable_type');
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $favorites = $query->paginate((int) $this->perPage);

        return view('favorite::admin.livewire.favorite-component', [
            'favorites' => $favorites,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
