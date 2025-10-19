<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Search\App\Services\SearchAnalyticsService;

#[Layout('admin.layout')]
class SearchAnalyticsComponent extends Component
{
    public $days = 30;
    public $stats = [];
    public $popularSearches = [];
    public $zeroResultSearches = [];
    public $performanceMetrics = [];

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function updatedDays()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $service = app(SearchAnalyticsService::class);

        $this->stats = $service->getDashboardStats($this->days);
        $this->popularSearches = $service->getPopularSearches($this->days, 10);
        $this->zeroResultSearches = $service->getZeroResultSearches($this->days, 10);
        $this->performanceMetrics = $service->getPerformanceMetrics($this->days);
    }

    public function exportData()
    {
        $service = app(SearchAnalyticsService::class);
        $csv = $service->exportToCsv($this->days);

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'search-analytics-' . now()->format('Y-m-d') . '.csv');
    }

    public function render()
    {
        return view('search::admin.livewire.analytics');
    }
}
