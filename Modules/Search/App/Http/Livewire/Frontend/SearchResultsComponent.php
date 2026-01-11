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
    public $perPage = 12;
    public $lastPage = 1;
    public $currentPage = 1;

    protected $queryString = ['query' => ['as' => 'q'], 'activeTab'];

    public function mount(?string $query = null)
    {
        $this->query = $query ?? '';
        if ($this->query) {
            $this->performSearch();
        }
    }

    public function updatingQuery()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function updatedQuery()
    {
        $this->performSearch();
    }

    public function updatedActiveTab()
    {
        $this->performSearch();
    }

    public function performSearch()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->totalCount = 0;
            $this->lastPage = 1;
            $this->currentPage = 1;
            $this->page = 1;
            return;
        }

        $searchService = app(UniversalSearchService::class);
        $page = max(1, (int) ($this->page ?? 1));

        $searchResults = $searchService->searchAll(
            query: $this->query,
            perPage: $this->perPage,
            page: $page,
            filters: $this->filters,
            activeTab: $this->activeTab
        );

        $formatted = $searchService->formatResultsForDisplay(
            $searchResults,
            $this->query
        );

        $this->results = $formatted->values();
        $this->totalCount = $searchResults['total_count'];
        $this->responseTime = $searchResults['response_time'];

        $pages = collect($searchResults['results'] ?? [])->map(function ($data) {
            return [
                'current' => $data['current_page'] ?? 1,
                'last' => $data['last_page'] ?? 1,
            ];
        });

        $this->lastPage = max(1, $pages->max('last') ?? 1);
        $current = max(1, $pages->max('current') ?? $page);
        if ($current !== $page) {
            $this->page = $current;
        }

        $this->currentPage = $this->page ?? $current;
    }

    public function trackClick($resultId, $resultType, $position)
    {
        $tracker = app(SearchClickTracker::class);
        $tracker->trackClick($this->query, $resultId, $resultType, $position, false);
    }

    public function goToPage(int $page)
    {
        $page = max(1, min($page, $this->lastPage));
        $this->page = $page;
        $this->performSearch();
    }

    public function goToPreviousPage()
    {
        if (($this->page ?? 1) > 1) {
            $this->goToPage(($this->page ?? 1) - 1);
        }
    }

    public function goToNextPage()
    {
        if (($this->page ?? 1) < $this->lastPage) {
            $this->goToPage(($this->page ?? 1) + 1);
        }
    }

    public function render()
    {
        return view('search::livewire.frontend.search-results');
    }
}
