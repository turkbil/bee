<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Search\App\Models\SearchQuery;
use Modules\Search\App\Models\SearchClick;

#[Layout('admin.layout')]
class SearchQueriesManagementComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 50;

    #[Url]
    public $sortField = 'search_count';

    #[Url]
    public $sortDirection = 'desc';

    public $editingQueryId = null;
    public $editingQueryText = '';
    public $newQuery = '';

    public function updatedPerPage()
    {
        $this->perPage = (int) $this->perPage;
        $this->resetPage();
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
    }

    public function togglePopular($query)
    {
        $searchQuery = SearchQuery::where('query', $query)->first();

        if ($searchQuery) {
            $newStatus = !$searchQuery->is_popular;
            SearchQuery::where('query', $query)->update([
                'is_popular' => $newStatus
            ]);

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $newStatus ? 'Popüler olarak işaretlendi!' : 'Popüler işareti kaldırıldı!',
                'type' => 'success',
            ]);
        }
    }

    public function toggleHidden($query)
    {
        $searchQuery = SearchQuery::where('query', $query)->first();

        if ($searchQuery) {
            $newStatus = !$searchQuery->is_hidden;
            SearchQuery::where('query', $query)->update([
                'is_hidden' => $newStatus
            ]);

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $newStatus ? 'Gizlendi!' : 'Gösterilecek!',
                'type' => 'success',
            ]);
        }
    }

    public function startEditingQuery($query)
    {
        $this->editingQueryId = $query;
        $this->editingQueryText = $query;
    }

    public function updateQueryInline()
    {
        if (empty($this->editingQueryText) || empty($this->editingQueryId)) {
            $this->editingQueryId = null;
            $this->editingQueryText = '';
            return;
        }

        SearchQuery::where('query', $this->editingQueryId)->update([
            'query' => $this->editingQueryText
        ]);

        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Arama sorgusu güncellendi!',
            'type' => 'success',
        ]);

        $this->editingQueryId = null;
        $this->editingQueryText = '';
    }

    public function deleteQuery($query)
    {
        SearchQuery::where('query', $query)->delete();

        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Arama sorgusu silindi!',
            'type' => 'success',
        ]);
    }

    public function addQuery()
    {
        if (empty($this->newQuery)) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Arama metni boş olamaz!',
                'type' => 'error',
            ]);
            return;
        }

        // Check if already exists
        $exists = SearchQuery::where('query', $this->newQuery)->exists();
        if ($exists) {
            $this->dispatch('toast', [
                'title' => 'Uyarı',
                'message' => 'Bu arama sorgusu zaten mevcut!',
                'type' => 'warning',
            ]);
            $this->newQuery = '';
            return;
        }

        // Create new search query
        SearchQuery::create([
            'query' => $this->newQuery,
            'results_count' => 0,
            'is_popular' => false,
            'is_hidden' => false,
            'session_id' => session()->getId(),
        ]);

        $this->dispatch('toast', [
            'title' => 'Başarılı',
            'message' => 'Yeni arama sorgusu eklendi!',
            'type' => 'success',
        ]);

        $this->newQuery = '';
    }

    public function render()
    {
        $queries = SearchQuery::query()
            ->when($this->search, function ($query) {
                $query->where('query', 'like', '%' . $this->search . '%');
            })
            ->selectRaw('
                query,
                MAX(is_popular) as is_popular,
                MAX(is_hidden) as is_hidden,
                COUNT(*) as search_count,
                (SELECT results_count FROM search_queries sq2
                 WHERE sq2.query = search_queries.query
                 ORDER BY created_at DESC LIMIT 1) as last_results_count,
                MAX(created_at) as last_searched
            ')
            ->groupBy('query')
            ->orderBy($this->sortField, $this->sortDirection)
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

        return view('search::admin.livewire.search-queries-management-component', [
            'queries' => $queries,
            'mostClickedQueries' => $mostClickedQueries,
        ]);
    }
}
