<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Modules\WidgetManagement\app\Models\WidgetCategory;

class WidgetManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // Çalıştırma izleme anahtarı
    private static $runKey = 'widget_management_seeder_executed';
    
    public function run()
    {
        Model::unguard();

        try {
            // Tenant kontrolü - eğer tenant veritabanındaysak ve widget_categories tablosu yoksa işlemi atla
            if (function_exists('tenant') && tenant() && !Schema::hasTable('widget_categories')) {
                return;
            }

            // Seeder'ın daha önce çalıştırılıp çalıştırılmadığını kontrol et
            $cacheKey = self::$runKey . '_' . Config::get('database.default');
            if (Cache::has($cacheKey)) {
                return;
            }
            
            // Bağlantının merkezi veritabanına yönlendirildiğinden emin ol
            $currentConnection = Config::get('database.default');
            
            if (!function_exists('tenant') || !tenant()) {
                $moduleCategory = WidgetCategory::where('slug', 'modul-bilesenleri')
                    ->orWhere('title', 'Modül Bileşenleri')
                    ->first();
                
                if (!$moduleCategory) {
                    
                    try {
                        // Önce tüm kategorileri temizleyelim
                        $this->cleanupCategories();
                        
                        // Auto increment değerini sıfırla
                        DB::statement('ALTER TABLE widget_categories AUTO_INCREMENT = 1;');
                        
                        // Doğrudan SQL ile ID'si 1 olacak şekilde oluştur
                        DB::statement('INSERT INTO widget_categories (title, slug, description, icon, `order`, is_active, parent_id, has_subcategories, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())', [
                            'Modül Bileşenleri',
                            'modul-bilesenleri',
                            'Sistem modüllerine ait bileşenler',
                            'fa-cubes',
                            1, // order
                            1, // is_active
                            null, // parent_id
                            1 // has_subcategories
                        ]);
                        
                        $moduleCategory = WidgetCategory::find(1);
                        
                        if (!$moduleCategory) {
                            throw new \Exception("Modül Bileşenleri kategorisi ID 1 olarak oluşturulamadı");
                        }
                        
                        Log::info("Modül Bileşenleri kategorisi oluşturuldu (ID: {$moduleCategory->widget_category_id})");
                    } catch (\Exception $e) {
                        Log::error("Modül Bileşenleri kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                    }
                } else {
                }
            }
            
            $this->call([
                WidgetCategorySeeder::class,   // Widget kategorilerini oluştur
                ModuleWidgetSeeder::class,   // Modül bileşenlerini oluştur
                BlockWidgetSeeder::class,    // Blok bileşenlerini oluştur
                SliderWidgetSeeder::class,   // Slider bileşenlerini oluştur
                HeroWidgetSeeder::class      // Hero bileşenlerini oluştur
            ]);
            
            
            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('WidgetManagementDatabaseSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    
    // Kategorileri temizle
    private function cleanupCategories()
    {
        try {
            // Foreign key constraint nedeniyle truncate kullanamıyoruz
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Tüm kategorileri temizle
            DB::table('widget_categories')->delete();
            Log::info("Tüm kategoriler silindi.");
            
            // Veritabanı tablolarını sıfırla
            DB::statement('ALTER TABLE widget_categories AUTO_INCREMENT = 1;');
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {
            Log::error("Kategori temizleme hatası: " . $e->getMessage());
        }
    }
}