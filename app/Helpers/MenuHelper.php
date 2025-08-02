<?php

declare(strict_types=1);

use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

if (!function_exists('getMenuById')) {
    /**
     * Get menu by ID with its active root items and children
     *
     * @param int $menuId
     * @param string|null $locale
     * @return array|null
     */
    function getMenuById(int $menuId, ?string $locale = null): ?array
    {
        try {
            $locale = $locale ?? app()->getLocale();
            $cacheKey = "menu.id.{$menuId}.{$locale}";
            
            // Cache devre dışı - direkt veri döndür
            // return Cache::remember($cacheKey, now()->addHours(24), function() use ($menuId, $locale) {
            return (function() use ($menuId, $locale) {
                $menu = Menu::with(['rootItems' => function($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }, 'rootItems.activeChildren' => function($query) {
                    $query->orderBy('sort_order');
                }])
                ->where('is_active', true)
                ->find($menuId);

                if (!$menu) {
                    return null;
                }

                return [
                    'id' => $menu->menu_id,
                    'name' => $menu->getTranslated('name', $locale),
                    'location' => $menu->location,
                    'items' => $menu->rootItems->map(function(MenuItem $item) use ($locale) {
                        $hasActiveChild = $item->hasActiveChild();
                        
                        $menuItem = [
                            'id' => $item->item_id,
                            'title' => $item->getTranslated('title', $locale),
                            'url' => $item->getResolvedUrl($locale),
                            'target' => $item->target,
                            'icon' => $item->icon,
                            'is_active' => $item->isActive(),
                            'has_active_child' => $hasActiveChild,
                            'children' => []
                        ];
                        
                        // Alt menü öğelerini ekle
                        if ($item->activeChildren->count() > 0) {
                            $menuItem['children'] = $item->activeChildren->map(function(MenuItem $child) use ($locale) {
                                return [
                                    'id' => $child->item_id,
                                    'title' => $child->getTranslated('title', $locale),
                                    'url' => $child->getResolvedUrl($locale),
                                    'target' => $child->target,
                                    'icon' => $child->icon,
                                    'is_active' => $child->isActive(),
                                ];
                            })->toArray();
                        }
                        
                        return $menuItem;
                    })->toArray()
                ];
            })();
        } catch (\Exception $e) {
            \Log::error('MenuHelper getMenuById error: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getDefaultMenu')) {
    /**
     * Get default menu with its active root items and children
     *
     * @param string|null $locale
     * @return array|null
     */
    function getDefaultMenu(?string $locale = null): ?array
    {
        try {
            $locale = $locale ?? app()->getLocale();
            $cacheKey = "menu.default.{$locale}";
            
            // Cache devre dışı - direkt veri döndür
            // return Cache::remember($cacheKey, now()->addHours(24), function() use ($locale) {
            return (function() use ($locale) {
                $menu = Menu::with(['rootItems' => function($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }, 'rootItems.activeChildren' => function($query) {
                    $query->orderBy('sort_order');
                }])
                ->where('is_default', true)
                ->where('is_active', true)
                ->first();

                if (!$menu) {
                    // Fallback to first active menu
                    $menu = Menu::with(['rootItems' => function($query) {
                        $query->where('is_active', true)->orderBy('sort_order');
                    }, 'rootItems.activeChildren' => function($query) {
                        $query->orderBy('sort_order');
                    }])
                    ->where('is_active', true)
                    ->first();
                }

                if (!$menu) {
                    return null;
                }

                return [
                    'id' => $menu->menu_id,
                    'name' => $menu->getTranslated('name', $locale),
                    'location' => $menu->location,
                    'items' => $menu->rootItems->map(function(MenuItem $item) use ($locale) {
                        $hasActiveChild = $item->hasActiveChild();
                        
                        $menuItem = [
                            'id' => $item->item_id,
                            'title' => $item->getTranslated('title', $locale),
                            'url' => $item->getResolvedUrl($locale),
                            'target' => $item->target,
                            'icon' => $item->icon,
                            'is_active' => $item->isActive(),
                            'has_active_child' => $hasActiveChild,
                            'children' => []
                        ];
                        
                        // Alt menü öğelerini ekle
                        if ($item->activeChildren->count() > 0) {
                            $menuItem['children'] = $item->activeChildren->map(function(MenuItem $child) use ($locale) {
                                return [
                                    'id' => $child->item_id,
                                    'title' => $child->getTranslated('title', $locale),
                                    'url' => $child->getResolvedUrl($locale),
                                    'target' => $child->target,
                                    'icon' => $child->icon,
                                    'is_active' => $child->isActive(),
                                ];
                            })->toArray();
                        }
                        
                        return $menuItem;
                    })->toArray()
                ];
            })();
        } catch (\Exception $e) {
            \Log::error('MenuHelper getDefaultMenu error: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getMenuByLocation')) {
    /**
     * Get menu by location with its active root items and children
     *
     * @param string $location
     * @param string|null $locale
     * @return array|null
     */
    function getMenuByLocation(string $location, ?string $locale = null): ?array
    {
        try {
            $locale = $locale ?? app()->getLocale();
            $cacheKey = "menu.location.{$location}.{$locale}";
            
            // Cache devre dışı - direkt veri döndür
            // return Cache::remember($cacheKey, now()->addHours(24), function() use ($location, $locale) {
            return (function() use ($location, $locale) {
                $menu = Menu::with(['rootItems' => function($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }, 'rootItems.activeChildren' => function($query) {
                    $query->orderBy('sort_order');
                }])
                ->where('location', $location)
                ->where('is_active', true)
                ->first();

                if (!$menu) {
                    return null;
                }

                return [
                    'id' => $menu->menu_id,
                    'name' => $menu->getTranslated('name', $locale),
                    'location' => $menu->location,
                    'items' => $menu->rootItems->map(function(MenuItem $item) use ($locale) {
                        $hasActiveChild = $item->hasActiveChild();
                        
                        $menuItem = [
                            'id' => $item->item_id,
                            'title' => $item->getTranslated('title', $locale),
                            'url' => $item->getResolvedUrl($locale),
                            'target' => $item->target,
                            'icon' => $item->icon,
                            'is_active' => $item->isActive(),
                            'has_active_child' => $hasActiveChild,
                            'children' => []
                        ];
                        
                        // Alt menü öğelerini ekle
                        if ($item->activeChildren->count() > 0) {
                            $menuItem['children'] = $item->activeChildren->map(function(MenuItem $child) use ($locale) {
                                return [
                                    'id' => $child->item_id,
                                    'title' => $child->getTranslated('title', $locale),
                                    'url' => $child->getResolvedUrl($locale),
                                    'target' => $child->target,
                                    'icon' => $child->icon,
                                    'is_active' => $child->isActive(),
                                ];
                            })->toArray();
                        }
                        
                        return $menuItem;
                    })->toArray()
                ];
            })();
        } catch (\Exception $e) {
            \Log::error('MenuHelper getMenuByLocation error: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('clearMenuCaches')) {
    /**
     * Clear all menu-related caches
     * @param int|null $menuId Clear cache for specific menu ID
     * @param string|null $locale Clear cache for specific locale
     * @return void
     */
    function clearMenuCaches(?int $menuId = null, ?string $locale = null): void
    {
        try {
            if ($menuId && $locale) {
                // Clear specific menu and locale
                Cache::forget("menu.id.{$menuId}.{$locale}");
            } elseif ($menuId) {
                // Clear all locales for specific menu
                $locales = \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_active', true)->pluck('code');
                foreach ($locales as $loc) {
                    Cache::forget("menu.id.{$menuId}.{$loc}");
                }
            } elseif ($locale) {
                // Clear all menus for specific locale
                Cache::forget("menu.default.{$locale}");
                Cache::forget("menu.location.header.{$locale}");
                Cache::forget("menu.location.footer.{$locale}");
                Cache::forget("menu.location.sidebar.{$locale}");
            } else {
                // Clear all menu caches
                $locales = \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_active', true)->pluck('code');
                foreach ($locales as $loc) {
                    Cache::forget("menu.default.{$loc}");
                    Cache::forget("menu.location.header.{$loc}");
                    Cache::forget("menu.location.footer.{$loc}");
                    Cache::forget("menu.location.sidebar.{$loc}");
                    
                    // Clear all menu IDs (1-100 as reasonable range)
                    for ($i = 1; $i <= 100; $i++) {
                        Cache::forget("menu.id.{$i}.{$loc}");
                    }
                }
            }
            
            \Log::info('Menu caches cleared', [
                'menu_id' => $menuId,
                'locale' => $locale
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to clear menu caches: ' . $e->getMessage());
        }
    }
}

if (!function_exists('renderMenuItems')) {
    /**
     * Render menu items as HTML with dropdown support
     *
     * @param array $menuItems
     * @param string $cssClass
     * @return string
     */
    function renderMenuItems(array $menuItems, string $cssClass = 'px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300'): string
    {
        $html = '';
        
        foreach ($menuItems as $item) {
            $target = $item['target'] === '_blank' ? ' target="_blank"' : '';
            $activeClass = $item['is_active'] ? ' bg-gray-100 dark:bg-gray-700' : '';
            
            // Alt menü var mı kontrol et
            $hasChildren = !empty($item['children']);
            
            if ($hasChildren) {
                // Dropdown menü
                $html .= '<div class="relative dropdown-menu" x-data="{ open: false }">';
                $html .= '<button @click="open = !open" class="' . $cssClass . $activeClass . ' flex items-center">';
                
                if (!empty($item['icon'])) {
                    $html .= '<i class="' . htmlspecialchars($item['icon']) . ' mr-2"></i>';
                }
                
                $html .= htmlspecialchars($item['title']);
                $html .= '<svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>';
                $html .= '</button>';
                
                // Dropdown içeriği
                $html .= '<div x-show="open" @click.away="open = false" x-transition class="absolute left-0 top-full mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">';
                
                foreach ($item['children'] as $child) {
                    $childTarget = $child['target'] === '_blank' ? ' target="_blank"' : '';
                    $childActiveClass = $child['is_active'] ? ' bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '';
                    
                    $html .= '<a href="' . htmlspecialchars($child['url']) . '"' . $childTarget . ' class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' . $childActiveClass . '">';
                    
                    if (!empty($child['icon'])) {
                        $html .= '<i class="' . htmlspecialchars($child['icon']) . ' mr-2"></i>';
                    }
                    
                    $html .= htmlspecialchars($child['title']);
                    $html .= '</a>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
            } else {
                // Normal menü item
                $html .= '<a href="' . htmlspecialchars($item['url']) . '"' . $target . ' class="' . $cssClass . $activeClass . '">';
                
                if (!empty($item['icon'])) {
                    $html .= '<i class="' . htmlspecialchars($item['icon']) . ' mr-2"></i>';
                }
                
                $html .= htmlspecialchars($item['title']);
                $html .= '</a>';
            }
        }
        
        return $html;
    }
}