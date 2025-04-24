<?php
namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WidgetCategorySeeder extends Seeder
{
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
            // Ana kategorileri önce oluştur
            $this->createMainCategories();
            
            // Tüm tabloyu yenile - veritabanından en güncel bilgileri al
            DB::reconnect();
            
            // Bir süre bekleyelim
            sleep(1);
            
            // Modules alt kategorilerini oluştur
            $this->createModuleSubcategories();
            
            Log::info('Widget kategorileri başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            Log::error('WidgetCategorySeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            if ($this->command) {
                $this->command->error('WidgetCategorySeeder hatası: ' . $e->getMessage());
            }
        }
    }
    
    private function createMainCategories(): void
    {
        // Önceden tanımlanmış ana kategoriler
        $predefinedCategories = [
            'kart-bilesenleri' => [
                'title' => 'Kart Bileşenleri',
                'description' => 'Kart tipi bileşenler için şablonlar',
                'icon' => 'fa-th-large'
            ],
            'icerik-bilesenleri' => [
                'title' => 'İçerik Bileşenleri',
                'description' => 'Metin ve içerik türleri için şablonlar',
                'icon' => 'fa-file-alt'
            ],
            'ozellik-bilesenleri' => [
                'title' => 'Özellik Bileşenleri',
                'description' => 'Özellik listeleme bileşenleri',
                'icon' => 'fa-list'
            ],
            'form-bilesenleri' => [
                'title' => 'Form Bileşenleri',
                'description' => 'Form ve giriş elemanları',
                'icon' => 'fa-wpforms'
            ],
            'hero-bilesenleri' => [
                'title' => 'Hero Bileşenleri',
                'description' => 'Ana başlık ve tanıtım bileşenleri',
                'icon' => 'fa-heading'
            ],
            'yerlesim-bilesenleri' => [
                'title' => 'Yerleşim Bileşenleri',
                'description' => 'Sayfa düzeni ve yerleşim şablonları',
                'icon' => 'fa-columns'
            ],
            'medya-bilesenleri' => [
                'title' => 'Medya Bileşenleri',
                'description' => 'Görsel, video ve diğer medya elemanları',
                'icon' => 'fa-photo-video'
            ],
            'referans-bilesenleri' => [
                'title' => 'Referans Bileşenleri',
                'description' => 'Müşteri yorumları ve referanslar',
                'icon' => 'fa-comment-dots'
            ],
            'slider-bilesenleri' => [
                'title' => 'Slider Bileşenleri',
                'description' => 'Slider ve carousel içeren bileşenler',
                'icon' => 'fa-sliders-h'
            ],
            'modul-bilesenleri' => [
                'title' => 'Modül Bileşenleri',
                'description' => 'Sistem modüllerine ait bileşenler',
                'icon' => 'fa-cubes',
                'has_subcategories' => true
            ]
        ];
        
        $order = 1;
        foreach ($predefinedCategories as $slug => $category) {
            try {
                // Önce mevcut kategoriyi kontrol et
                $existingCategory = WidgetCategory::where('slug', $slug)->first();
                
                if (!$existingCategory) {
                    WidgetCategory::create([
                        'title' => $category['title'],
                        'slug' => $slug,
                        'description' => $category['description'],
                        'order' => $order,
                        'is_active' => true,
                        'icon' => $category['icon'],
                        'parent_id' => null,
                        'has_subcategories' => $category['has_subcategories'] ?? false
                    ]);
                }
                
                Log::info("Kategori oluşturuldu veya güncellendi: {$category['title']} (slug: $slug)");
                $order++;
            } catch (\Exception $e) {
                Log::error("Kategori oluşturma hatası (slug: $slug): " . $e->getMessage());
            }
        }
    }
    
    private function createModuleSubcategories(): void
    {
        // Modüller ana kategorisini bul
        $modulesCategory = DB::table('widget_categories')
            ->where('slug', 'modul-bilesenleri')
            ->first();
        
        if (!$modulesCategory) {
            Log::warning('Modül ana kategorisi bulunamadı, alt kategoriler oluşturulamıyor... Doğrudan SQL ile bakılıyor.');
            
            // Direkt SQL ile kontrol et
            $result = DB::select('SELECT * FROM widget_categories WHERE slug = ?', ['modul-bilesenleri']);
            
            if (empty($result)) {
                if ($this->command) {
                    $this->command->info('Modül ana kategorisi SQL ile de bulunamadı, şimdi oluşturulacak...');
                }
                
                // Modüller ana kategorisini doğrudan oluştur
                $moduleCategoryId = DB::table('widget_categories')->insertGetId([
                    'title' => 'Modül Bileşenleri',
                    'slug' => 'modul-bilesenleri', 
                    'description' => 'Sistem modüllerine ait bileşenler',
                    'icon' => 'fa-cubes',
                    'order' => 99,
                    'is_active' => true,
                    'parent_id' => null,
                    'has_subcategories' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                Log::info('Modül ana kategorisi manuel olarak oluşturuldu. ID: ' . $moduleCategoryId);
                $modulesCategory = (object) ['widget_category_id' => $moduleCategoryId];
            } else {
                $modulesCategory = $result[0];
                Log::info('Modül kategorisi SQL ile bulundu. ID: ' . $modulesCategory->widget_category_id);
            }
        }
        
        if (!$modulesCategory) {
            Log::error('Modül kategorisi oluşturulamadı, işlem durduruluyor...');
            return;
        }
        
        Log::info('Modül Kategorisi ID: ' . $modulesCategory->widget_category_id);
        
        // Alt kategoriler
        $subCategories = [
            'sayfa-modulu' => [
                'title' => 'Sayfa Modülü',
                'description' => 'Sayfa içeriklerine ait bileşenler',
                'icon' => 'fa-file',
                'order' => 1
            ],
            'portfolio-modulu' => [
                'title' => 'Portfolio Modülü',
                'description' => 'Portfolio içeriklerine ait bileşenler',
                'icon' => 'fa-images',
                'order' => 2
            ]
        ];
        
        foreach ($subCategories as $slug => $category) {
            try {
                // Önce mevcut alt kategoriyi kontrol et
                $existingSubcategory = WidgetCategory::where('slug', $slug)->first();
                
                if (!$existingSubcategory) {
                    WidgetCategory::create([
                        'title' => $category['title'],
                        'slug' => $slug,
                        'description' => $category['description'],
                        'order' => $category['order'],
                        'is_active' => true,
                        'icon' => $category['icon'],
                        'parent_id' => $modulesCategory->widget_category_id,
                        'has_subcategories' => false
                    ]);
                    Log::info("Alt kategori oluşturuldu: {$category['title']} (slug: $slug)");
                } else {
                    Log::info("Alt kategori zaten mevcut: {$category['title']} (slug: $slug)");
                }
            } catch (\Exception $e) {
                Log::error("Alt kategori oluşturma hatası (slug: $slug): " . $e->getMessage());
            }
        }
    }
}