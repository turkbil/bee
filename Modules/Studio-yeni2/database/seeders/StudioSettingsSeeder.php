<?php

namespace Modules\Studio\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Studio\App\Models\StudioSetting;

class StudioSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Örnek tema ayarları
        $this->seedThemeSettings();
        
        // Page modülü için örnek ayarlar
        $this->seedPageSettings();
    }
    
    /**
     * Tema ayarlarını ekle
     */
    protected function seedThemeSettings(): void
    {
        // ThemeManagement modülü yüklüyse
        if (class_exists('Modules\ThemeManagement\App\Models\Theme')) {
            // Kiracı (tenant) veritabanında çalışıyoruz, ancak themes tablosu merkezi veritabanında
            // Bu nedenle doğrudan tema oluşturmak yerine, merkezi veritabanındaki temaları kullanacağız
            
            // Kiracı veritabanında çalışıyoruz mu kontrol et
            $isTenant = config('database.default') === config('tenancy.database.tenant_connection');
            
            if ($isTenant) {
                // Kiracı veritabanında çalışıyoruz, merkezi veritabanındaki temaları kullanacağız
                // Tema oluşturmaya çalışmayacağız, sadece varsayılan tema adlarını kullanacağız
                return;
            } else {
                // Merkezi veritabanında çalışıyoruz, temaları oluşturabiliriz
                // Themes tablosu var mı kontrol et
                if (!Schema::hasTable('themes')) {
                    return;
                }
                
                // Varsayılan temayı kontrol et veya oluştur
                $defaultTheme = \Modules\ThemeManagement\App\Models\Theme::where('name', 'default')->first();
                
                if (!$defaultTheme) {
                    $defaultTheme = \Modules\ThemeManagement\App\Models\Theme::create([
                        'name' => 'default',
                        'title' => 'Varsayılan Tema',
                        'description' => 'Varsayılan tema',
                        'folder_name' => 'default',
                        'is_default' => true,
                        'is_active' => true,
                        'settings' => json_encode([
                            'theme_mode' => 'light',
                            'color_scheme' => 'primary',
                            'default_header' => 'themes.default.headers.standard',
                            'default_footer' => 'themes.default.footers.standard'
                        ])
                    ]);
                }
                
                // Bootstrap temasını kontrol et veya oluştur
                $bootstrapTheme = \Modules\ThemeManagement\App\Models\Theme::where('name', 'bootstrap')->first();
                
                if (!$bootstrapTheme) {
                    $bootstrapTheme = \Modules\ThemeManagement\App\Models\Theme::create([
                        'name' => 'bootstrap',
                        'title' => 'Bootstrap Tema',
                        'description' => 'Bootstrap 5 teması',
                        'folder_name' => 'bootstrap',
                        'is_default' => false,
                        'is_active' => true,
                        'settings' => json_encode([
                            'theme_mode' => 'light',
                            'color_scheme' => 'primary',
                            'default_header' => 'themes.bootstrap.headers.standard',
                            'default_footer' => 'themes.bootstrap.footers.standard'
                        ])
                    ]);
                }
            }
        }
    }
    
    /**
     * Page modülü için örnek ayarlar
     */
    protected function seedPageSettings(): void
    {
        // Page modülü yüklüyse
        if (class_exists('Modules\Page\App\Models\Page')) {
            // Sayfaları al
            $pages = \Modules\Page\App\Models\Page::all();
            
            foreach ($pages as $page) {
                // Sayfa ID'si alınırken page_id kullanılacak (model tanımına göre)
                $pageId = $page->page_id ?? null;
                
                // Geçerli bir page_id varsa kayıt oluştur
                if ($pageId) {
                    // Her sayfa için varsayılan studio ayarlarını oluştur
                    StudioSetting::updateOrCreate([
                        'module' => 'page',
                        'module_id' => $pageId
                    ], [
                        'theme' => 'default',
                        'header_template' => 'themes.default.headers.standard',
                        'footer_template' => 'themes.default.footers.standard',
                        'settings' => [
                            'show_title' => true,
                            'show_breadcrumbs' => true,
                            'sidebar_position' => 'right',
                            'content_width' => 'container'
                        ]
                    ]);
                }
            }
        }
    }
}