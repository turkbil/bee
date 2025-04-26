<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

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
            // Seeder'ın daha önce çalıştırılıp çalıştırılmadığını kontrol et
            $cacheKey = self::$runKey . '_' . Config::get('database.default');
            if (Cache::has($cacheKey)) {
                Log::info('WidgetManagementDatabaseSeeder zaten çalıştırılmış, atlanıyor...');
                return;
            }
            
            Log::info('WidgetManagementDatabaseSeeder başlatılıyor...');
            
            // Bağlantının merkezi veritabanına yönlendirildiğinden emin ol
            $currentConnection = Config::get('database.default');
            Log::info('Mevcut veritabanı bağlantısı: ' . $currentConnection);
            
            // Diğer seeder'ları çalıştır - kategori oluşturulduktan sonra
            $this->call([
                WidgetCategorySeeder::class,   // Widget kategorilerini oluştur
                ModuleWidgetSeeder::class,   // Modül bileşenlerini oluştur
                BlockWidgetSeeder::class,    // Blok bileşenlerini oluştur
                SliderWidgetSeeder::class,   // Slider bileşenlerini oluştur
                HeroWidgetSeeder::class      // Hero bileşenlerini oluştur
            ]);
            
            Log::info('WidgetManagementDatabaseSeeder başarıyla tamamlandı.');
            
            // Seeder'ın çalıştırıldığını işaretle (10 dakika süreyle cache'de tut)
            Cache::put($cacheKey, true, 600);
        } catch (\Exception $e) {
            Log::error('WidgetManagementDatabaseSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}