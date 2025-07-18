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
use App\Helpers\TenantHelpers;

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
            // Bu seeder sadece central veritabanında çalışmalı
            if (!TenantHelpers::isCentral()) {
                $this->command->info('WidgetManagementDatabaseSeeder sadece central veritabanında çalışır.');
                return;
            }

            // Seeder'ın daha önce çalıştırılıp çalıştırılmadığını kontrol et
            $cacheKey = self::$runKey . '_' . Config::get('database.default');
            if (Cache::has($cacheKey)) {
                return;
            }
            $moduleCategory = WidgetCategory::where('slug', 'moduller')
                ->orWhere('title', 'Moduller')
                ->first();
            
            if (!$moduleCategory) {
                
                try {
                    // Önce tüm kategorileri temizleyelim
                    $this->cleanupCategories();
                    
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
                    
                    Log::info("Moduller kategorisi oluşturuldu (ID: {$moduleCategory->widget_category_id})");
                } catch (\Exception $e) {
                    Log::error("Moduller kategorisi oluşturulamadı. Hata: " . $e->getMessage());
                }
            }
            
            // Her bir seeder'ı ayrı ayrı çalıştır
            $this->call(WidgetCategorySeeder::class);
            $this->call(ModuleWidgetSeeder::class);
            $this->call(BlockWidgetSeeder::class);
            $this->call(SliderWidgetSeeder::class);
            $this->call(HeroWidgetSeeder::class); // HeroWidgetSeeder en son çalışsın
            
            // Seeder'ın çalıştırıldığını işaretle
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