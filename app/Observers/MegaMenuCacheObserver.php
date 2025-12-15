<?php

namespace App\Observers;

use App\Services\MegaMenuCacheService;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer to invalidate megamenu cache when related models change
 *
 * Usage: Attach to ShopCategory and ShopProduct models
 */
class MegaMenuCacheObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->invalidateCache($model, 'created');
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        // Only invalidate if relevant fields changed
        $relevantFields = [
            'title', 'slug', 'is_active', 'show_in_menu',
            'parent_id', 'sort_order', 'icon_class', 'category_id'
        ];

        $changedFields = array_keys($model->getDirty());
        $hasRelevantChange = !empty(array_intersect($changedFields, $relevantFields));

        if ($hasRelevantChange) {
            $this->invalidateCache($model, 'updated');
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->invalidateCache($model, 'deleted');
    }

    /**
     * Invalidate the megamenu cache
     */
    protected function invalidateCache(Model $model, string $event): void
    {
        try {
            MegaMenuCacheService::invalidate();

            \Log::debug('MegaMenuCache: Invalidated', [
                'model' => get_class($model),
                'id' => $model->getKey(),
                'event' => $event,
            ]);
        } catch (\Exception $e) {
            \Log::warning('MegaMenuCache: Invalidation failed', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
