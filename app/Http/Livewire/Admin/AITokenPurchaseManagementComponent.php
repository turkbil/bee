<?php

namespace App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AITokenPurchase;

#[Layout('admin.layout')]
class AITokenPurchaseManagementComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 25;

    protected $queryString = ['search', 'status', 'dateFrom', 'dateTo', 'sortField', 'sortDirection', 'perPage'];

    public function render()
    {
        $purchases = AITokenPurchase::with(['tenant', 'package', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('tenant', function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('package', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $totalStats = [
            'total_purchases' => AITokenPurchase::count(),
            'total_revenue' => AITokenPurchase::where('status', 'completed')->sum('amount'),
            'total_tokens' => AITokenPurchase::where('status', 'completed')->sum('token_amount'),
            'today_revenue' => AITokenPurchase::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount')
        ];

        return view('livewire.admin.ai-token-purchase-management-component', compact('purchases', 'totalStats'));
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
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }
}