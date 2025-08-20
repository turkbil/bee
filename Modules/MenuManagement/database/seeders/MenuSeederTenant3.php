<?php

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

/**
 * Menu Seeder for Tenant3 Database
 * Languages: en, ar
 */
class MenuSeederTenant3 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT3 menus (en, ar)...');
        
        // Duplicate kontrolü
        $existingCount = Menu::count();
        if ($existingCount > 0) {
            $this->command->info("Menus already exist in TENANT3 database ({$existingCount} menus), skipping seeder...");
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
        $this->createTechMenu();
    }
    
    private function createMainMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'en' => 'Main Menu - Tenant3',
                'ar' => 'القائمة الرئيسية - Tenant3'
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
                'en' => 'Home',
                'ar' => 'الصفحة الرئيسية'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Solutions
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Solutions',
                'ar' => 'الحلول'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/solutions'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Products
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Products',
                'ar' => 'المنتجات'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // AI Platform
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'AI Platform',
                'ar' => 'منصة الذكاء الاصطناعي'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/ai-platform'],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // News
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'News',
                'ar' => 'الأخبار'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'announcement', 'type' => 'index'],
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
                'en' => 'Contact',
                'ar' => 'اتصل بنا'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/contact'],
            'target' => '_self',
            'sort_order' => 6,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
    }
    
    private function createTechMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'en' => 'Technology Menu',
                'ar' => 'قائمة التكنولوجيا'
            ],
            'slug' => 'tech-menu',
            'location' => 'footer',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Documentation
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Documentation',
                'ar' => 'الوثائق'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/docs'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // API Reference
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'API Reference',
                'ar' => 'مرجع API'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/api-docs'],
            'target' => '_blank',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Support
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'en' => 'Support',
                'ar' => 'الدعم'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/support'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
    }
}