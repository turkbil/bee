<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\MenuManagement\App\Models\MenuItem;

class FixMenuModuleNames extends Command
{
    protected $signature = 'menu:fix-module-names';
    protected $description = 'Fix menu item module names to be properly capitalized';

    public function handle()
    {
        $this->info('Fixing menu item module names...');
        
        $menuItems = MenuItem::where('url_type', 'module')->get();
        $updated = 0;
        
        foreach ($menuItems as $menuItem) {
            $urlData = $menuItem->url_data;
            
            if (isset($urlData['module'])) {
                $oldModule = $urlData['module'];
                $newModule = ucfirst(strtolower($oldModule));
                
                if ($oldModule !== $newModule) {
                    $urlData['module'] = $newModule;
                    $menuItem->url_data = $urlData;
                    $menuItem->save();
                    
                    $this->info("Updated menu item #{$menuItem->menu_item_id}: {$oldModule} -> {$newModule}");
                    $updated++;
                }
            }
        }
        
        $this->info("Fixed {$updated} menu items");
        
        // Clear cache
        cache()->flush();
        
        return 0;
    }
}