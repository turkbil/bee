<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Observers;

use Modules\MenuManagement\App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    /**
     * Handle the Menu "created" event.
     */
    public function created(Menu $menu): void
    {
        $this->clearMenuCache();
        
        // Log activity
        activity()
            ->performedOn($menu)
            ->withProperties([
                'menu_id' => $menu->menu_id,
                'name' => $menu->getTranslated('name', app()->getLocale()),
                'location' => $menu->location,
            ])
            ->log('Menu created');
    }

    /**
     * Handle the Menu "updated" event.
     */
    public function updated(Menu $menu): void
    {
        $this->clearMenuCache();
        
        // Log activity
        activity()
            ->performedOn($menu)
            ->withProperties([
                'menu_id' => $menu->menu_id,
                'name' => $menu->getTranslated('name', app()->getLocale()),
                'changes' => $menu->getChanges(),
            ])
            ->log('Menu updated');
    }

    /**
     * Handle the Menu "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        $this->clearMenuCache();
        
        // Log activity
        activity()
            ->withProperties([
                'menu_id' => $menu->menu_id,
                'name' => $menu->getTranslated('name', app()->getLocale()),
            ])
            ->log('Menu deleted');
    }

    /**
     * Handle the Menu "restored" event.
     */
    public function restored(Menu $menu): void
    {
        $this->clearMenuCache();
        
        // Log activity
        activity()
            ->performedOn($menu)
            ->withProperties([
                'menu_id' => $menu->menu_id,
                'name' => $menu->getTranslated('name', app()->getLocale()),
            ])
            ->log('Menu restored');
    }

    /**
     * Handle the Menu "force deleted" event.
     */
    public function forceDeleted(Menu $menu): void
    {
        $this->clearMenuCache();
        
        // Log activity
        activity()
            ->withProperties([
                'menu_id' => $menu->menu_id,
                'name' => $menu->getTranslated('name', app()->getLocale()),
            ])
            ->log('Menu force deleted');
    }

    /**
     * Clear menu-related cache
     */
    private function clearMenuCache(): void
    {
        // Clear menu cache tags
        Cache::tags(['menus', 'menu_items'])->flush();
        
        // Clear specific cache keys
        Cache::forget('menu_structure');
        Cache::forget('active_menus');
        Cache::forget('default_menu');
    }
}