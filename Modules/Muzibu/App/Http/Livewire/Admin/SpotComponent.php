<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Muzibu\App\Models\CorporateSpot;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;

class SpotComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $corporateFilter = '';

    public array $selectedIds = [];
    public bool $selectAll = false;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'spotDeleted' => '$refresh',
        'updateSpotOrder' => 'updateOrder',
    ];

    public function mount(): void
    {
        view()->share('pretitle', __('muzibu::admin.spot_management'));
        view()->share('title', __('muzibu::admin.spots'));
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->spots->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCorporateFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleEnabled(int $id): void
    {
        try {
            $spot = CorporateSpot::findOrFail($id);
            $spot->update(['is_enabled' => !$spot->is_enabled]);

            $status = $spot->is_enabled ? 'etkinleştirildi' : 'devre dışı bırakıldı';
            log_activity($spot, $status);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => "Spot $status.",
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

    public function toggleArchived(int $id): void
    {
        try {
            $spot = CorporateSpot::findOrFail($id);
            $spot->update(['is_archived' => !$spot->is_archived]);

            $status = $spot->is_archived ? 'arşivlendi' : 'arşivden çıkarıldı';
            log_activity($spot, $status);

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => "Spot $status.",
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

    public function deleteSpot(int $id): void
    {
        try {
            $spot = CorporateSpot::findOrFail($id);
            $spotTitle = $spot->title;

            // MediaLibrary dosyalarını da sil
            $spot->clearMediaCollection('audio');
            $spot->delete();

            log_activity($spot, 'silindi');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => "\"$spotTitle\" silindi.",
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

    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => 'Lütfen en az bir spot seçin.',
                'type' => 'warning',
            ]);
            return;
        }

        try {
            $spots = CorporateSpot::whereIn('id', $this->selectedIds)->get();
            $count = $spots->count();

            foreach ($spots as $spot) {
                $spot->clearMediaCollection('audio');
                $spot->delete();
            }

            $this->selectedIds = [];
            $this->selectAll = false;

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => "$count spot silindi.",
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

    #[Computed]
    public function corporateAccounts(): \Illuminate\Database\Eloquent\Collection
    {
        return MuzibuCorporateAccount::whereNull('parent_id')
            ->orderBy('company_name')
            ->get();
    }

    #[Computed]
    public function spots()
    {
        $query = CorporateSpot::query()
            ->with(['corporateAccount', 'media']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        // Corporate filter
        if ($this->corporateFilter) {
            $query->where('corporate_account_id', $this->corporateFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Sıralama güncelleme (drag & drop)
     */
    public function updateOrder(array $list): void
    {
        try {
            foreach ($list as $item) {
                CorporateSpot::where('id', $item['id'])
                    ->update(['position' => $item['order']]);
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Sıralama güncellendi.',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => 'Sıralama güncellenemedi.',
                'type' => 'error',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('muzibu::admin.livewire.spot-component', [
            'spots' => $this->spots,
            'corporateAccounts' => $this->corporateAccounts,
        ]);
    }
}
