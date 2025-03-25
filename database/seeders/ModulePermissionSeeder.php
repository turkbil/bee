<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use App\Models\Tenant;
use Modules\ModuleManagement\App\Models\Module;
use Modules\UserManagement\App\Models\ModulePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModulePermissionSeeder extends Seeder
{
    /**
     * Modül izinlerini oluştur
     */
    public function run(): void
    {
        // Önce ilişkili tabloları temizleyelim
        $this->cleanPermissionTables();
        
        // Temel izin tipleri
        $permissionTypes = [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'update' => 'Güncelleme',
            'delete' => 'Silme',
        ];
        
        // Mevcut modülleri bul
        $modulesPath = base_path('Modules');
        
        if (!File::exists($modulesPath)) {
            $this->command->warn('Modules dizini bulunamadı!');
            return;
        }

        $moduleDirectories = File::directories($modulesPath);
        $moduleNames = array_map('basename', $moduleDirectories);
        
        // Her modül için izinleri oluştur
        foreach ($moduleNames as $moduleName) {
            // Modül adı ve slug'ını hazırla - Tireleri kaldırıyoruz
            $moduleSlug = Str::slug(Str::snake($moduleName), '');
            $displayName = Str::title(Str::snake($moduleName, ' '));
            
            $this->command->info("Modül izinleri oluşturuluyor: {$displayName}");
            
            // Her izin tipi için oluştur
            foreach ($permissionTypes as $type => $label) {
                $permissionName = "{$moduleSlug}.{$type}";
                $description = "{$displayName} - {$label}";
                
                // İzni oluştur
                Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'description' => $description
                ]);
            }
        }
        
        $this->command->info('Modül izinleri başarıyla oluşturuldu!');
    }
    
    /**
     * İzin tablolarını temizle (ilişkili tablolar önce temizlenmeli)
     */
    private function cleanPermissionTables(): void
    {
        $this->command->info('İzin tabloları temizleniyor...');
        
        // İlişkili tabloları önce temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        if (Schema::hasTable('model_has_permissions')) {
            DB::table('model_has_permissions')->truncate();
        }
        
        if (Schema::hasTable('model_has_roles')) {
            DB::table('model_has_roles')->truncate();
        }
        
        if (Schema::hasTable('role_has_permissions')) {
            DB::table('role_has_permissions')->truncate();
        }
        
        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}