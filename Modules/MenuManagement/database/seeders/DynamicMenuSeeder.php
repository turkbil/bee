<?php

declare(strict_types=1);

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Portfolio\App\Models\Portfolio;
use App\Helpers\TenantHelpers;

class DynamicMenuSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = tenant()?->id ?? 1;
        $this->command->info("DynamicMenuSeeder başlatılıyor - Tenant ID: {$tenantId}");
        
        // Mevcut ana menüyü kontrol et
        $menu = Menu::where('slug', 'ana-menu')->first();
        
        if (!$menu) {
            // Ana Menü oluştur
            $menu = Menu::create([
                'name' => ['tr' => 'Ana Menü', 'en' => 'Main Menu', 'ar' => 'القائمة الرئيسية'],
                'slug' => 'ana-menu',
                'location' => 'header',
                'is_active' => true,
                'is_default' => true,
            ]);
            $this->command->info("✅ Ana menu oluşturuldu - ID: {$menu->menu_id}");
        } else {
            $this->command->info("ℹ️ Ana menu zaten var - ID: {$menu->menu_id}");
            // Mevcut menu item'ları temizle (opsiyonel)
            // MenuItem::where('menu_id', $menu->menu_id)->delete();
        }

        // 1. Sayfalar
        $pagesItem = MenuItem::firstOrCreate([
            'menu_id' => $menu->menu_id,
            'url_type' => 'module',
            'url_data' => ['module' => 'Page', 'type' => 'list', '_locale' => 'tr'],
        ], [
            'title' => ['tr' => 'Sayfalar', 'en' => 'Pages', 'ar' => 'الصفحات'],
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // 2. Portfolyo (Parent)
        $portfolioParent = MenuItem::firstOrCreate([
            'menu_id' => $menu->menu_id,
            'url_type' => 'module',
            'url_data' => ['module' => 'Portfolio', 'type' => 'list', '_locale' => 'tr'],
            'parent_id' => null,
        ], [
            'title' => ['tr' => 'Portfolyo', 'en' => 'Portfolio', 'ar' => 'محفظة'],
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // Portfolio Kategorilerini dinamik olarak ekle
        $categories = PortfolioCategory::where('is_active', true)
            ->orderBy('order')
            ->get();

        foreach ($categories as $index => $category) {
            // Her kategori için menu item oluştur
            $categoryItem = MenuItem::firstOrCreate([
                'menu_id' => $menu->menu_id,
                'parent_id' => $portfolioParent->item_id,
                'url_type' => 'module',
                'url_data' => [
                    'module' => 'Portfolio',
                    'type' => 'category',
                    'id' => $category->portfolio_category_id,
                    'slug' => $category->getTranslated('slug', 'tr'),
                    '_locale' => 'tr'
                ],
            ], [
                'title' => $category->title, // JSON field olduğu için direkt kullanılabilir
                'target' => '_self',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);

            // Her kategoriye ait ilk 3 portfolio'yu alt menü olarak ekle (tenant'a göre)
            if ($tenantId % 2 == 0) { // Çift ID'li tenant'lar için
                $portfolios = Portfolio::where('portfolio_category_id', $category->portfolio_category_id)
                    ->where('is_active', true)
                    ->limit(3)
                    ->get();

                foreach ($portfolios as $pIndex => $portfolio) {
                    MenuItem::firstOrCreate([
                        'menu_id' => $menu->menu_id,
                        'parent_id' => $categoryItem->item_id,
                        'url_type' => 'module',
                        'url_data' => [
                            'module' => 'Portfolio',
                            'type' => 'detail',
                            'id' => $portfolio->portfolio_id,
                            'slug' => $portfolio->getTranslated('slug', 'tr'),
                            '_locale' => 'tr'
                        ],
                    ], [
                        'title' => $portfolio->title,
                        'target' => '_self',
                        'sort_order' => $pIndex + 1,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 3. Duyurular
        $announcementItem = MenuItem::firstOrCreate([
            'menu_id' => $menu->menu_id,
            'url_type' => 'module',
            'url_data' => ['module' => 'Announcement', 'type' => 'list', '_locale' => 'tr'],
        ], [
            'title' => ['tr' => 'Duyurular', 'en' => 'Announcements', 'ar' => 'الإعلانات'],
            'target' => '_self',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // 4. İletişim (Tenant'a göre farklı)
        if ($tenantId <= 2) { // İlk 2 tenant için
            MenuItem::firstOrCreate([
                'menu_id' => $menu->menu_id,
                'url_type' => 'internal',
                'url_data' => ['url' => '/iletisim'],
            ], [
                'title' => ['tr' => 'İletişim', 'en' => 'Contact', 'ar' => 'اتصل بنا'],
                'target' => '_self',
                'sort_order' => 4,
                'is_active' => true,
            ]);
        }

        // 5. Hakkımızda (Tek ID'li tenant'lar için)
        if ($tenantId % 2 == 1) {
            MenuItem::firstOrCreate([
                'menu_id' => $menu->menu_id,
                'url_type' => 'internal',
                'url_data' => ['url' => '/hakkimizda'],
            ], [
                'title' => ['tr' => 'Hakkımızda', 'en' => 'About Us', 'ar' => 'معلومات عنا'],
                'target' => '_self',
                'sort_order' => 5,
                'is_active' => true,
            ]);
        }

        // Özet bilgi
        $totalItems = MenuItem::where('menu_id', $menu->menu_id)->count();
        $this->command->info("✅ Menu seeder tamamlandı - Toplam {$totalItems} menu item");
        $this->command->info("🏢 Tenant #{$tenantId} için özelleştirilmiş menu oluşturuldu");
    }
}