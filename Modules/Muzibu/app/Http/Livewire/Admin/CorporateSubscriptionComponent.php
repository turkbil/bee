<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;

class CorporateSubscriptionComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'status'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function subscriptions()
    {
        return MuzibuCorporateAccount::with(['user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->status === 'cancelled', fn($q) => $q->where('is_active', false))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_accounts' => MuzibuCorporateAccount::count(),
            'active_accounts' => MuzibuCorporateAccount::where('is_active', true)->count(),
            'inactive_accounts' => MuzibuCorporateAccount::where('is_active', false)->count(),
        ];
    }

    public function render()
    {
        return view('muzibu::admin.livewire.corporate-subscription-component');
    }
}
