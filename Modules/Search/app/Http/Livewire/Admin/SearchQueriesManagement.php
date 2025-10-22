<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Search\App\Models\SearchQuery;
use Modules\Search\App\Models\SearchClick;

#[Layout('admin.layout')]
class SearchQueriesManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all'; // all, popular, hidden, visible
    public $perPage = 50;
    public $editingQueryId = null;
    public $editingQueryText = '';

    protected $queryString = ['search', 'filter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function togglePopular($query)
    {
        // Get unique search query
        $searchQuery = SearchQuery::where('query', $query)->first();

        if ($searchQuery) {
            // Toggle for all instances of this query
            SearchQuery::where('query', $query)->update([
                'is_popular' => !$searchQuery->is_popular
            ]);

            $this->dispatch('success', message: $searchQuery->is_popular ? 'Popüler işareti kaldırıldı!' : 'Popüler olarak işaretlendi!');
        }
    }

    public function toggleHidden($query)
    {
        // Get unique search query
        $searchQuery = SearchQuery::where('query', $query)->first();

        if ($searchQuery) {
            // Toggle for all instances of this query
            SearchQuery::where('query', $query)->update([
                'is_hidden' => !$searchQuery->is_hidden
            ]);

            $this->dispatch('success', message: $searchQuery->is_hidden ? 'Sitede gösterilecek!' : 'Siteden gizlendi!');
        }
    }

    public function startEdit($query)
    {
        $this->editingQueryId = $query;
        $this->editingQueryText = $query;
    }

    public function cancelEdit()
    {
        $this->editingQueryId = null;
        $this->editingQueryText = '';
    }

    public function saveEdit()
    {
        if (empty($this->editingQueryText)) {
            $this->dispatch('error', message: 'Arama metni boş olamaz!');
            return;
        }

        // Update all instances of this query
        SearchQuery::where('query', $this->editingQueryId)->update([
            'query' => $this->editingQueryText
        ]);

        $this->dispatch('success', message: 'Arama sorgusu güncellendi!');
        $this->cancelEdit();
    }

    public function deleteQuery($query)
    {
        // Delete all instances of this query
        SearchQuery::where('query', $query)->delete();

        $this->dispatch('success', message: 'Arama sorgusu silindi!');
    }

    public function render()
    {
        $queries = SearchQuery::query()
            ->when($this->search, function ($query) {
                $query->where('query', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter === 'popular', fn($q) => $q->where('is_popular', true))
            ->when($this->filter === 'hidden', fn($q) => $q->where('is_hidden', true))
            ->when($this->filter === 'visible', fn($q) => $q->where('is_hidden', false))
            ->selectRaw('
                query,
                MAX(is_popular) as is_popular,
                MAX(is_hidden) as is_hidden,
                COUNT(*) as search_count,
                SUM(results_count) as total_results,
                MAX(created_at) as last_searched
            ')
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->paginate($this->perPage);

        // Get most clicked queries
        $mostClickedQueries = SearchClick::query()
            ->join('search_queries', 'search_clicks.search_query_id', '=', 'search_queries.id')
            ->selectRaw('
                search_queries.query,
                COUNT(*) as click_count
            ')
            ->groupBy('search_queries.query')
            ->orderByDesc('click_count')
            ->limit(10)
            ->get();

        return view('search::admin.livewire.search-queries-management', [
            'queries' => $queries,
            'mostClickedQueries' => $mostClickedQueries,
            'totalQueries' => SearchQuery::selectRaw('COUNT(DISTINCT query) as count')->value('count'),
            'totalPopular' => SearchQuery::where('is_popular', true)->selectRaw('COUNT(DISTINCT query) as count')->value('count'),
            'totalHidden' => SearchQuery::where('is_hidden', true)->selectRaw('COUNT(DISTINCT query) as count')->value('count'),
        ]);
    }
}
