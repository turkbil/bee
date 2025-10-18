<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Frontend;

use Livewire\Component;
use Modules\Search\App\Services\UniversalSearchService;

class SearchBarComponent extends Component
{
    public $query = '';
    public $results = [];
    public $isOpen = false;
    public $selectedIndex = -1;

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) {
            $this->search();
            $this->isOpen = true;
            $this->selectedIndex = -1;
        } else {
            $this->results = [];
            $this->isOpen = false;
        }
    }

    public function search()
    {
        $searchService = app(UniversalSearchService::class);
        $searchResults = $searchService->searchAll($this->query, 10);
        $this->results = $searchService->formatResultsForDisplay(
            $searchResults,
            $this->query
        )->take(10);
    }

    public function selectResult($index)
    {
        if (isset($this->results[$index])) {
            $result = $this->results[$index];
            return redirect($result['url']);
        }
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
