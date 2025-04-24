<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Modules\WidgetManagement\app\Models\Widget;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WidgetCategorySeeder extends Seeder
{
    // Çalıştırma izleme anahtarı
    private static $runKey = 'widget_category_seeder_executed';
    
    public function run(): void
    {
        // Tenant kontrolü
        if (function_exists('tenant') && tenant()) {
            if ($this->command) {
                $this->command->info('Tenant contextinde çalışıyor, WidgetCategorySeeder atlanıyor.');
            }
            Log::info('Tenant contextinde çalışıyor, WidgetCategorySeeder atlanıyor. Tenant ID: ' . tenant('id'));
            return;
        }
        
        // Seeder'ın daha önce çalıştırılıp çalıştırılmadığını kontrol et
        $cacheKey = self::$runKey . '_' . Config::get('database.default');
        if (Cache::has($cacheKey)) {
            Log::info('WidgetCategorySeeder zaten çalıştırılmış, atlanıyor...');
            return;
        }
        
        Log::info('WidgetCategorySeeder merkezi veritabanında çalışıyor...');
        
        // Tablo var mı kontrol et
        if (!Schema::hasTable('widget_categories')) {
            Log::warning('widget_categories tablosu bulunamadı, işlem atlanıyor...');
            if ($this->command) {
                $this->command->info('widget_categories tablosu bulunamadı, işlem atlanıyor...');
            }
            return;
        }

        try {
            // Önce duplicate kategorileri temizle
            $this->cleanupDuplicateCategories();

            // Ana kategorileri oluştur
            $modulesCategory = $this->createMainCategories();
            
            if (!$modulesCategory) {
                Log::error('Modül ana kategorisi bulunamadı, alt kategoriler oluşturulamıyor...');
                return;
            }
            
            // Alt kategorileri oluştur
            $this->createModuleSubcategories($modulesCategory);
            
            Log::info('Widget kategorileri başarıyla oluşturuldu.');
            
            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('WidgetCategorySeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($this->command) {
                $this->command->error('WidgetCategorySeeder hatası: ' . $e->getMessage());
            }
        }
    }
    
    private function cleanupDuplicateCategories(): void
    {
        try {
            // Önce tüm kategorileri kontrol et ve temizle
            Log::info("Kategori tablosu temizleniyor ve kontrol ediliyor...");
            
            // Aynı slug'a sahip kategorileri gruplandır
            $duplicates = DB::table('widget_categories')
                ->select('slug')
                ->groupBy('slug')
                ->havingRaw('COUNT(*) > 1')
                ->get();
            
            foreach ($duplicates as $duplicate) {
                $categories = WidgetCategory::where('slug', $duplicate->slug)
                    ->orderBy('widget_category_id')
                    ->get();
                
                // İlk kategoriyi koru, diğerlerini sil
                $keepCategory = $categories->shift();
                
                foreach ($categories as $category) {
                    // Bu kategoriye bağlı widget'ları kalan kategoriye taşı
                    Widget::where('widget_category_id', $category->widget_category_id)
                        ->update(['widget_category_id' => $keepCategory->widget_category_id]);
                    
                    // Alt kategorileri kalan kategoriye taşı
                    WidgetCategory::where('parent_id', $category->widget_category_id)
                        ->update(['parent_id' => $keepCategory->widget_category_id]);
                    
                    // Kategoriyi sil
                    $category->delete();
                    
                    Log::info("Yinelenen kategori silindi: {$category->title} (ID: {$category->widget_category_id})");
                }
            }
        } catch (\Exception $e) {
            Log::error("Duplicate kategori temizleme hatası: " . $e->getMessage());
        }
    }
    
    private function createMainCategories()
    {
        // Önce tüm kategorileri temizle
        $this->cleanupAllCategories();
        
        // Modül bileşenleri için ana kategori
        $modulesCategory = WidgetCategory::create([
            'title' => 'Modül Bileşenleri',
            'slug' => 'modul-bilesenleri',
            'description' => 'Sistem modüllerine ait bileşenler',
            'icon' => 'fa-cubes',
            'order' => 1,
            'is_active' => true,
            'parent_id' => null,
            'has_subcategories' => true
        ]);
        
        Log::info("Ana kategori oluşturuldu: {$modulesCategory->title} (slug: {$modulesCategory->slug})");
        
        // Blocks klasörünü tara ve içindeki klasörleri kategori olarak oluştur
        $blocksPath = base_path('Modules/WidgetManagement/resources/views/blocks');
        
        if (File::isDirectory($blocksPath)) {
            $folders = File::directories($blocksPath);
            $order = 2;
            
            foreach ($folders as $folder) {
                $folderName = basename($folder);
                
                // modules klasörünü atla, onun için ayrı bir işlem yapacağız
                if ($folderName === 'modules') {
                    continue;
                }
                
                // Klasör adını düzgün formata çevir
                $title = ucfirst(str_replace(['-', '_'], ' ', $folderName));
                $slug = Str::slug($folderName);
                
                // İkon belirleme
                $icon = $this->getCategoryIcon($folderName);
                
                // Kategori oluştur
                $category = WidgetCategory::create([
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $title . ' bileşenleri',
                    'icon' => $icon,
                    'order' => $order++,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => false
                ]);
                
                Log::info("Kategori oluşturuldu: {$category->title} (slug: {$category->slug})");
            }
        }
        
        return $modulesCategory;
    }
    
    /**
     * Kategori için ikon belirle
     */
    private function getCategoryIcon($folderName)
    {
        $icons = [
            'cards' => 'fa-id-card',
            'content' => 'fa-align-left',
            'features' => 'fa-star',
            'form' => 'fa-wpforms',
            'hero' => 'fa-heading',
            'layout' => 'fa-columns',
            'media' => 'fa-photo-video',
            'testimonials' => 'fa-quote-right',
            'slider' => 'fa-images'
        ];
        
        return $icons[$folderName] ?? 'fa-puzzle-piece';
    }
    
    /**
     * Tüm kategorileri temizle
     */
    private function cleanupAllCategories()
    {
        // Önce tüm kategorileri temizle
        WidgetCategory::truncate();
        Log::info("Tüm kategoriler temizlendi.");
    }
    
    private function createModuleSubcategories($modulesCategory)
    {
        if (!$modulesCategory) {
            Log::error("Modül kategorisi bulunamadı, alt kategoriler oluşturulamıyor.");
            return;
        }
        
        // Modules klasörünü tara ve içindeki modül klasörlerini alt kategori olarak oluştur
        $modulesPath = base_path('Modules/WidgetManagement/resources/views/blocks/modules');
        
        if (File::isDirectory($modulesPath)) {
            $moduleFolders = File::directories($modulesPath);
            $order = 1;
            
            foreach ($moduleFolders as $moduleFolder) {
                $moduleName = basename($moduleFolder);
                
                // Modül adını düzgün formata çevir
                $title = ucfirst($moduleName) . ' Modülü';
                $slug = Str::slug($moduleName) . '-modulu';
                
                // İkon belirleme
                $icon = $this->getModuleIcon($moduleName);
                
                // Alt kategori oluştur
                $subcategory = WidgetCategory::create([
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $title . ' bileşenleri',
                    'icon' => $icon,
                    'order' => $order++,
                    'is_active' => true,
                    'parent_id' => $modulesCategory->widget_category_id,
                    'has_subcategories' => false
                ]);
                
                Log::info("Alt kategori oluşturuldu: {$subcategory->title} (slug: {$subcategory->slug})");
            }
        } else {
            // Modül klasörü yoksa, varsayılan olarak Page ve Portfolio modüllerini ekle
            $defaultModules = [
                [
                    'name' => 'page',
                    'title' => 'Sayfa Modülü',
                    'icon' => 'fa-file-alt'
                ],
                [
                    'name' => 'portfolio',
                    'title' => 'Portfolio Modülü',
                    'icon' => 'fa-briefcase'
                ]
            ];
            
            foreach ($defaultModules as $index => $module) {
                $slug = Str::slug($module['name']) . '-modulu';
                
                $subcategory = WidgetCategory::create([
                    'title' => $module['title'],
                    'slug' => $slug,
                    'description' => $module['title'] . ' bileşenleri',
                    'icon' => $module['icon'],
                    'order' => $index + 1,
                    'is_active' => true,
                    'parent_id' => $modulesCategory->widget_category_id,
                    'has_subcategories' => false
                ]);
                
                Log::info("Varsayılan alt kategori oluşturuldu: {$subcategory->title} (slug: {$subcategory->slug})");
            }
        }
    }
    
    /**
     * Modül için ikon belirle
     */
    private function getModuleIcon($moduleName)
    {
        $icons = [
            'page' => 'fa-file-alt',
            'portfolio' => 'fa-briefcase',
            'blog' => 'fa-blog',
            'gallery' => 'fa-images',
            'contact' => 'fa-envelope',
            'product' => 'fa-shopping-cart',
            'event' => 'fa-calendar-alt',
            'news' => 'fa-newspaper',
            'team' => 'fa-users'
        ];
        
        return $icons[$moduleName] ?? 'fa-puzzle-piece';
    }
}