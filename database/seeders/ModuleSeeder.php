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
            
            // Debug: Her modül için context durumunu kontrol et
            $contextStatus = TenantHelpers::isCentral() ? 'CENTRAL' : 'TENANT';
            $this->command->info("🔍 Processing module: {$moduleBaseName} - Context: {$contextStatus}");
            
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
                
                // Page ve Announcement modülleri için ana seeder varsa sadece onu çalıştır
                if (in_array($moduleBaseName, ['Page', 'Announcement']) && class_exists($moduleSeederClassName)) {
                    $this->command->info("{$moduleBaseName} module has main seeder, skipping individual seeders");
                    continue;
                }
            }
            
            // LanguageManagement özel durumu - Database seeder'ını çalıştır
            if ($moduleBaseName === 'LanguageManagement') {
                $langDbSeederClass = "Modules\\LanguageManagement\\Database\\Seeders\\LanguageManagementDatabaseSeeder";
                if (class_exists($langDbSeederClass) && !in_array($langDbSeederClass . '_central', $this->executedSeeders)) {
                    $this->command->info("Seeding LanguageManagement Database Seeder: {$langDbSeederClass}");
                    $this->call($langDbSeederClass);
                    $this->executedSeeders[] = $langDbSeederClass . '_central';
                }
                continue; // Diğer individual seeder'ları atlat
            }
            
            // Diğer bireysel seeder'ları çalıştır (ana modül seeder'ı içinde çağrılmamışsa)
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $className = str_replace('.php', '', $file->getFilename());
                    
                    // Ana modül seeder'ını tekrar çalıştırma
                    if ($className === $moduleSeederName) {
                        continue;
                    }
                    
                    // AI modülü özel durumları - ana seeder'da zaten çağrıldı
                    if ($moduleBaseName === 'AI' && in_array($className, [
                        'AIPurchaseSeeder', 
                        'AITenantSetupSeeder', 
                        'AIUsageUpdateSeeder',
                        'AIFeatureSeeder_Complete',
                        'AIFeatureSeeder_Master',
                        'AIFeatureSeeder_Part1',
                        'AIFeatureSeeder_Part1_Updated',
                        'AIFeatureSeeder_Part2',
                        'AIFeatureSeeder_Part3',
                        'AISEOFeaturesSeeder',  // Duplicate slug hatası önlenmesi için
                        'SeoAdvancedInputSystemSeeder'  // SEO expert prompts seeder'ı dahil et
                    ])) {
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
            // MenuManagement gibi tenant tabloları için tenant 1'de de çalıştırmalıyız
            // Sadece central-only işlemler atlanır
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
                        
                        // AI modülü özel durumları - tenant'larda da atla
                        if ($moduleBaseName === 'AI' && in_array($className, [
                            'AIPurchaseSeeder', 
                            'AITenantSetupSeeder', 
                            'AIUsageUpdateSeeder',
                            'AIFeatureSeeder_Complete',
                            'AIFeatureSeeder_Master',
                            'AIFeatureSeeder_Part1',
                            'AIFeatureSeeder_Part1_Updated',
                            'AIFeatureSeeder_Part2',
                            'AIFeatureSeeder_Part3',
                            'AISEOFeaturesSeeder',  // Tenant'larda da duplicate slug önlemesi
                            // V3 Universal Input System seeder'ları - sadece central'da çalışır
                            'AIContextRulesSeeder',
                            'AIPromptTemplatesSeeder',
                            'UniversalInputSystemSeeder',
                            // System prompts - sadece central'da çalışır
                            'AISystemPromptsSeeder',
                            'TranslationFeatureSeeder',
                            // Universal Input System V3 seeder'ları 
                            'BlogWriterUniversalInputSeeder',
                            'TranslationUniversalInputSeeder',
                            'UniversalContentLengthPromptsSeeder',
                            'ModernBlogContentSeeder',
                            // SEO AI seeder'ları - tenant'ta AI tabloları yok
                            'SeoAdvancedFeaturesSeeder',
                            'SeoAdvancedInputSystemSeeder',
                            'SeoFeaturesSeeder'
                        ])) {
                            continue;
                        }
                        
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