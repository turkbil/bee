<?php

namespace Modules\ModuleManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Tenancy;
use App\Helpers\TenantHelpers;

class ModuleManagementSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('ModuleManagementSeeder sadece central veritabanında çalışır.');
            return;
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
                    'display_name' => 'Alan Adı Yönetimi',
                    'description' => 'Çoklu müşteri yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'system',
                    'is_active' => true
                ],
                [
                    'name' => 'ai',
                    'display_name' => 'Yapay Zeka',
                    'description' => 'Yapay Zeka modülü',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'ai',
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
                    'name' => 'menumanagement',
                    'display_name' => 'Menü Yönetimi',
                    'description' => 'Site menü yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'management',
                    'is_active' => true
                ],
                [
                    'name' => 'mediamanagement',
                    'display_name' => 'Medya Yönetimi',
                    'description' => 'Universal medya yönetim sistemi - Image, Video, Audio, Document desteği',
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
                    'type' => 'widget',
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
                    'name' => 'studio',
                    'display_name' => 'Studio Editör',
                    'description' => 'Studio ile site yönetimi',
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
                [
                    'name' => 'blog',
                    'display_name' => 'Blog',
                    'description' => 'Blog yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'content',
                    'is_active' => true
                ],
                [
                    'name' => 'languagemanagement',
                    'display_name' => 'Dil Yönetimi',
                    'description' => 'Çoklu dil yönetim sistemi',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'system',
                    'is_active' => true
                ],
                [
                    'name' => 'seomanagement',
                    'display_name' => 'SEO Yönetimi',
                    'description' => 'Universal SEO yönetim sistemi - Çoklu dil desteği ile tüm modüller için merkezi SEO ayarları',
                    'version' => '1.0.0',
                    'settings' => null,
                    'type' => 'system',
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

            // Modülleri tüm tenant'lara otomatik ata
            $this->assignModulesToTenants();

            // Modüller için default permission'ları oluştur
            $this->createDefaultPermissions();

            $this->command->info('Modules seeded successfully!');
        } catch (\Exception $e) {
            Log::error('Module seeding failed: ' . $e->getMessage());
            $this->command->error('Module seeding error: ' . $e->getMessage());
        }
    }

    /**
     * Modülleri tüm tenant'lara otomatik ata
     */
    private function assignModulesToTenants()
    {
        try {
            // Tüm tenant'ları al
            $tenants = DB::table('tenants')->get();

            // Tüm modülleri al
            $modules = DB::table('modules')->get();

            foreach ($tenants as $tenant) {
                foreach ($modules as $module) {
                    // Eğer atama yoksa ekle
                    $exists = DB::table('module_tenants')
                        ->where('tenant_id', $tenant->id)
                        ->where('module_id', $module->module_id)
                        ->exists();

                    if (!$exists) {
                        DB::table('module_tenants')->insert([
                            'tenant_id' => $tenant->id,
                            'module_id' => $module->module_id,
                            'is_active' => true,
                            'assigned_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $this->command->info("Module '{$module->name}' assigned to tenant {$tenant->id}");
                    }
                }
            }

            $this->command->info('All modules assigned to all tenants successfully!');
        } catch (\Exception $e) {
            Log::error('Module tenant assignment failed: ' . $e->getMessage());
            $this->command->error('Module tenant assignment error: ' . $e->getMessage());
        }
    }

    /**
     * Modüller için default CRUD permission'ları oluştur
     */
    private function createDefaultPermissions()
    {
        try {
            // Tüm tenant'lara modül permission'larını oluştur
            $tenants = DB::table('tenants')->get();
            $modules = DB::table('modules')->get();

            foreach ($tenants as $tenant) {
                foreach ($modules as $module) {
                    // Permission service'i kullanarak otomatik permission oluştur
                    $permissionService = app(\App\Services\ModuleTenantPermissionService::class);

                    // Modül verilerini hazırla
                    $moduleData = [
                        'name' => $module->name,
                        'display_name' => $module->display_name,
                        'is_active' => $module->is_active
                    ];

                    // Permission'ları oluştur
                    $permissionService->handleModuleAddedToTenant($module->module_id, $tenant->id);

                    $this->command->info("Permissions created for {$module->name} in tenant {$tenant->id}");
                }
            }
        } catch (\Exception $e) {
            Log::error('Default permission creation failed: ' . $e->getMessage());
            $this->command->error('Default permission creation error: ' . $e->getMessage());
        }
    }
}
