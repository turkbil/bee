<?php

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;
use Modules\SeoManagement\App\Models\SeoSetting;

/**
 * Menu Seeder for Central Database
 * Languages: tr, en, ar
 * Pattern: Same as PageSeederCentral
 */
class MenuSeederCentral extends Seeder
{
    public function run(): void
    {
        // Bu seeder sadece central context'te çalışmalı
        if (tenancy()->initialized) {
            $this->command->warn("⚠️  MenuSeederCentral sadece central database'de çalışır. Atlanıyor...");
            return;
        }

        $this->command->info('🍔 Creating CENTRAL menus (tr, en, ar)...');

        // Duplicate kontrolü
        $existingCount = Menu::count();
        if ($existingCount > 0) {
            $this->command->info("Menus already exist in CENTRAL database ({$existingCount} menus), skipping seeder...");
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
        $this->createFooterMenu();
        
        $this->command->info('✅ CENTRAL menus created successfully!');
    }
    
    private function createMainMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'tr' => 'Ana Menü',
                'en' => 'Main Menu',
                'ar' => 'القائمة الرئيسية'
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

        // Sayfalar
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Sayfalar',
                'en' => 'Pages',
                'ar' => 'الصفحات'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'page', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Portfolio
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Portfolio',
                'en' => 'Portfolio',
                'ar' => 'معرض الأعمال'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Duyurular
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Duyurular',
                'en' => 'Announcements',
                'ar' => 'الإعلانات'
            ],
            'url_type' => 'module',
            'url_data' => ['module' => 'announcement', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Hakkımızda
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Hakkımızda',
                'en' => 'About Us',
                'ar' => 'معلومات عنا'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/hakkimizda'],
            'target' => '_self',
            'sort_order' => 5,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // İletişim
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'İletişim',
                'en' => 'Contact',
                'ar' => 'اتصل بنا'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/iletisim'],
            'target' => '_self',
            'sort_order' => 6,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
        
        $this->command->info('✅ Main Menu created');
    }
    
    private function createFooterMenu(): void
    {
        $menu = Menu::create([
            'name' => [
                'tr' => 'Footer Menü',
                'en' => 'Footer Menu',
                'ar' => 'قائمة التذييل'
            ],
            'slug' => 'footer-menu',
            'location' => 'footer',
            'is_active' => true,
            'is_default' => false,
        ]);

        // Gizlilik Politikası
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Gizlilik Politikası',
                'en' => 'Privacy Policy',
                'ar' => 'سياسة الخصوصية'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/gizlilik-politikasi'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Kullanım Şartları
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Kullanım Şartları',
                'en' => 'Terms of Service',
                'ar' => 'شروط الخدمة'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/kullanim-sartlari'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);

        // Çerez Politikası
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => [
                'tr' => 'Çerez Politikası',
                'en' => 'Cookie Policy',
                'ar' => 'سياسة ملفات تعريف الارتباط'
            ],
            'url_type' => 'custom',
            'url_data' => ['url' => '/cerez-politikasi'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
            'depth_level' => 0,
            'visibility' => 'public'
        ]);
        
        $this->command->info('✅ Footer Menu created');
    }
}