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
            return;
        }
        
        // Cache kontrolü
        $cacheKey = self::$runKey . '_' . Config::get('database.default');
        if (Cache::has($cacheKey)) {
            return;
        }
        
        // Tablo var mı kontrol et
        if (!Schema::hasTable('widget_categories')) {
            Log::warning('widget_categories tablosu bulunamadı, işlem atlanıyor...');
            if ($this->command) {
                $this->command->info('widget_categories tablosu bulunamadı, işlem atlanıyor...');
            }
            return;
        }

        try {
            // Moduller kategorisini kontrol et
            $moduleCategory = WidgetCategory::where('slug', 'moduller')
                ->orWhere('title', 'Moduller')
                ->first();
            
            if (!$moduleCategory) {
                
                try {
                    // Önce tüm kategorileri ve widget'ları temizle
                    $this->cleanupAllCategories();
            
                    // Auto increment değerini sıfırla
                    DB::statement('ALTER TABLE widget_categories AUTO_INCREMENT = 1;');
                    
                    // Doğrudan SQL ile ID'si 1 olacak şekilde oluştur
                    DB::statement('INSERT INTO widget_categories (title, slug, description, icon, `order`, is_active, parent_id, has_subcategories, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', [
                        'Moduller',
                        'moduller',
                        'Sistem modüllerine ait bileşenler',
                        'fa-cubes',
                        1, // order
                        1, // is_active
                        null, // parent_id
                        1 // has_subcategories
                    ]);
                    
                    $moduleCategory = WidgetCategory::find(1);
                    
                    if (!$moduleCategory) {
                        throw new \Exception("Moduller kategorisi ID 1 olarak oluşturulamadı");
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Moduller kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                    return;
                }
            } else {
                
                // Önce tüm kategorileri ve widget'ları temizle
                $this->cleanupAllCategories();
            }

            // Ana kategorileri oluştur
            $modulesCategory = $this->createMainCategories();
            
            if (!$modulesCategory) {
                Log::error('Modül ana kategorisi bulunamadı, alt kategoriler oluşturulamıyor...');
                return;
            }
            
            // Alt kategorileri oluştur
            $this->createModuleSubcategories($modulesCategory);
            
            if ($this->command) {
                $this->command->info('Widget kategorileri ve widget\'lar başarıyla oluşturuldu.');
            }
            
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
        // Moduller kategorisini kontrol et ve yoksa oluştur
        $modulesCategory = WidgetCategory::where('slug', 'moduller')
            ->orWhere('title', 'Moduller')
            ->first();
            
        if (!$modulesCategory) {
            try {
                // Doğrudan SQL ile ID'si 1 olacak şekilde oluştur
                DB::statement('INSERT INTO widget_categories (title, slug, description, icon, `order`, is_active, parent_id, has_subcategories, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', [
                    'Moduller',
                    'moduller',
                    'Sistem modüllerine ait bileşenler',
                    'fa-cubes',
                    1, // order
                    1, // is_active
                    null, // parent_id
                    1 // has_subcategories
                ]);
                
                $modulesCategory = WidgetCategory::find(1);
                
                if (!$modulesCategory) {
                    throw new \Exception("Moduller kategorisi ID 1 olarak oluşturulamadı");
                }
                
            } catch (\Exception $e) {
                Log::error("Moduller kategorisi oluşturma hatası: " . $e->getMessage());
                return null;
            }
        } else {
        }
        
        // 3. Blocks klasörünü tara ve modules dışındaki her ana klasörü kategori olarak oluştur
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
                
                // 4. Ana klasörlerin içindeki her klasör, o klasörün kategorisinin widget'ı olacak
                $this->createWidgetsForCategory($folder, $category);
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
        try {
            // Foreign key constraint nedeniyle truncate kullanamıyoruz
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Önce tüm widget'ı temizle
            Widget::query()->delete();
            
            // Sonra tüm kategorileri temizle
            WidgetCategory::query()->delete();
            
            // Veritabanı tablolarını sıfırla
            DB::statement('ALTER TABLE widgets AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE widget_categories AUTO_INCREMENT = 1;');
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
        } catch (\Exception $e) {
            Log::error("Kategori ve widget temizleme hatası: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function createModuleSubcategories($modulesCategory)
    {
        if (!$modulesCategory) {
            Log::error("Modül kategorisi bulunamadı, alt kategoriler oluşturulamıyor.");
            return;
        }
        
        // 2. Modules klasörünü tara ve içindeki modül klasörlerini alt kategori olarak oluştur
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
                
                
                // 4. Modüllerin içindeki her klasör, o modülün widget'ı olacak
                $this->createWidgetsForModule($moduleFolder, $subcategory);
            }
        } else {
            Log::warning("Modül klasörü bulunamadı: $modulesPath");
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
            'announcement' => 'fa-announcement',
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
    
    /**
     * Kategori için widget'ları oluştur
     */
    private function createWidgetsForCategory($categoryFolder, $category)
    {
        // Kategori klasörü içindeki alt klasörleri widget olarak oluştur
        if (File::isDirectory($categoryFolder)) {
            $widgetFolders = File::directories($categoryFolder);
            
            foreach ($widgetFolders as $widgetFolder) {
                $widgetName = basename($widgetFolder);
                $widgetViewPath = $widgetFolder . '/view.blade.php';
                
                // Eğer view.blade.php dosyası varsa
                if (File::exists($widgetViewPath)) {
                    $widgetTitle = ucfirst(str_replace(['-', '_'], ' ', $widgetName));
                    $widgetSlug = Str::slug(basename($categoryFolder) . '-' . $widgetName);
                    
                    // Widget'ın zaten var olup olmadığını kontrol et
                    $existingWidget = Widget::where('slug', $widgetSlug)->first();
                    
                    if (!$existingWidget) {
                        Widget::create([
                            'widget_category_id' => $category->widget_category_id,
                            'name' => $widgetTitle,
                            'slug' => $widgetSlug,
                            'description' => $widgetTitle . ' bileşeni',
                            'type' => 'file',
                            'file_path' => basename($categoryFolder) . '/' . $widgetName . '/view',
                            'has_items' => false,
                            'is_active' => true,
                            'is_core' => true,
                            'settings_schema' => $this->getWidgetSettings($widgetName)
                        ]);
                        
                    } else {
                        // Varolan widget'ı güncelle
                        $existingWidget->update([
                            'widget_category_id' => $category->widget_category_id,
                            'type' => 'file',
                            'file_path' => basename($categoryFolder) . '/' . $widgetName . '/view'
                        ]);
                        
                        Log::info("Widget güncellendi: $widgetTitle (slug: $widgetSlug)");
                    }
                }
            }
        }
    }
    
    /**
     * Modül için widget'ları oluştur
     */
    private function createWidgetsForModule($moduleFolder, $moduleCategory)
    {
        // Modül klasörü içindeki alt klasörleri widget olarak oluştur
        if (File::isDirectory($moduleFolder)) {
            $widgetFolders = File::directories($moduleFolder);
            
            foreach ($widgetFolders as $widgetFolder) {
                $widgetName = basename($widgetFolder);
                $widgetViewPath = $widgetFolder . '/view.blade.php';
                $moduleName = basename($moduleFolder);
                
                // Eğer view.blade.php dosyası varsa
                if (File::exists($widgetViewPath)) {
                    $widgetTitle = ucfirst(str_replace(['-', '_'], ' ', $widgetName));
                    $widgetSlug = Str::slug($moduleName . '-' . $widgetName);
                    
                    // Widget'ın zaten var olup olmadığını kontrol et
                    $existingWidget = Widget::where('slug', $widgetSlug)->first();
                    
                    if (!$existingWidget) {
                        Widget::create([
                            'widget_category_id' => $moduleCategory->widget_category_id,
                            'name' => $widgetTitle,
                            'slug' => $widgetSlug,
                            'description' => $widgetTitle . ' modül bileşeni',
                            'type' => 'module',
                            'file_path' => 'modules/' . $moduleName . '/' . $widgetName . '/view',
                            'has_items' => false,
                            'is_active' => true,
                            'is_core' => true,
                            'settings_schema' => $this->getModuleWidgetSettings($moduleName, $widgetName)
                        ]);
                        
                    } else {
                        // Varolan widget'ı güncelle
                        $existingWidget->update([
                            'widget_category_id' => $moduleCategory->widget_category_id,
                            'type' => 'module',
                            'file_path' => 'modules/' . $moduleName . '/' . $widgetName . '/view'
                        ]);
                        
                        Log::info("Modül widget'ı güncellendi: $widgetTitle (slug: $widgetSlug)");
                    }
                }
            }
        }
    }
    
    /**
     * Widget için temel ayarları getir
     */
    private function getWidgetSettings($widgetName)
    {
        // Temel ayarlar
        $settings = [
            [
                'name' => 'title',
                'label' => 'Başlık',
                'type' => 'text',
                'required' => true,
                'system' => true
            ],
            [
                'name' => 'unique_id',
                'label' => 'Benzersiz ID',
                'type' => 'text',
                'required' => false,
                'system' => true,
                'hidden' => true
            ]
        ];
        
        return $settings;
    }
    
    /**
     * Modül widget'ı için ayarları getir
     */
    private function getModuleWidgetSettings($moduleName, $widgetName)
    {
        // Temel ayarlar
        $settings = [
            [
                'name' => 'title',
                'label' => 'Başlık',
                'type' => 'text',
                'required' => true,
                'system' => true
            ],
            [
                'name' => 'unique_id',
                'label' => 'Benzersiz ID',
                'type' => 'text',
                'required' => false,
                'system' => true,
                'hidden' => true
            ]
        ];
        
        // Modül ve widget tipine göre özel ayarlar ekle
        if ($moduleName == 'page') {
            if ($widgetName == 'recent') {
                $settings[] = [
                    'name' => 'show_dates',
                    'label' => 'Tarihleri Göster',
                    'type' => 'checkbox',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'limit',
                    'label' => 'Gösterilecek Sayfa Sayısı',
                    'type' => 'number',
                    'required' => false,
                    'default' => 5
                ];
            } elseif ($widgetName == 'home') {
                $settings[] = [
                    'name' => 'show_title',
                    'label' => 'Sayfa Başlığını Göster',
                    'type' => 'checkbox',
                    'required' => false
                ];
            }
        } elseif ($moduleName == 'portfolio') {
            if ($widgetName == 'list') {
                $settings[] = [
                    'name' => 'show_description',
                    'label' => 'Açıklamayı Göster',
                    'type' => 'checkbox',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'limit',
                    'label' => 'Gösterilecek Proje Sayısı',
                    'type' => 'number',
                    'required' => false,
                    'default' => 6
                ];
            } elseif ($widgetName == 'detail') {
                $settings[] = [
                    'name' => 'project_id',
                    'label' => 'Proje ID',
                    'type' => 'number',
                    'required' => false
                ];
                $settings[] = [
                    'name' => 'show_gallery',
                    'label' => 'Galeriyi Göster',
                    'type' => 'checkbox',
                    'required' => false,
                    'default' => true
                ];
            }
        }
        
        return $settings;
    }
}