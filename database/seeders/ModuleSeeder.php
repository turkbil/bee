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
        
        // Bu noktada hangi seeders'ları çalıştırdığımızı takip etmek için dizi oluşturuyoruz
        $processedSeeders = [];
        
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
                        // SettingManagement modülünün özel seeders'ları için düzenleme
                        $moduleName = basename($modulePath);
                        
                        // Eğer daha önce SettingManagementSeeder çalıştırdıysak ve 
                        // şimdi doğrudan SettingsGroupsTableSeeder veya SettingsTableSeeder çalıştırmak istiyorsak atla
                        if ($moduleName === 'SettingManagement') {
                            $mainSeederName = "Modules\\SettingManagement\\Database\\Seeders\\SettingManagementSeeder";
                            
                            // Ana seeder zaten çalıştırıldıysa ve alt seeder çalıştırılmak isteniyorsa atla
                            if (in_array($mainSeederName, $processedSeeders) && 
                                ($className === 'SettingsGroupsTableSeeder' || $className === 'SettingsTableSeeder')) {
                                $this->command->info("Skipping already processed: {$fullClassName}");
                                continue;
                            }
                        }
                        
                        $this->command->info("Seeding central: {$fullClassName}");
                        $this->call($fullClassName);
                        
                        // İşlenen seeder'ı listeye ekle
                        $processedSeeders[] = $fullClassName;
                    }
                }
            }
        }
    }
}