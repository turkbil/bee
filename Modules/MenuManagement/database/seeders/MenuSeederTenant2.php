<?php

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

/**
 * Menu Seeder for Tenant2 Database
 * Languages: tr, en
 */
class MenuSeederTenant2 extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating TENANT2 menus (tr, en)...');
        
        // Duplicate kontrolü
        $existingCount = Menu::count();
        if ($existingCount > 0) {
            $this->command->info("Menus already exist in TENANT2 database ({$existingCount} menus), skipping seeder...");
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
        $this->createBusinessMenu();
    }
    
    private function createMainMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'tr' => 'Ana Menü - Tenant2',
                'en' => 'Main Menu - Tenant2'
            ],
            'slug' => 'ana-menu',
            'location' => 'header',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Ana Sayfa
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Ana Sayfa',
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

        // Hizmetlerimiz
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Hizmetlerimiz',
                'en' => 'Our Services'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/hizmetlerimiz'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Projelerimiz
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Projelerimiz',
                'en' => 'Our Projects'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'index'],
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
                'tr' => 'Blog',
                'en' => 'Blog'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'page', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // İletişim
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'İletişim',
                'en' => 'Contact'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/iletisim'],
            'target' => '_self',
            'sort_order' => 5,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
    }
    
    private function createBusinessMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'tr' => 'İş Menüsü',
                'en' => 'Business Menu'
            ],
            'slug' => 'business-menu',
            'location' => 'sidebar',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Kurumsal
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Kurumsal',
                'en' => 'Corporate'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/kurumsal'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Referanslar
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Referanslar',
                'en' => 'References'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/referanslar'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
    }
}