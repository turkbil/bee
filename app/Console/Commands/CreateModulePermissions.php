<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\ModuleManagement\App\Models\Module;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateModulePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:module-permissions {--module= : Belirli bir modül için izinleri oluştur}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm modüller için izinleri oluşturur ve rollere atar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleOption = $this->option('module');
        
        // Modülleri al
        if ($moduleOption) {
            $modules = Module::where('name', $moduleOption)->get();
            if ($modules->isEmpty()) {
                $this->error("'{$moduleOption}' adında bir modül bulunamadı!");
                return 1;
            }
        } else {
            $modules = Module::all();
        }
        
        // İzin tiplerini al
        $permissionTypes = config('module-permissions.permission_types', [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'update' => 'Güncelleme',
            'delete' => 'Silme'
        ]);
        
        // Rolleri al
        $rootRole = Role::where('name', 'root')->first();
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$rootRole || !$adminRole) {
            $this->error('Temel roller (root, admin) bulunamadı! Önce rolleri oluşturun.');
            return 1;
        }
        
        $this->info('Modül izinleri oluşturuluyor...');
        $bar = $this->output->createProgressBar(count($modules) * count($permissionTypes));
        $bar->start();
        
        $createdPermissions = 0;
        
        foreach ($modules as $module) {
            $this->newLine();
            $this->info("Modül: {$module->name} ({$module->display_name})");
            
            foreach ($permissionTypes as $type => $label) {
                $permissionName = "{$module->name}.{$type}";
                
                // İzin oluştur
                $permission = Permission::firstOrCreate(
                    [
                        'name' => $permissionName,
                        'guard_name' => 'web',
                    ],
                    [
                        'description' => "{$module->display_name} - " . ucfirst($label)
                    ]
                );
                
                // Root rolüne izni ata
                if (!$rootRole->hasPermissionTo($permission)) {
                    $rootRole->givePermissionTo($permission);
                }
                
                // Admin rolüne izni ata (TenantManagement hariç)
                if ($module->name !== 'tenantmanagement' && !$adminRole->hasPermissionTo($permission)) {
                    $adminRole->givePermissionTo($permission);
                }
                
                $createdPermissions++;
                $bar->advance();
            }
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info("Toplam {$createdPermissions} izin oluşturuldu ve rollere atandı.");
        
        return 0;
    }
}