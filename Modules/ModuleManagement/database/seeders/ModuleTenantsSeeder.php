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
use App\Helpers\TenantHelpers;

class ModuleTenantsSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('ModuleTenantsSeeder sadece central veritabanında çalışır.');
            return;
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
                'menumanagement',
                'settingmanagement',
                'widgetmanagement',
                'thememanagement',
                'studio',
                'ai',
                'page',
                'languagemanagement',
                'seomanagement',
            ];
            // Sadece central tenant'a atanacak modüller
            $centralModuller = [
                'tenantmanagement',
            ];
            // Özel atama yapılacak modüller (tenant bazında manuel kontrol)
            $ozelModuller = [
                'announcement',
                'portfolio',
            ];

            foreach ($tenants as $tenant) {
                $tenantSeed = crc32($tenant);
                srand($tenantSeed);

                // Önce zorunlu modülleri ata
                foreach ($modules as $module) {
                    if (in_array($module->name, $zorunluModuller)) {
                        $isActive = true; // Zorunlu modüller %100 aktif olmalı
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
                
                // Central modülleri sadece central tenant'a ata
                foreach ($modules as $module) {
                    if (in_array($module->name, $centralModuller)) {
                        $tenantDomain = DB::table('domains')->where('tenant_id', $tenant)->value('domain');
                        
                        if ($tenantDomain === 'laravel.test') {
                            // Sadece central tenant'ta olsun
                            $isActive = true;
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
                
                // Özel modülleri tenant bazında ata
                foreach ($modules as $module) {
                    if (in_array($module->name, $ozelModuller)) {
                        $shouldAdd = false;
                        
                        // Tenant domain'ine göre özel atama kuralları
                        $tenantDomain = DB::table('domains')->where('tenant_id', $tenant)->value('domain');
                        
                        if ($tenantDomain === 'laravel.test') {
                            // Central tenant'ta tüm özel modüller olsun
                            $shouldAdd = true;
                        } elseif ($tenantDomain === 'a.test') {
                            // a.test'te hem announcement hem portfolio olsun
                            $shouldAdd = true;
                        } elseif ($tenantDomain === 'b.test' && $module->name === 'portfolio') {
                            // b.test'te sadece portfolio olsun
                            $shouldAdd = true;
                        } elseif ($tenantDomain === 'c.test' && $module->name === 'announcement') {
                            // c.test'te sadece announcement olsun
                            $shouldAdd = true;
                        }
                        
                        if ($shouldAdd) {
                            $isActive = true; // Özel modüller hep aktif
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

            // SettingManagement ve UserManagement türü modüller kontrolü
            $isUserTypeModule = (
                $moduleSlug === 'settingmanagement' || 
                $moduleSlug === 'settingsmanagement' || 
                $moduleSlug === 'usermanagement' ||
                $moduleSlug === 'menumanagement' ||
                strtolower($moduleName) === 'settingmanagement' || 
                strtolower($moduleName) === 'settingsmanagement' ||
                strtolower($moduleName) === 'usermanagement' ||
                strtolower($moduleName) === 'menumanagement'
            );

            // Temel izin tipleri - User type modüller için sadece view ve update olacak
            if ($isUserTypeModule) {
                // User type modüller için sadece görüntüleme ve güncelleme izinleri
                $permissionTypes = [
                    'view' => 'Görüntüleme',
                    'update' => 'Güncelleme',
                ];
                
                $this->command->info("User type modülü ({$moduleName}) için sadece view ve update izinleri oluşturuluyor - Tenant: {$tenantId}");
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