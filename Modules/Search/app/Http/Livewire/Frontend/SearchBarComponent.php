<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Frontend;

use Livewire\Component;
use Modules\Search\App\Services\UniversalSearchService;
use Modules\Search\App\Services\SearchClickTracker;

class SearchBarComponent extends Component
{
    public $query = '';
    public $isOpen = false;
    public $viewMode = 'default'; // default or footer

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->isOpen = true;
        } else {
            $this->isOpen = false;
        }
    }

    /**
     * Computed property - NOT serialized in state!
     * Results calculated on each render, never stored
     */
    public function getResultsProperty()
    {
        if (strlen($this->query) < 2) {
            return [];
        }

        $searchService = app(UniversalSearchService::class);
        $searchResults = $searchService->searchAll(
            query: $this->query,
            perPage: 10,
            page: 1,
            filters: [],
            activeTab: 'all',
            logQuery: false
        );

        return $searchService->formatResultsForDisplay(
            $searchResults,
            $this->query
        )->take(10)->values()->all();
    }

    public function closeDropdown()
    {
        $this->isOpen = false;
    }

    /**
     * Track click on search result
     */
    public function trackClick(int $resultId, string $resultType, int $position)
    {
        // Convert type string to model class name
        $typeMap = [
            'products' => \Modules\Shop\App\Models\ShopProduct::class,
            'categories' => \Modules\Shop\App\Models\ShopCategory::class,
            'brands' => \Modules\Shop\App\Models\ShopBrand::class,
        ];

        $modelType = $typeMap[$resultType] ?? $resultType;

        $tracker = app(SearchClickTracker::class);
        $tracker->trackClick(
            query: $this->query,
            resultId: $resultId,
            resultType: $modelType,
            position: $position,
            openedInNewTab: false
        );
    }

    public function render()
    {
        $viewName = $this->viewMode === 'footer'
            ? 'search::livewire.frontend.search-bar-footer'
            : 'search::livewire.frontend.search-bar';

        return view($viewName);
    }
}
