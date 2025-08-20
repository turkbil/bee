<?php

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

/**
 * Menu Seeder for Tenant4 Database
 * Languages: en
 */
class MenuSeederTenant4 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT4 menus (en)...');
        
        // Duplicate kontrolü
        $existingCount = Menu::count();
        if ($existingCount > 0) {
            $this->command->info("Menus already exist in TENANT4 database ({$existingCount} menus), skipping seeder...");
            return;
        }
        
        // Mevcut menüleri sil (foreign key sırası önemli)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (\Schema::hasTable('menu_items')) {
            \DB::table('menu_items')->truncate();
        }
        Menu::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->createMainMenu();
        $this->createQuickLinksMenu();
    }
    
    private function createMainMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'en' => 'Main Navigation - Tenant4'
            ],
            'slug' => 'ana-menu',
            'location' => 'header',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Home
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Home'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Business Solutions
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Business Solutions'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/business-solutions'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Automation Tools
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Automation Tools'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Case Studies
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Case Studies'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'page', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // About Us
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'About Us'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/about'],
            'target' => '_self',
            'sort_order' => 5,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Contact
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Get Started'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/get-started'],
            'target' => '_self',
            'sort_order' => 6,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
    }
    
    private function createQuickLinksMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'en' => 'Quick Links'
            ],
            'slug' => 'quick-links',
            'location' => 'footer',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Product Demo
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Product Demo'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/demo'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Pricing
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Pricing'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/pricing'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Support Center
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Support Center'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/support'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
        
        // Blog
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Blog'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'announcement', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
    }
}