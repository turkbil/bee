<?php

namespace App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AITokenUsage;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class AITokenUsageManagementComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $model = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 25;

    protected $queryString = ['search', 'model', 'dateFrom', 'dateTo', 'sortField', 'sortDirection', 'perPage'];

    public function render()
    {
        $usages = AITokenUsage::with(['tenant', 'user'])
            ->when($this->search, function ($query) {
                $query->whereHas('tenant', function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('purpose', 'like', '%' . $this->search . '%');
            })
            ->when($this->model, function ($query) {
                $query->where('model', $this->model);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Aggregate statistics
        $stats = [
            'total_usage' => AITokenUsage::sum('tokens_used'),
            'today_usage' => AITokenUsage::whereDate('created_at', today())->sum('tokens_used'),
            'monthly_usage' => AITokenUsage::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('tokens_used'),
            'unique_users' => AITokenUsage::distinct('user_id')->count('user_id'),
            'unique_tenants' => AITokenUsage::distinct('tenant_id')->count('tenant_id')
        ];

        // Model usage stats
        $modelStats = AITokenUsage::select('model', DB::raw('COUNT(*) as count'), DB::raw('SUM(tokens_used) as total'))
            ->groupBy('model')
            ->orderBy('total', 'desc')
            ->get();

        // Available models for filter
        $availableModels = AITokenUsage::distinct('model')->pluck('model');

        return view('livewire.admin.ai-token-usage-management-component', compact('usages', 'stats', 'modelStats', 'availableModels'));
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
        $this->reset(['search', 'model', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }
}