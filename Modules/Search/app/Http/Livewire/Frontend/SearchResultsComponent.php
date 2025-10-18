<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Frontend;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Search\App\Services\UniversalSearchService;
use Modules\Search\App\Services\SearchClickTracker;

class SearchResultsComponent extends Component
{
    use WithPagination;

    public $query = '';
    public $activeTab = 'all';
    public $filters = [];
    public $results = [];
    public $totalCount = 0;
    public $responseTime = 0;

    protected $queryString = ['query' => ['as' => 'q'], 'activeTab'];

    public function mount(?string $query = null)
    {
        $this->query = $query ?? '';
        if ($this->query) {
            $this->performSearch();
        }
    }

    public function updatedQuery()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function performSearch()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->totalCount = 0;
            return;
        }

        $searchService = app(UniversalSearchService::class);
        $searchResults = $searchService->searchAll(
            $this->query,
            20,
            $this->filters,
            $this->activeTab
        );

        $this->results = $searchService->formatResultsForDisplay(
            $searchResults,
            $this->query
        );
        $this->totalCount = $searchResults['total_count'];
        $this->responseTime = $searchResults['response_time'];
    }

    public function trackClick($resultId, $resultType, $position)
    {
        $tracker = app(SearchClickTracker::class);
        $tracker->trackClick($this->query, $resultId, $resultType, $position, false);
    }

    public function render()
    {
        return view('search::livewire.frontend.search-results');
    }
}
