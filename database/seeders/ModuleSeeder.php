<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Stancl\Tenancy\Facades\Tenancy;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modulesPath = base_path('Modules');
        
        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);
        
        foreach ($modules as $modulePath) {
            $seederPath = $modulePath . '/Database/Seeders';
            
            if (!File::exists($seederPath)) {
                continue;
            }

            $files = File::files($seederPath);
            
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $className = str_replace('.php', '', $file->getFilename());
                    $fullClassName = "Modules\\" . basename($modulePath) . "\\Database\\Seeders\\" . $className;
                    
                    if (class_exists($fullClassName)) {
                        // SettingManagement modülünün özel seeder'ları - bunları central'da çalıştır
                        if (basename($modulePath) === 'SettingManagement' && 
                           ($className === 'SettingsGroupsTableSeeder' || $className === 'SettingsTableSeeder')) {
                            $this->command->info("Seeding central only: {$fullClassName}");
                            $this->call($fullClassName);
                            continue;
                        }
                        
                        // SettingManagement ana seeder'ını atla - tenant'larda çalıştırma
                        if (basename($modulePath) === 'SettingManagement' && 
                            $className === 'SettingManagementSeeder') {
                            $this->command->info("Skipping tenant seeding for: {$fullClassName}");
                            continue;
                        }
                        
                        // Diğer tüm modüller için tenant'larda çalıştır
                        tenancy()->central(function () use ($fullClassName) {
                            $tenants = \App\Models\Tenant::all();
                            
                            foreach ($tenants as $tenant) {
                                tenancy()->initialize($tenant);
                                $this->command->info("Seeding: {$fullClassName} for tenant: {$tenant->id}");
                                $this->call($fullClassName);
                            }
                        });
                    }
                }
            }
        }
    }
}