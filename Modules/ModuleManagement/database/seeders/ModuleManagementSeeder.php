<?php 
namespace Modules\ModuleManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Tenancy;

class ModuleManagementSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        // Eğer tenant kontekstinde çalışıyorsa, seederi çalıştırma
        if (app()->has('tenancy') && app(Tenancy::class)->initialized) {
            try {
                // Tenant veritabanında modules tablosu var mı kontrol et
                if (!Schema::hasTable('modules')) {
                    $this->command->info('Tenant veritabanında modules tablosu bulunamadı. Bu normal bir durumdur, modüller merkezi veritabanında yönetilir.');
                    return;
                }
            } catch (\Exception $e) {
                $this->command->info('Tenant veritabanı kontrol hatası: ' . $e->getMessage());
                return;
            }
        }

        try {
            $modules = [
                [
                    'name' => 'modulemanagement',
                    'display_name' => 'Modüller Yönetimi',
                    'description' => 'Sistem modüllerinin yönetimi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'system',
                    'is_active' => true
                ],
                [
                    'name' => 'tenantmanagement',
                    'display_name' => 'Domainler Yönetimi', 
                    'description' => 'Çoklu müşteri yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'system',
                    'is_active' => true
                ],
                [
                    'name' => 'usermanagement',
                    'display_name' => 'Kullanıcılar',
                    'description' => 'Kullanıcı yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'management',
                    'is_active' => true
                ],
                [
                    'name' => 'settingmanagement',
                    'display_name' => 'Ayarlar Yönetimi',
                    'description' => 'Sistem ayarlarının yönetimi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'system',
                    'is_active' => true
                ],
                [
                    'name' => 'widgetmanagement',
                    'display_name' => 'Bileşenler',
                    'description' => 'Widget bileşenlerinin yönetimi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'content',
                    'is_active' => true
                ],
                [
                    'name' => 'thememanagement',
                    'display_name' => 'Tema Yönetimi',
                    'description' => 'Tema yönetimi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'system',
                    'is_active' => true
                ],
                [
                    'name' => 'announcement',
                    'display_name' => 'Duyurular',
                    'description' => 'Duyuru yönetimi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'content',
                    'is_active' => true
                ],
                [
                    'name' => 'page',
                    'display_name' => 'Sayfalar',
                    'description' => 'Statik sayfa yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => '9',
                    'type' => 'content',
                    'is_active' => true
                ],
                [
                    'name' => 'portfolio',
                    'display_name' => 'Portfolyo',
                    'description' => 'Portföy yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => '10',
                    'type' => 'content',
                    'is_active' => true
                ],
            ];

            // Tablo var mı kontrol et
            if (Schema::hasTable('modules')) {
                // Foreign key constraint hatası için DISABLE FOREIGN_KEY_CHECKS
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Temizlik yapalım önce
                DB::table('modules')->truncate();
                
                // Foreign key constraint'leri tekrar aktif et
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } else {
                $this->command->info('modules tablosu bulunamadı, oluşturuluyor...');
                return;
            }

            // manuel olarak ekleyelim
            foreach ($modules as $moduleData) {
                DB::table('modules')->insert([
                    'name' => $moduleData['name'],
                    'display_name' => $moduleData['display_name'],
                    'description' => $moduleData['description'],
                    'version' => $moduleData['version'],
                    'settings' => $moduleData['settings'],
                    'type' => $moduleData['type'],
                    'is_active' => $moduleData['is_active'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            $this->command->info('Modules seeded successfully!');
        } catch (\Exception $e) {
            Log::error('Module seeding failed: ' . $e->getMessage());
            $this->command->error('Module seeding error: ' . $e->getMessage());
        }
    }
}
