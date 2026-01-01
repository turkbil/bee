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

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($menu, 'oluşturuldu');
        }
    }

    /**
     * Handle the Menu "updated" event.
     */
    public function updated(Menu $menu): void
    {
        $this->clearMenuCache();

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $menu->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                // Eski başlığı al (name değiştiyse)
                $oldTitle = null;
                if (isset($changes['name'])) {
                    $oldTitle = $menu->getOriginal('name');
                }

                log_activity($menu, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }
    }

    /**
     * Handle the Menu "deleted" event.
     */
    public function deleted(Menu $menu): void
    {
        $this->clearMenuCache();

        // Activity log - silinen kaydın adını sakla
        if (function_exists('log_activity')) {
            log_activity($menu, 'silindi', null, $menu->name);
        }
    }

    /**
     * Handle the Menu "restored" event.
     */
    public function restored(Menu $menu): void
    {
        $this->clearMenuCache();

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($menu, 'geri yüklendi');
        }
    }

    /**
     * Handle the Menu "force deleted" event.
     */
    public function forceDeleted(Menu $menu): void
    {
        $this->clearMenuCache();

        // Activity log - kalıcı silme
        if (function_exists('log_activity')) {
            log_activity($menu, 'kalıcı silindi', null, $menu->name);
        }
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