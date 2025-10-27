<?php

declare(strict_types=1);

namespace Modules\Search\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Search\App\Models\SearchClick;
use Modules\Search\App\Models\SearchQuery;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;
use Carbon\Carbon;

#[Layout('admin.layout')]
class ClickAnalyticsComponent extends Component
{
    use WithPagination;

    public $perPage = 50;
    public $dateFilter = 'week'; // today, week, month, all
    public $typeFilter = 'all'; // all, products, categories, brands
    public $groupBy = 'item'; // item, query, position

    protected $queryString = [
        'dateFilter' => ['except' => 'week'],
        'typeFilter' => ['except' => 'all'],
        'groupBy' => ['except' => 'item'],
    ];

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingGroupBy()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Base query
        $query = SearchClick::query()->with(['searchQuery']);

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
            $modelMap = [
                'products' => ShopProduct::class,
                'categories' => ShopCategory::class,
                'brands' => ShopBrand::class,
            ];
            if (isset($modelMap[$this->typeFilter])) {
                $query->where('clicked_result_type', $modelMap[$this->typeFilter]);
            }
        }

        // Group by logic
        $results = match ($this->groupBy) {
            'item' => $this->getGroupedByItem($query),
            'query' => $this->getGroupedByQuery($query),
            'position' => $this->getGroupedByPosition($query),
            default => $this->getGroupedByItem($query),
        };

        // Statistics
        $stats = $this->getStatistics();

        return view('search::admin.livewire.click-analytics', [
            'results' => $results,
            'stats' => $stats,
        ]);
    }

    protected function getGroupedByItem($baseQuery)
    {
        $clicks = $baseQuery->get()->groupBy(function ($click) {
            return $click->clicked_result_type . ':' . $click->clicked_result_id;
        })->map(function ($group) {
            $first = $group->first();
            $item = $this->resolveItem($first->clicked_result_type, $first->clicked_result_id);

            return [
                'type' => $first->clicked_result_type,
                'id' => $first->clicked_result_id,
                'title' => $item['title'] ?? 'Bilinmeyen',
                'url' => $item['url'] ?? '#',
                'click_count' => $group->count(),
                'unique_queries' => $group->pluck('searchQuery.query')->unique()->count(),
                'avg_position' => round($group->avg('click_position'), 1),
                'new_tab_count' => $group->where('opened_in_new_tab', true)->count(),
                'latest_click' => $group->max('created_at'),
            ];
        })->sortByDesc('click_count')->values();

        return $this->paginateCollection($clicks);
    }

    protected function getGroupedByQuery($baseQuery)
    {
        $clicks = $baseQuery->with('searchQuery')->get()->groupBy('searchQuery.query')->map(function ($group) {
            $query = $group->first()->searchQuery->query ?? 'Bilinmeyen';

            return [
                'query' => $query,
                'click_count' => $group->count(),
                'unique_items' => $group->groupBy(function ($click) {
                    return $click->clicked_result_type . ':' . $click->clicked_result_id;
                })->count(),
                'avg_position' => round($group->avg('click_position'), 1),
                'new_tab_count' => $group->where('opened_in_new_tab', true)->count(),
                'latest_click' => $group->max('created_at'),
            ];
        })->sortByDesc('click_count')->values();

        return $this->paginateCollection($clicks);
    }

    protected function getGroupedByPosition($baseQuery)
    {
        $clicks = $baseQuery->get()->groupBy('click_position')->map(function ($group, $position) {
            return [
                'position' => $position,
                'click_count' => $group->count(),
                'unique_items' => $group->groupBy(function ($click) {
                    return $click->clicked_result_type . ':' . $click->clicked_result_id;
                })->count(),
                'unique_queries' => $group->pluck('searchQuery.query')->unique()->count(),
                'new_tab_count' => $group->where('opened_in_new_tab', true)->count(),
            ];
        })->sortBy('position')->values();

        return $this->paginateCollection($clicks);
    }

    protected function resolveItem(string $type, int $id): array
    {
        $model = match ($type) {
            ShopProduct::class => ShopProduct::find($id),
            ShopCategory::class => ShopCategory::find($id),
            ShopBrand::class => ShopBrand::find($id),
            default => null,
        };

        if (!$model) {
            return ['title' => 'Silinmiş İçerik', 'url' => '#'];
        }

        $title = $model->title ?? $model->name ?? 'Başlıksız';
        if (is_array($title)) {
            $title = $title[app()->getLocale()] ?? $title['tr'] ?? reset($title);
        }

        $slug = $model->slug ?? null;
        if (is_array($slug)) {
            $slug = $slug[app()->getLocale()] ?? $slug['tr'] ?? reset($slug);
        }

        $url = match ($type) {
            ShopProduct::class => $slug ? route('shop.show', $slug) : '#',
            ShopCategory::class => $slug ? route('shop.category', $slug) : '#',
            ShopBrand::class => $slug ? route('shop.brand', $slug) : '#',
            default => '#',
        };

        return [
            'title' => $title,
            'url' => $url,
        ];
    }

    protected function getStatistics(): array
    {
        $dateQuery = SearchClick::query();

        if ($this->dateFilter !== 'all') {
            $dateQuery->when($this->dateFilter !== 'all', function ($q) {
                match ($this->dateFilter) {
                    'today' => $q->whereDate('created_at', Carbon::today()),
                    'week' => $q->where('created_at', '>=', Carbon::now()->subWeek()),
                    'month' => $q->where('created_at', '>=', Carbon::now()->subMonth()),
                    default => $q,
                };
            });
        }

        $totalClicks = $dateQuery->count();
        $uniqueItems = $dateQuery->select('clicked_result_type', 'clicked_result_id')
            ->distinct()
            ->get()
            ->count();

        $uniqueQueries = SearchQuery::query()
            ->when($this->dateFilter !== 'all', function ($q) {
                match ($this->dateFilter) {
                    'today' => $q->whereDate('created_at', Carbon::today()),
                    'week' => $q->where('created_at', '>=', Carbon::now()->subWeek()),
                    'month' => $q->where('created_at', '>=', Carbon::now()->subMonth()),
                    default => $q,
                };
            })
            ->has('clicks')
            ->distinct('query')
            ->count('query');

        $newTabPercentage = $totalClicks > 0
            ? round(($dateQuery->where('opened_in_new_tab', true)->count() / $totalClicks) * 100, 1)
            : 0;

        return [
            'total_clicks' => $totalClicks,
            'unique_items' => $uniqueItems,
            'unique_queries' => $uniqueQueries,
            'new_tab_percentage' => $newTabPercentage,
        ];
    }

    protected function paginateCollection($collection)
    {
        $currentPage = $this->paginators['page'] ?? 1;
        $perPage = $this->perPage;

        $items = $collection->forPage($currentPage, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
