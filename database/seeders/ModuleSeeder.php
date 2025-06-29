<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Stancl\Tenancy\Facades\Tenancy;
use App\Models\Tenant;
use App\Helpers\TenantHelpers;

class ModuleSeeder extends Seeder
{
    // Çalıştırılan seeder'ları izlemek için
    private $executedSeeders = [];

    public function run(): void
    {
        $modulesPath = base_path('Modules');
        
        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);
        
        // Central veritabanında çalıştırılacak seeder'lar
        if (TenantHelpers::isCentral()) {
            $this->command->info("Running CENTRAL database seeders");
            $this->runCentralSeeders($modules);
        }
        
        // Tenant veritabanlarında çalıştırılacak seeder'lar
        $this->runTenantSeeders($modules);
    }

    /**
     * Central veritabanında çalıştırılacak seeder'ları yürüt
     */
    private function runCentralSeeders($modules): void
    {
        foreach ($modules as $modulePath) {
            $moduleBaseName = basename($modulePath);
            $seederPath = $modulePath . '/Database/Seeders';
            
            if (!File::exists($seederPath)) {
                continue;
            }

            $files = File::files($seederPath);
            
            // Ana modül seeder'ı için
            $moduleSeederName = $moduleBaseName . "Seeder";
            $moduleSeederClassName = "Modules\\" . $moduleBaseName . "\\Database\\Seeders\\" . $moduleSeederName;
            
            // Önce ana modül seeder'ını çalıştır (varsa)
            if (class_exists($moduleSeederClassName) && !in_array($moduleSeederClassName . '_central', $this->executedSeeders)) {
                $this->command->info("Seeding central module: {$moduleSeederClassName}");
                $this->call($moduleSeederClassName);
                $this->executedSeeders[] = $moduleSeederClassName . '_central';
                
                // SettingManagement modülü özel durum - alt seeder'ları tekrar çalıştırmaya çalışma
                if ($moduleBaseName === 'SettingManagement') {
                    $this->command->info("SettingManagement module seeders already run through the main seeder, skipping individual seeders");
                    continue;
                }
            }
            
            // Diğer bireysel seeder'ları çalıştır (ana modül seeder'ı içinde çağrılmamışsa)
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $className = str_replace('.php', '', $file->getFilename());
                    
                    // Ana modül seeder'ını tekrar çalıştırma
                    if ($className === $moduleSeederName) {
                        continue;
                    }
                    
                    $fullClassName = "Modules\\" . $moduleBaseName . "\\Database\\Seeders\\" . $className;
                    
                    if (class_exists($fullClassName) && !in_array($fullClassName . '_central', $this->executedSeeders)) {
                        $this->command->info("Seeding central: {$fullClassName}");
                        $this->call($fullClassName);
                        $this->executedSeeders[] = $fullClassName . '_central';
                    }
                }
            }
        }
    }

    /**
     * Tenant veritabanlarında çalıştırılacak seeder'ları yürüt
     */
    private function runTenantSeeders($modules): void
    {
        // Tüm tenant'ları al
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->command->info("No tenants found, skipping tenant seeders");
            return;
        }
        
        foreach ($tenants as $tenant) {
            // Tenant 1 (central) için seeder'ı çalıştırma, çünkü central olarak zaten çalıştırıldı
            if ($tenant->id == 1) {
                $this->command->info("Skipping tenant {$tenant->id} seeders as it's the central database");
                continue;
            }
            $this->command->info("Initializing tenant: {$tenant->id}");
            
            // Tenant bağlamını başlat
            tenancy()->initialize($tenant);
            
            foreach ($modules as $modulePath) {
                $moduleBaseName = basename($modulePath);
                
                // SettingManagement modülünü tenant'larda atla
                if ($moduleBaseName === 'SettingManagement') {
                    $this->command->info("Skipping SettingManagement module seeders for tenant: {$tenant->id}");
                    continue;
                }
                
                $seederPath = $modulePath . '/Database/Seeders';
                
                if (!File::exists($seederPath)) {
                    continue;
                }

                $files = File::files($seederPath);
                
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $className = str_replace('.php', '', $file->getFilename());
                        $fullClassName = "Modules\\" . $moduleBaseName . "\\Database\\Seeders\\" . $className;
                        
                        // Seeder'ı tenant_id ile birlikte benzersiz olarak işaretler
                        $uniqueKey = $fullClassName . '_' . $tenant->id;
                        
                        if (class_exists($fullClassName) && !in_array($uniqueKey, $this->executedSeeders)) {
                            $this->command->info("Seeding tenant {$tenant->id}: {$fullClassName}");
                            $this->call($fullClassName);
                            $this->executedSeeders[] = $uniqueKey;
                        }
                    }
                }
            }
            
            // Her tenant için varsayılan site dili oluştur
            $this->command->info("Seeding site languages for tenant: {$tenant->id}");
            $this->call(\Modules\LanguageManagement\Database\Seeders\TenantLanguagesSeeder::class);
            
            // Tenant bağlamını sonlandır ve central'a geri dön
            tenancy()->end();
        }
    }
}