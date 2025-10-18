<?php

declare(strict_types=1);

namespace Modules\Search\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SearchClick extends Model
{
    protected $fillable = [
        'search_query_id',
        'clicked_result_id',
        'clicked_result_type',
        'click_position',
        'opened_in_new_tab',
    ];

    protected $casts = [
        'click_position' => 'integer',
        'opened_in_new_tab' => 'boolean',
    ];

    /**
     * Get the search query for this click
     */
    public function searchQuery(): BelongsTo
    {
        return $this->belongsTo(SearchQuery::class);
    }

    /**
     * Get the clicked item (polymorphic)
     */
    public function clickedResult(): MorphTo
    {
        return $this->morphTo('clickedResult', 'clicked_result_type', 'clicked_result_id');
    }

    /**
     * Scope: Get clicks by position
     */
    public function scopeByPosition($query, int $position)
    {
        return $query->where('click_position', $position);
    }

    /**
     * Scope: Get new tab clicks
     */
    public function scopeNewTabClicks($query)
    {
        return $query->where('opened_in_new_tab', true);
    }

    /**
     * Get position analytics
     */
    public static function getPositionAnalytics(int $days = 30)
    {
        return static::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('click_position, COUNT(*) as click_count')
            ->groupBy('click_position')
            ->orderBy('click_position')
            ->get();
    }

    /**
     * Get most clicked items
     */
    public static function getMostClickedItems(string $type = null, int $limit = 10, int $days = 30)
    {
        $query = static::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('clicked_result_id, clicked_result_type, COUNT(*) as click_count')
            ->groupBy('clicked_result_id', 'clicked_result_type')
            ->orderByDesc('click_count')
            ->limit($limit);

        if ($type) {
            $query->where('clicked_result_type', $type);
        }

        return $query->get();
    }

    /**
     * Get new tab vs same tab statistics
     */
    public static function getNewTabStatistics(int $days = 30)
    {
        return static::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('opened_in_new_tab, COUNT(*) as count')
            ->groupBy('opened_in_new_tab')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->opened_in_new_tab ? 'new_tab' : 'same_tab' => $item->count];
            });
    }
}
