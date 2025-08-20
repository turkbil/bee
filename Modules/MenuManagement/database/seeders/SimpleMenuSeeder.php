<?php

declare(strict_types=1);

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

class SimpleMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Mevcut ana menüyü kontrol et
        $existingMenu = Menu::where('slug', 'ana-menu')->first();
        
        if ($existingMenu) {
            $this->command->info('Ana menu zaten var, atlanıyor...');
            return;
        }
        
        // Ana Menü oluştur
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

        // 1. Pages (Basit Link)
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
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // 2. Portfolyo (Parent)
        $portfolioParent = MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'Portfolyo', 'en' => 'Portfolio'],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // Portfolio Alt Kategorileri - ID bazlı (database'den slug çekilecek)
        // Web Tasarım kategorisi (ID: 1)
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'parent_id' => $portfolioParent->item_id,
            'title' => ['tr' => 'Web Tasarım', 'en' => 'Web Design'],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'category', 'id' => 1],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Mobil Uygulama kategorisi (ID: 2)
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'parent_id' => $portfolioParent->item_id,
            'title' => ['tr' => 'Mobil Uygulama', 'en' => 'Mobile App'],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'category', 'id' => 2],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // E-Ticaret kategorisi (ID: 3)
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'parent_id' => $portfolioParent->item_id,
            'title' => ['tr' => 'E-Ticaret', 'en' => 'E-Commerce'],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'category', 'id' => 3],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // Kurumsal Web kategorisi (ID: 4)
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'parent_id' => $portfolioParent->item_id,
            'title' => ['tr' => 'Kurumsal Web', 'en' => 'Corporate Web'],
            'url_type' => 'module',
            'url_data' => ['module' => 'portfolio', 'type' => 'category', 'id' => 4],
            'target' => '_self',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // 3. Duyurular
        MenuItem::create([
            'menu_id' => $menu->menu_id,
            'title' => ['tr' => 'Duyurular', 'en' => 'Announcements'],
            'url_type' => 'module',
            'url_data' => ['module' => 'announcement', 'type' => 'index'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        echo "✅ Basit menu oluşturuldu - Ana Menü (ID: {$menu->menu_id})\n";
        echo "📋 Pages + Portfolio + Announcement menu items oluşturuldu\n";
    }
}