<?php

namespace Modules\ModuleManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\ModuleManagement\App\Models\Module;
use Spatie\Permission\Models\Permission;
use Stancl\Tenancy\Tenancy;
use Illuminate\Support\Str;

class ModuleTenantsSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        // Eğer tenant kontekstinde çalışıyorsa, seederi çalıştırma
        if (app()->has('tenancy') && app(Tenancy::class)->initialized) {
            try {
                // Tenant veritabanında module_tenants tablosu var mı kontrol et
                if (!Schema::hasTable('module_tenants')) {
                    $this->command->info('Tenant veritabanında module_tenants tablosu bulunamadı. Bu normal bir durumdur, modül-tenant ilişkileri merkezi veritabanında yönetilir.');
                    return;
                }
            } catch (\Exception $e) {
                $this->command->info('Tenant veritabanı kontrol hatası: ' . $e->getMessage());
                return;
            }
        }

        try {
            // Önce tüm mevcut tenant ve modülleri alalım
            $tenants = DB::table('tenants')->pluck('id')->toArray();
            $modules = Module::all();

            // Tablo var mı kontrol et
            if (Schema::hasTable('module_tenants')) {
                // Foreign key constraint hatası için DISABLE FOREIGN_KEY_CHECKS
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Temizlik yapalım önce
                DB::table('module_tenants')->truncate();
                
                // Foreign key constraint'leri tekrar aktif et
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } else {
                $this->command->info('module_tenants tablosu bulunamadı, işlem atlanıyor...');
                return;
            }

            // Tüm tenantlara atanacak zorunlu modüller
            $zorunluModuller = [
                'modulemanagement',
                'usermanagement',
                'settingmanagement',
                'widgetmanagement',
                'thememanagement',
                'studio',
            ];
            // Rastgele atanacak modüller
            $rastgeleModuller = [
                'announcement',
                'page',
                'portfolio',
            ];

            foreach ($tenants as $tenant) {
                $tenantSeed = crc32($tenant);
                srand($tenantSeed);

                // Önce zorunlu modülleri ata
                foreach ($modules as $module) {
                    if (in_array($module->name, $zorunluModuller)) {
                        $isActive = rand(1, 100) <= 90; // %90 aktif olsun
                        DB::table('module_tenants')->insert([
                            'tenant_id' => $tenant,
                            'module_id' => $module->module_id,
                            'is_active' => $isActive,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        if ($isActive) {
                            $this->createPermissionsForTenant($tenant, $module->name);
                        }
                    }
                }
                // Sonra rastgele modülleri ata
                foreach ($modules as $module) {
                    if (in_array($module->name, $rastgeleModuller)) {
                        // %50 ihtimalle tenant'a eklensin
                        if (rand(1, 100) <= 50) {
                            $isActive = rand(1, 100) <= 90;
                            DB::table('module_tenants')->insert([
                                'tenant_id' => $tenant,
                                'module_id' => $module->module_id,
                                'is_active' => $isActive,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            if ($isActive) {
                                $this->createPermissionsForTenant($tenant, $module->name);
                            }
                        }
                    }
                }
            }
            
            $this->command->info('Modules-Tenant assignments and permissions seeded successfully!');
        } catch (\Exception $e) {
            Log::error('Modules-Tenant seeding failed: ' . $e->getMessage());
            $this->command->error('Modules-Tenant seeding error: ' . $e->getMessage());
        }
    }

    /**
     * Tenant için modüle ait izinleri oluştur
     * 
     * @param string $tenantId Tenant ID
     * @param string $moduleName Modül adı
     * @return void
     */
    private function createPermissionsForTenant($tenantId, $moduleName)
    {
        try {
            // Tenant'a geçiş yapalım
            tenancy()->initialize($tenantId);

            // Modül adı ve slug'ını hazırla - Tireleri kaldırıyoruz
            $moduleSlug = Str::slug(Str::snake($moduleName), '');
            $displayName = Str::title(Str::snake($moduleName, ' '));

            // SettingManagement modülü kontrolü - Dikkat: Dosya adları ekranda settingmanagement.* şeklinde
            $isSettingManagement = (
                $moduleSlug === 'settingmanagement' || 
                $moduleSlug === 'settingsmanagement' || 
                strtolower($moduleName) === 'settingmanagement' || 
                strtolower($moduleName) === 'settingsmanagement'
            );

            // Temel izin tipleri - Setting Management için sadece view ve update olacak
            if ($isSettingManagement) {
                // Setting Management için sadece görüntüleme ve güncelleme izinleri
                $permissionTypes = [
                    'view' => 'Görüntüleme',
                    'update' => 'Güncelleme',
                ];
                
                $this->command->info("Setting Management modülü için sadece view ve update izinleri oluşturuluyor - Tenant: {$tenantId}");
            } else {
                // Diğer modüller için tüm izinler
                $permissionTypes = [
                    'view' => 'Görüntüleme',
                    'create' => 'Oluşturma',
                    'update' => 'Güncelleme',
                    'delete' => 'Silme',
                ];
            }

            // Her izin tipi için oluştur
            foreach ($permissionTypes as $type => $label) {
                $permissionName = "{$moduleSlug}.{$type}";
                $description = "{$displayName} - {$label}";
                
                // İzin yoksa oluştur
                Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['description' => $description]
                );
            }

            // Tenant'dan çıkış yapalım
            tenancy()->end();
        } catch (\Exception $e) {
            Log::error("Permission seeding failed for tenant {$tenantId}, module {$moduleName}: " . $e->getMessage());
        }
    }
}