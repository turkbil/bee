<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Search\App\Models\SearchQuery;
use Carbon\Carbon;

#[Layout('admin.layout')]
class RecentSearchesComponent extends Component
{
    use WithPagination;

    public $perPage = 50;
    public $dateFilter = 'all'; // today, week, month, all
    public $typeFilter = 'all'; // all, products, categories, brands
    public $showZeroResults = false;
    public $searchTerm = '';
    public $ipFilter = '';

    protected $queryString = [
        'dateFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'showZeroResults' => ['except' => false],
        'searchTerm' => ['except' => ''],
        'ipFilter' => ['except' => ''],
    ];

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingShowZeroResults()
    {
        $this->resetPage();
    }

    public function updatingIpFilter()
    {
        $this->resetPage();
    }

    public function filterByIp($ip)
    {
        $this->ipFilter = $ip;
        $this->resetPage();
    }

    public function render()
    {
        $query = SearchQuery::query()
            ->with(['user'])
            ->orderByDesc('created_at');

        // Date filter
        $query->when($this->dateFilter !== 'all', function ($q) {
            match ($this->dateFilter) {
                'today' => $q->whereDate('created_at', Carbon::today()),
                'week' => $q->where('created_at', '>=', Carbon::now()->subWeek()),
                'month' => $q->where('created_at', '>=', Carbon::now()->subMonth()),
                default => $q,
            };
        });

        // Type filter
        if ($this->typeFilter !== 'all') {
            $query->where('searchable_type', $this->typeFilter);
        }

        // Zero results filter
        if ($this->showZeroResults) {
            $query->where('results_count', 0);
        }

        // Search term filter
        if (!empty($this->searchTerm)) {
            $query->where('query', 'LIKE', "%{$this->searchTerm}%");
        }

        // IP filter
        if (!empty($this->ipFilter)) {
            $query->where('ip_address', $this->ipFilter);
        }

        $searches = $query->paginate($this->perPage);

        // Statistics
        $stats = [
            'total_today' => SearchQuery::whereDate('created_at', Carbon::today())->count(),
            'total_week' => SearchQuery::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'total_month' => SearchQuery::where('created_at', '>=', Carbon::now()->subMonth())->count(),
            'zero_results_today' => SearchQuery::whereDate('created_at', Carbon::today())
                ->where('results_count', 0)->count(),
        ];

        return view('search::admin.livewire.recent-searches', [
            'searches' => $searches,
            'stats' => $stats,
        ]);
    }

    public function deleteSearch($searchId)
    {
        try {
            $search = SearchQuery::findOrFail($searchId);
            $search->delete();

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Arama kaydı silindi.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Arama kaydı silinemedi: ' . $e->getMessage(),
            ]);
        }
    }

    public function markAsPopular($searchId)
    {
        try {
            $search = SearchQuery::findOrFail($searchId);
            $search->update(['is_popular' => !$search->is_popular]);

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => $search->is_popular ? 'Popüler olarak işaretlendi.' : 'Popüler işareti kaldırıldı.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'İşlem başarısız: ' . $e->getMessage(),
            ]);
        }
    }

    public function hideSearch($searchId)
    {
        try {
            $search = SearchQuery::findOrFail($searchId);
            $search->update(['is_hidden' => !$search->is_hidden]);

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => $search->is_hidden ? 'Arama gizlendi.' : 'Arama görünür yapıldı.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'İşlem başarısız: ' . $e->getMessage(),
            ]);
        }
    }
}
