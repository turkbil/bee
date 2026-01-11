<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

#[Layout('admin.layout')]
class TranslationCheckerComponent extends Component
{
    public $selectedModules = [];
    public $results = [];
    public $isLoading = false;
    public $scanType = 'all'; // 'all', 'selected'
    public $totalMissing = 0;
    public $showDetails = [];

    public function mount()
    {
        // Tüm modülleri başlangıçta seçili yap
        $modules = Module::allEnabled();
        $this->selectedModules = array_keys($modules);
    }

    public function render()
    {
        $modules = Module::allEnabled();
        
        return view('languagemanagement::admin.livewire.translation-checker-component', [
            'modules' => $modules
        ]);
    }

    public function checkTranslations()
    {
        $this->isLoading = true;
        $this->results = [];
        $this->totalMissing = 0;

        try {
            if ($this->scanType === 'all') {
                $this->results = $this->analyzeAllModules();
            } else {
                $this->results = $this->analyzeSelectedModules();
            }

            // Toplam eksik sayısını hesapla
            foreach ($this->results as $moduleResults) {
                if (isset($moduleResults['missing'])) {
                    foreach ($moduleResults['missing'] as $locale => $contexts) {
                        foreach ($contexts as $context => $keys) {
                            $this->totalMissing += count($keys);
                        }
                    }
                }
            }

            $this->dispatch('show-toast', [
                'message' => $this->totalMissing === 0 
                    ? __('languagemanagement::admin.translation_check_complete_no_issues')
                    : __('languagemanagement::admin.translation_check_complete_with_issues', ['count' => $this->totalMissing]),
                'type' => $this->totalMissing === 0 ? 'success' : 'warning'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'message' => __('languagemanagement::admin.translation_check_error', ['error' => $e->getMessage()]),
                'type' => 'error'
            ]);
        }

        $this->isLoading = false;
    }

    public function fixTranslations($moduleName = null)
    {
        $this->isLoading = true;

        try {
            if ($moduleName) {
                // Tek modül düzelt
                $results = $this->analyzeModule($moduleName);
                if (!empty($results['missing'])) {
                    $this->fixMissingTranslations($moduleName, $results['missing']);
                    // Sonuçları güncelle
                    $this->results[$moduleName] = $this->analyzeModule($moduleName);
                }
            } else {
                // Tüm modülleri düzelt
                foreach ($this->results as $moduleName => $moduleResults) {
                    if (!empty($moduleResults['missing'])) {
                        $this->fixMissingTranslations($moduleName, $moduleResults['missing']);
                        $this->results[$moduleName] = $this->analyzeModule($moduleName);
                    }
                }
            }

            // Toplam eksik sayısını yeniden hesapla
            $this->totalMissing = 0;
            foreach ($this->results as $moduleResults) {
                if (isset($moduleResults['missing'])) {
                    foreach ($moduleResults['missing'] as $locale => $contexts) {
                        foreach ($contexts as $context => $keys) {
                            $this->totalMissing += count($keys);
                        }
                    }
                }
            }

            $this->dispatch('show-toast', [
                'message' => __('languagemanagement::admin.translations_fixed_successfully'),
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'message' => __('languagemanagement::admin.fix_translations_error', ['error' => $e->getMessage()]),
                'type' => 'error'
            ]);
        }

        $this->isLoading = false;
    }

    public function toggleDetails($moduleName)
    {
        if (in_array($moduleName, $this->showDetails)) {
            $this->showDetails = array_diff($this->showDetails, [$moduleName]);
        } else {
            $this->showDetails[] = $moduleName;
        }
    }

    private function analyzeAllModules()
    {
        $results = [];
        $modules = Module::allEnabled();

        foreach ($modules as $moduleName => $module) {
            $results[$moduleName] = $this->analyzeModule($moduleName);
        }

        return $results;
    }

    private function analyzeSelectedModules()
    {
        $results = [];

        foreach ($this->selectedModules as $moduleName) {
            $results[$moduleName] = $this->analyzeModule($moduleName);
        }

        return $results;
    }

    private function analyzeModule($module)
    {
        $modulePath = base_path("Modules/{$module}");
        
        if (!File::exists($modulePath)) {
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
        $pattern = "/__\(['\"](" . strtolower(basename($modulePath)) . ")::(admin|front)\.([^'\"]+)['\"]\)/";
        
        $files = File::allFiles($modulePath);
        
        foreach ($files as $file) {
            if (!Str::endsWith($file->getExtension(), ['php'])) {
                continue;
            }
            
            $content = File::get($file->getPathname());
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $context = $match[2]; // admin or front
                $key = $match[3];
                
                if (!isset($keys[$context])) {
                    $keys[$context] = [];
                }
                
                $keys[$context][$key] = [
                    'file' => str_replace(base_path(), '', $file->getPathname()),
                    'full_key' => $match[1] . '::' . $match[2] . '.' . $match[3]
                ];
            }
        }
        
        return $keys;
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

    private function fixMissingTranslations($module, $missing)
    {
        foreach (['tr', 'en'] as $locale) {
            foreach (['admin', 'front'] as $context) {
                $keys = $missing[$locale][$context] ?? [];
                
                if (empty($keys)) continue;
                
                $this->addKeysToFile($module, $locale, $context, $keys);
            }
        }
    }

    private function addKeysToFile($module, $locale, $context, $keys)
    {
        $filePath = base_path("Modules/{$module}/lang/{$locale}/{$context}.php");
        
        if (!File::exists($filePath)) {
            $this->createTranslationFile($filePath);
        }
        
        $translations = include $filePath;
        
        foreach ($keys as $key => $info) {
            $this->setNestedKey($translations, $key, $this->generateTranslation($key, $locale));
        }
        
        $this->writeTranslationFile($filePath, $translations);
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
            ? "Eksik çeviri: '{$key}'"
            : "Missing translation: '{$key}'";
    }

    private function createTranslationFile($filePath)
    {
        $directory = dirname($filePath);
        
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        $content = "<?php\n\nreturn [\n    // Çeviriler buraya eklenecek\n];\n";
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