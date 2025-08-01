<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Observers;

use Modules\MenuManagement\App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuItemObserver
{
    /**
     * Handle the MenuItem "created" event.
     */
    public function created(MenuItem $menuItem): void
    {
        // Clear menu cache
        $this->clearMenuCache($menuItem);
        
        // Update sort order for siblings
        $this->updateSiblingSortOrder($menuItem);
        
        // Log activity
        activity()
            ->performedOn($menuItem)
            ->causedBy(auth()->user())
            ->withProperties([
                'menu_id' => $menuItem->menu_id,
                'title' => $menuItem->title,
                'url_type' => $menuItem->url_type,
            ])
            ->log('menu_item_created');
    }

    /**
     * Handle the MenuItem "updated" event.
     */
    public function updated(MenuItem $menuItem): void
    {
        // Clear menu cache
        $this->clearMenuCache($menuItem);
        
        // If parent changed, update depth levels
        if ($menuItem->isDirty('parent_id')) {
            $this->updateDepthLevels($menuItem);
        }
        
        // Log activity
        activity()
            ->performedOn($menuItem)
            ->causedBy(auth()->user())
            ->withProperties([
                'changes' => $menuItem->getDirty(),
                'original' => $menuItem->getOriginal(),
            ])
            ->log('menu_item_updated');
    }

    /**
     * Handle the MenuItem "deleted" event.
     */
    public function deleted(MenuItem $menuItem): void
    {
        // Clear menu cache
        $this->clearMenuCache($menuItem);
        
        // Delete all children
        $menuItem->children()->delete();
        
        // Log activity
        activity()
            ->performedOn($menuItem)
            ->causedBy(auth()->user())
            ->withProperties([
                'menu_id' => $menuItem->menu_id,
                'title' => $menuItem->title,
            ])
            ->log('menu_item_deleted');
    }

    /**
     * Handle the MenuItem "restored" event.
     */
    public function restored(MenuItem $menuItem): void
    {
        // Clear menu cache
        $this->clearMenuCache($menuItem);
        
        // Log activity
        activity()
            ->performedOn($menuItem)
            ->causedBy(auth()->user())
            ->log('menu_item_restored');
    }

    /**
     * Clear menu related cache
     */
    private function clearMenuCache(MenuItem $menuItem): void
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        
        // Clear specific menu cache
        Cache::tags([
            "menu_items",
            "tenant.{$tenantId}",
            "menu.{$menuItem->menu_id}"
        ])->flush();
        
        // Clear general menu cache
        Cache::tags(["menus", "tenant.{$tenantId}"])->flush();
    }

    /**
     * Update sibling sort order
     */
    private function updateSiblingSortOrder(MenuItem $menuItem): void
    {
        // Get all siblings (same parent)
        $siblings = MenuItem::where('menu_id', $menuItem->menu_id)
            ->where('parent_id', $menuItem->parent_id)
            ->where('item_id', '!=', $menuItem->item_id)
            ->orderBy('sort_order')
            ->get();
        
        // Reorder if necessary
        $order = 1;
        foreach ($siblings as $sibling) {
            if ($order == $menuItem->sort_order) {
                $order++;
            }
            
            if ($sibling->sort_order != $order) {
                $sibling->update(['sort_order' => $order]);
            }
            
            $order++;
        }
    }

    /**
     * Update depth levels for item and its children
     */
    private function updateDepthLevels(MenuItem $menuItem): void
    {
        $menuItem->updateDepthLevel();
    }
}