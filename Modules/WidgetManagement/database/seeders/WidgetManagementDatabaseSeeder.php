<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WidgetManagementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        try {
            Log::info('WidgetManagementDatabaseSeeder başlatılıyor...');
            
            // Bağlantının merkezi veritabanına yönlendirildiğinden emin ol
            $currentConnection = Config::get('database.default');
            Log::info('Mevcut veritabanı bağlantısı: ' . $currentConnection);
            
            // Seeder'ları çalıştır - dikkat edilmesi gereken sıralama önemli!
            $this->call([
                WidgetCategorySeeder::class, // İlk önce kategorileri oluştur
                ModuleWidgetSeeder::class,   // Sonra modül bileşenlerini oluştur
                BlockWidgetSeeder::class,    // Sonra blok bileşenlerini oluştur
                SliderWidgetSeeder::class,   // Sonra slider bileşenlerini oluştur
                HeroWidgetSeeder::class      // En son hero bileşenlerini oluştur
            ]);
            
            Log::info('WidgetManagementDatabaseSeeder başarıyla tamamlandı.');
        } catch (\Exception $e) {
            Log::error('WidgetManagementDatabaseSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}