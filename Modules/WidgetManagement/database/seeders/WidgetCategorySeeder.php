<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Modules\WidgetManagement\app\Models\WidgetCategory;
use Illuminate\Support\Facades\Schema;
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
            // Önce tüm modül kategorilerini temizle
            WidgetCategory::where('slug', 'modul-bilesenleri')->delete();
            
            // Ana kategorileri oluştur
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
        // Ana kategoriler
        $predefinedCategories = [
            'modul-bilesenleri' => [
                'title' => 'Modül Bileşenleri',
                'description' => 'Sistem modüllerine ait bileşenler',
                'icon' => 'fa-cubes',
                'order' => 1,
                'has_subcategories' => true
            ],
            'kartlar' => [
                'title' => 'Kartlar',
                'description' => 'Kart tipi bileşenler için şablonlar',
                'icon' => 'fa-th-large',
                'order' => 2
            ],
            'icerikler' => [
                'title' => 'İçerikler',
                'description' => 'Metin ve içerik türleri için şablonlar',
                'icon' => 'fa-file-alt',
                'order' => 3
            ],
            'ozellikler' => [
                'title' => 'Özellikler',
                'description' => 'Özellik listeleme bileşenleri',
                'icon' => 'fa-list',
                'order' => 4
            ],
            'formlar' => [
                'title' => 'Formlar',
                'description' => 'Form ve giriş elemanları',
                'icon' => 'fa-wpforms',
                'order' => 5
            ],
            'herolar' => [
                'title' => 'Herolar',
                'description' => 'Ana başlık ve tanıtım bileşenleri',
                'icon' => 'fa-heading',
                'order' => 6
            ],
            'yerlesimler' => [
                'title' => 'Yerleşimler',
                'description' => 'Sayfa düzeni ve yerleşim şablonları',
                'icon' => 'fa-columns',
                'order' => 7
            ],
            'medya' => [
                'title' => 'Medya',
                'description' => 'Görsel, video ve diğer medya elemanları',
                'icon' => 'fa-photo-video',
                'order' => 8
            ],
            'referanslar' => [
                'title' => 'Referanslar',
                'description' => 'Müşteri yorumları ve referanslar',
                'icon' => 'fa-comment-dots',
                'order' => 9
            ],
            'sliderlar' => [
                'title' => 'Sliderlar',
                'description' => 'Slider ve carousel içeren bileşenler',
                'icon' => 'fa-sliders-h',
                'order' => 10
            ]
        ];
        
        foreach ($predefinedCategories as $slug => $category) {
            try {
                // Önce mevcut kategoriyi kontrol et
                $existingCategory = WidgetCategory::where('slug', $slug)->first();
                
                if (!$existingCategory) {
                    WidgetCategory::create([
                        'title' => $category['title'],
                        'slug' => $slug,
                        'description' => $category['description'],
                        'order' => $category['order'],
                        'is_active' => true,
                        'icon' => $category['icon'],
                        'parent_id' => null,
                        'has_subcategories' => $category['has_subcategories'] ?? false
                    ]);
                    
                    Log::info("Kategori oluşturuldu: {$category['title']} (slug: $slug)");
                } else {
                    $existingCategory->update([
                        'title' => $category['title'],
                        'description' => $category['description'],
                        'order' => $category['order'],
                        'is_active' => true,
                        'icon' => $category['icon'],
                        'has_subcategories' => $category['has_subcategories'] ?? false
                    ]);
                    Log::info("Kategori güncellendi: {$category['title']} (slug: $slug)");
                }
            } catch (\Exception $e) {
                Log::error("Kategori oluşturma hatası (slug: $slug): " . $e->getMessage());
            }
        }
    }
    
    private function createModuleSubcategories(): void
    {
        // Modüller ana kategorisini bul
        $modulesCategory = WidgetCategory::where('slug', 'modul-bilesenleri')->first();
        
        if (!$modulesCategory) {
            Log::warning('Modül ana kategorisi bulunamadı, alt kategoriler oluşturulamıyor...');
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
                    $existingSubcategory->update([
                        'title' => $category['title'],
                        'description' => $category['description'],
                        'order' => $category['order'],
                        'is_active' => true,
                        'icon' => $category['icon'],
                        'parent_id' => $modulesCategory->widget_category_id,
                        'has_subcategories' => false
                    ]);
                    Log::info("Alt kategori güncellendi: {$category['title']} (slug: $slug)");
                }
            } catch (\Exception $e) {
                Log::error("Alt kategori oluşturma hatası (slug: $slug): " . $e->getMessage());
            }
        }
    }
}