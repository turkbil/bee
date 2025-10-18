<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Frontend;

use Livewire\Component;
use Modules\Search\App\Services\UniversalSearchService;

class SearchBarComponent extends Component
{
    public $query = '';
    public $isOpen = false;

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
        $searchResults = $searchService->searchAll($this->query, 10);

        return $searchService->formatResultsForDisplay(
            $searchResults,
            $this->query
        )->take(10)->values()->all();
    }

    public function closeDropdown()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('search::livewire.frontend.search-bar');
    }
}
