<?php

namespace Modules\WidgetManagement\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

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
            
            // Seeder'ları çalıştır
            $this->call([
                WidgetCategorySeeder::class,
                FileWidgetSeeder::class,
                SliderWidgetSeeder::class
            ]);
            
            Log::info('WidgetManagementDatabaseSeeder başarıyla tamamlandı.');
        } catch (\Exception $e) {
            Log::error('WidgetManagementDatabaseSeeder hatası: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}