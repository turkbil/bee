<?php

namespace Modules\MenuManagement\App\Console;

use Illuminate\Console\Command;
use Modules\MenuManagement\App\Models\MenuItem;
use Modules\MenuManagement\App\Services\MenuUrlBuilderService;
use Illuminate\Support\Facades\Log;

class MigrateMenuItemSlugs extends Command
{
    protected $signature = 'menu:migrate-slugs';
    protected $description = 'Migrate menu items from ID-based to slug-based URLs';

    protected MenuUrlBuilderService $menuUrlBuilder;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->menuUrlBuilder = app(MenuUrlBuilderService::class);
        
        $this->info('Starting menu item slug migration...');
        
        // Tüm module tipindeki menü itemlarını al
        $menuItems = MenuItem::where('url_type', 'module')->get();
        
        $updatedCount = 0;
        $skippedCount = 0;
        
        foreach ($menuItems as $menuItem) {
            $urlData = $menuItem->url_data;
            
            // Eğer zaten slug varsa skip et
            if (isset($urlData['slug'])) {
                $skippedCount++;
                continue;
            }
            
            // Category veya detail tipi için slug'ı bul
            if (isset($urlData['module']) && isset($urlData['type']) && isset($urlData['id'])) {
                $type = $urlData['type'];
                $moduleContents = $this->menuUrlBuilder->getModuleContent($urlData['module'], $type);
                
                // ID'ye göre içeriği bul
                $content = collect($moduleContents)->firstWhere('id', $urlData['id']);
                
                if ($content && isset($content['slug'])) {
                    // URL data'yı güncelle
                    $urlData['slug'] = $content['slug'];
                    $menuItem->url_data = $urlData;
                    $menuItem->save();
                    
                    $updatedCount++;
                    $this->info("Updated menu item #{$menuItem->menu_item_id} - Added slug: {$content['slug']}");
                } else {
                    $this->warn("Could not find slug for menu item #{$menuItem->menu_item_id} - Module: {$urlData['module']}, Type: {$type}, ID: {$urlData['id']}");
                }
            }
        }
        
        $this->info("Migration completed!");
        $this->info("Updated: {$updatedCount} items");
        $this->info("Skipped: {$skippedCount} items (already have slug)");
        
        // Cache'i temizle
        cache()->flush();
        
        return 0;
    }
}