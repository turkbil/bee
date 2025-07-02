<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CheckMissingTranslations extends Command
{
    protected $signature = 'translations:check {module?} {--fix : Automatically add missing keys}';
    protected $description = 'AI modülü ve diğer modüllerdeki eksik çevirileri tespit eder';

    public function handle()
    {
        $module = $this->argument('module');
        $autoFix = $this->option('fix');

        if ($module) {
            // Tek modül analizi
            $this->info("🔍 {$module} modülü dil analizi başlatılıyor...");
            $results = $this->analyzeModule($module);
            
            if (empty($results['missing'])) {
                $this->info("✅ {$module} modülünde eksik çeviri bulunamadı!");
                return 0;
            }

            $this->displayResults($results, $module);
            
            if ($autoFix) {
                $this->fixMissingTranslations($module, $results['missing']);
            }
        } else {
            // Tüm modülleri analiz et
            $this->info("🔍 Tüm modüller dil analizi başlatılıyor...");
            $this->analyzeAllModules($autoFix);
        }
        
        return 0;
    }

    private function analyzeAllModules($autoFix = false)
    {
        $modulesPath = base_path('Modules');
        
        if (!File::exists($modulesPath)) {
            $this->error("❌ Modules dizini bulunamadı!");
            return;
        }

        $modules = File::directories($modulesPath);
        $allResults = [];
        $totalMissing = 0;

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            
            $this->line("\n📂 {$moduleName} modülü taranıyor...");
            
            $results = $this->analyzeModule($moduleName);
            
            if (!empty($results['missing'])) {
                $allResults[$moduleName] = $results;
                
                $moduleTotal = 0;
                foreach ($results['missing'] as $locale => $contexts) {
                    foreach ($contexts as $context => $keys) {
                        $moduleTotal += count($keys);
                    }
                }
                
                $totalMissing += $moduleTotal;
                $this->line("  ❌ {$moduleTotal} eksik çeviri tespit edildi");
                
                if ($autoFix) {
                    $this->fixMissingTranslations($moduleName, $results['missing']);
                    $this->line("  🔧 Eksiklikler otomatik düzeltildi");
                }
            } else {
                $this->line("  ✅ Eksik çeviri yok");
            }
        }

        $this->displaySummary($allResults, $totalMissing);
    }

    private function displaySummary($allResults, $totalMissing)
    {
        $this->line("\n" . str_repeat("=", 60));
        $this->info("📊 GENEL ÖZET");
        $this->line(str_repeat("=", 60));
        
        if ($totalMissing === 0) {
            $this->info("🎉 Tüm modüllerde çeviriler eksiksiz!");
            return;
        }

        $this->line("📈 Toplam eksik çeviri: {$totalMissing}");
        $this->line("🔍 Sorunlu modül sayısı: " . count($allResults));
        
        $this->line("\n🎯 Modül bazında detaylar:");
        
        foreach ($allResults as $moduleName => $results) {
            $moduleTotal = 0;
            foreach ($results['missing'] as $locale => $contexts) {
                foreach ($contexts as $context => $keys) {
                    $moduleTotal += count($keys);
                }
            }
            
            $this->line("  📦 {$moduleName}: {$moduleTotal} eksik");
            
            // En kritik eksiklikleri göster
            foreach (['tr', 'en'] as $locale) {
                foreach (['admin', 'front'] as $context) {
                    $missing = $results['missing'][$locale][$context] ?? [];
                    if (!empty($missing)) {
                        $count = count($missing);
                        $this->line("    🌐 {$locale} {$context}: {$count} eksik");
                        
                        // İlk 3 eksikliği göster
                        $sample = array_slice(array_keys($missing), 0, 3);
                        foreach ($sample as $key) {
                            $this->line("      • {$key}");
                        }
                        
                        if (count($missing) > 3) {
                            $remaining = count($missing) - 3;
                            $this->line("      ... ve {$remaining} eksik daha");
                        }
                    }
                }
            }
        }
        
        $this->line("\n💡 Tüm eksiklikleri otomatik düzeltmek için:");
        $this->comment("   php artisan translations:check --fix");
    }

    private function analyzeModule($module)
    {
        $modulePath = base_path("Modules/{$module}");
        
        if (!File::exists($modulePath)) {
            $this->error("❌ {$module} modülü bulunamadı!");
            return ['missing' => []];
        }

        // Blade ve PHP dosyalarını tara
        $usedKeys = $this->scanForTranslationKeys($modulePath);
        
        // Mevcut çeviri dosyalarını oku
        $trKeys = $this->loadTranslationKeys($module, 'tr');
        $enKeys = $this->loadTranslationKeys($module, 'en');
        
        // Eksik çevirileri tespit et
        $missing = $this->findMissingKeys($usedKeys, $trKeys, $enKeys);
        
        return [
            'used' => $usedKeys,
            'tr_keys' => $trKeys,
            'en_keys' => $enKeys,
            'missing' => $missing
        ];
    }

    private function scanForTranslationKeys($modulePath)
    {
        $keys = [];
        $pattern = "/__\(['\"]({$this->getModuleName($modulePath)})::(admin|front)\.([^'\"]+)['\"]\)/";
        
        $files = File::allFiles($modulePath);
        
        foreach ($files as $file) {
            if (!Str::endsWith($file->getExtension(), ['php', 'blade.php'])) {
                continue;
            }
            
            $content = File::get($file->getPathname());
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $fullKey = $match[1] . '::' . $match[2] . '.' . $match[3];
                $context = $match[2]; // admin or front
                $key = $match[3];
                
                if (!isset($keys[$context])) {
                    $keys[$context] = [];
                }
                
                $keys[$context][$key] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'full_key' => $fullKey
                ];
            }
        }
        
        return $keys;
    }

    private function getModuleName($modulePath)
    {
        return strtolower(basename($modulePath));
    }

    private function loadTranslationKeys($module, $locale)
    {
        $adminFile = base_path("Modules/{$module}/lang/{$locale}/admin.php");
        $frontFile = base_path("Modules/{$module}/lang/{$locale}/front.php");
        
        $keys = [];
        
        if (File::exists($adminFile)) {
            $keys['admin'] = $this->flattenArray(include $adminFile);
        }
        
        if (File::exists($frontFile)) {
            $keys['front'] = $this->flattenArray(include $frontFile);
        }
        
        return $keys;
    }

    private function flattenArray($array, $prefix = '')
    {
        $flattened = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $flattened = array_merge($flattened, $this->flattenArray($value, $newKey));
            } else {
                $flattened[$newKey] = $value;
            }
        }
        
        return $flattened;
    }

    private function findMissingKeys($usedKeys, $trKeys, $enKeys)
    {
        $missing = [
            'tr' => ['admin' => [], 'front' => []],
            'en' => ['admin' => [], 'front' => []]
        ];
        
        foreach (['admin', 'front'] as $context) {
            if (!isset($usedKeys[$context])) continue;
            
            foreach ($usedKeys[$context] as $key => $info) {
                // TR kontrolü
                if (!isset($trKeys[$context][$key])) {
                    $missing['tr'][$context][$key] = $info;
                }
                
                // EN kontrolü
                if (!isset($enKeys[$context][$key])) {
                    $missing['en'][$context][$key] = $info;
                }
            }
        }
        
        return $missing;
    }

    private function displayResults($results, $moduleName = null)
    {
        $this->info("\n📊 Analiz Sonuçları:");
        
        foreach (['tr', 'en'] as $locale) {
            $this->line("\n🌐 {$locale} dili:");
            
            foreach (['admin', 'front'] as $context) {
                $missing = $results['missing'][$locale][$context] ?? [];
                
                if (empty($missing)) {
                    $this->info("  ✅ {$context}: Eksik çeviri yok");
                    continue;
                }
                
                $this->error("  ❌ {$context}: " . count($missing) . " eksik çeviri");
                
                foreach ($missing as $key => $info) {
                    $this->line("    • {$key}");
                    $this->line("      🔗 {$info['file']}");
                    $this->line("      📝 {$info['full_key']}");
                }
            }
        }
        
        $totalMissing = 0;
        foreach ($results['missing'] as $locale => $contexts) {
            foreach ($contexts as $context => $keys) {
                $totalMissing += count($keys);
            }
        }
        
        $this->line("\n📈 Toplam eksik çeviri: {$totalMissing}");
        
        if ($totalMissing > 0) {
            $this->line("💡 Eksik çevirileri otomatik eklemek için: --fix parametresini kullanın");
        }
    }

    private function fixMissingTranslations($module, $missing)
    {
        $this->info("\n🔧 Eksik çeviriler otomatik ekleniyor...");
        
        foreach (['tr', 'en'] as $locale) {
            foreach (['admin', 'front'] as $context) {
                $keys = $missing[$locale][$context] ?? [];
                
                if (empty($keys)) continue;
                
                $this->addKeysToFile($module, $locale, $context, $keys);
            }
        }
        
        $this->info("✅ Eksik çeviriler eklendi!");
    }

    private function addKeysToFile($module, $locale, $context, $keys)
    {
        $filePath = base_path("Modules/{$module}/lang/{$locale}/{$context}.php");
        
        if (!File::exists($filePath)) {
            $this->warn("📄 {$filePath} dosyası bulunamadı, oluşturuluyor...");
            $this->createTranslationFile($filePath);
        }
        
        $translations = include $filePath;
        
        foreach ($keys as $key => $info) {
            $this->setNestedKey($translations, $key, $this->generateTranslation($key, $locale));
        }
        
        $this->writeTranslationFile($filePath, $translations);
        $this->line("  📝 {$context}.php güncelledin (" . count($keys) . " anahtar)");
    }

    private function setNestedKey(&$array, $key, $value)
    {
        $keys = explode('.', $key);
        $current = &$array;
        
        foreach ($keys as $k) {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;
    }

    private function generateTranslation($key, $locale)
    {
        $translations = [
            'tr' => [
                'name' => 'Ad',
                'title' => 'Başlık',
                'description' => 'Açıklama',
                'actions' => 'İşlemler',
                'edit' => 'Düzenle',
                'delete' => 'Sil',
                'save' => 'Kaydet',
                'cancel' => 'İptal',
                'status' => 'Durum',
                'active' => 'Aktif',
                'inactive' => 'Pasif',
                'loading' => 'Yükleniyor...',
                'search' => 'Ara',
                'filters' => 'Filtreler',
                'clear_filters' => 'Filtreleri Temizle',
            ],
            'en' => [
                'name' => 'Name',
                'title' => 'Title',
                'description' => 'Description',
                'actions' => 'Actions',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'save' => 'Save',
                'cancel' => 'Cancel',
                'status' => 'Status',
                'active' => 'Active',
                'inactive' => 'Inactive',
                'loading' => 'Loading...',
                'search' => 'Search',
                'filters' => 'Filters',
                'clear_filters' => 'Clear Filters',
            ]
        ];
        
        // Basit anahtar varsa direkt döndür
        if (isset($translations[$locale][$key])) {
            return $translations[$locale][$key];
        }
        
        // Anahtar parçalarından tahmin et
        $lastPart = Str::afterLast($key, '.');
        if (isset($translations[$locale][$lastPart])) {
            return $translations[$locale][$lastPart];
        }
        
        // Manuel çeviri gerektiğini belirt
        return $locale === 'tr' 
            ? "TODO: '{$key}' çevirisini ekleyin"
            : "TODO: Add translation for '{$key}'";
    }

    private function createTranslationFile($filePath)
    {
        $directory = dirname($filePath);
        
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        $content = "<?php\n\nreturn [\n    // TODO: Çevirileri ekleyin\n];\n";
        File::put($filePath, $content);
    }

    private function writeTranslationFile($filePath, $translations)
    {
        $content = "<?php\n\nreturn " . $this->arrayToString($translations, 0) . ";\n";
        File::put($filePath, $content);
    }

    private function arrayToString($array, $depth = 0)
    {
        $indent = str_repeat('    ', $depth);
        $items = [];
        
        foreach ($array as $key => $value) {
            $keyStr = is_string($key) ? "'$key'" : $key;
            
            if (is_array($value)) {
                $valueStr = $this->arrayToString($value, $depth + 1);
                $items[] = "$indent$keyStr => $valueStr";
            } else {
                $valueStr = is_string($value) ? "'" . addslashes($value) . "'" : $value;
                $items[] = "$indent$keyStr => $valueStr";
            }
        }
        
        return "[\n" . implode(",\n", $items) . "\n$indent]";
    }
}