<?php

namespace Modules\LanguageManagement\App\Http\Livewire\Admin;

use Livewire\Component;
use App\Services\TranslationFileManager;
use Illuminate\Support\Facades\Cache;

class TranslationManageComponent extends Component
{
    public $selectedType = 'system'; // system, module, tenant
    public $selectedModule = '';
    public $selectedFile = 'common';
    public $selectedLocale = 'tr';
    public $translations = [];
    public $newKey = '';
    public $newValue = '';
    public $editingKey = '';
    public $editingValue = '';
    public $modules = [];
    public $translationFiles = [];
    public $availableLocales = ['tr', 'en'];

    protected $translationManager;

    public function mount()
    {
        $this->translationManager = app(TranslationFileManager::class);
        $this->loadModules();
        $this->loadTranslationFiles();
        $this->loadTranslations();
    }

    public function loadModules()
    {
        $modulePath = base_path('Modules');
        if (is_dir($modulePath)) {
            $modules = array_filter(scandir($modulePath), function ($item) use ($modulePath) {
                return is_dir($modulePath . '/' . $item) && !in_array($item, ['.', '..']);
            });
            $this->modules = array_values($modules);
        }
    }

    public function loadTranslationFiles()
    {
        switch ($this->selectedType) {
            case 'system':
                $langPath = base_path("lang/{$this->selectedLocale}");
                break;
            case 'module':
                if (!$this->selectedModule) {
                    $this->translationFiles = [];
                    return;
                }
                $langPath = base_path("Modules/{$this->selectedModule}/lang/{$this->selectedLocale}");
                break;
            case 'tenant':
                if (!function_exists('tenant') || !tenant()) {
                    $this->translationFiles = [];
                    return;
                }
                $langPath = storage_path("app/tenants/" . tenant()->id . "/lang/{$this->selectedLocale}");
                break;
            default:
                $this->translationFiles = [];
                return;
        }

        if (is_dir($langPath)) {
            $files = array_filter(scandir($langPath), function ($item) use ($langPath) {
                return pathinfo($item, PATHINFO_EXTENSION) === 'php';
            });
            $this->translationFiles = array_map(function ($file) {
                return pathinfo($file, PATHINFO_FILENAME);
            }, array_values($files));
        } else {
            $this->translationFiles = [];
        }
    }

    public function loadTranslations()
    {
        try {
            switch ($this->selectedType) {
                case 'system':
                    $this->translations = $this->translationManager->getSystemTranslations($this->selectedLocale);
                    if (isset($this->translations[$this->selectedFile])) {
                        $this->translations = $this->translations[$this->selectedFile];
                    } else {
                        $this->translations = [];
                    }
                    break;
                case 'module':
                    if ($this->selectedModule) {
                        $moduleTranslations = $this->translationManager->getModuleTranslations($this->selectedModule, $this->selectedLocale);
                        if (isset($moduleTranslations[$this->selectedFile])) {
                            $this->translations = $moduleTranslations[$this->selectedFile];
                        } else {
                            $this->translations = [];
                        }
                    }
                    break;
                case 'tenant':
                    if (function_exists('tenant') && tenant()) {
                        $tenantTranslations = $this->translationManager->getTenantTranslations(tenant()->id, $this->selectedLocale);
                        if (isset($tenantTranslations[$this->selectedFile])) {
                            $this->translations = $tenantTranslations[$this->selectedFile];
                        } else {
                            $this->translations = [];
                        }
                    }
                    break;
            }
        } catch (\Exception $e) {
            $this->translations = [];
            session()->flash('error', 'Çeviriler yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function updatedSelectedType()
    {
        $this->reset(['selectedModule', 'selectedFile', 'translations']);
        $this->selectedFile = 'common';
        $this->loadTranslationFiles();
        $this->loadTranslations();
    }

    public function updatedSelectedModule()
    {
        $this->reset(['selectedFile', 'translations']);
        $this->selectedFile = 'general';
        $this->loadTranslationFiles();
        $this->loadTranslations();
    }

    public function updatedSelectedFile()
    {
        $this->loadTranslations();
    }

    public function updatedSelectedLocale()
    {
        $this->loadTranslationFiles();
        $this->loadTranslations();
    }

    public function addTranslation()
    {
        $this->validate([
            'newKey' => 'required|string',
            'newValue' => 'required|string',
        ], [
            'newKey.required' => 'Anahtar zorunludur',
            'newValue.required' => 'Değer zorunludur',
        ]);

        if (isset($this->translations[$this->newKey])) {
            session()->flash('error', 'Bu anahtar zaten mevcut');
            return;
        }

        $this->translations[$this->newKey] = $this->newValue;
        $this->saveTranslations();
        
        $this->reset(['newKey', 'newValue']);
        session()->flash('message', 'Çeviri başarıyla eklendi');
    }

    public function startEdit($key)
    {
        $this->editingKey = $key;
        $this->editingValue = $this->translations[$key];
    }

    public function cancelEdit()
    {
        $this->reset(['editingKey', 'editingValue']);
    }

    public function saveEdit()
    {
        $this->validate([
            'editingValue' => 'required|string',
        ], [
            'editingValue.required' => 'Değer zorunludur',
        ]);

        $this->translations[$this->editingKey] = $this->editingValue;
        $this->saveTranslations();
        
        $this->reset(['editingKey', 'editingValue']);
        session()->flash('message', 'Çeviri başarıyla güncellendi');
    }

    public function deleteTranslation($key)
    {
        unset($this->translations[$key]);
        $this->saveTranslations();
        session()->flash('message', 'Çeviri başarıyla silindi');
    }

    private function saveTranslations()
    {
        try {
            switch ($this->selectedType) {
                case 'system':
                    $filePath = base_path("lang/{$this->selectedLocale}/{$this->selectedFile}.php");
                    $this->writeTranslationFile($filePath, $this->translations);
                    break;
                case 'module':
                    if ($this->selectedModule) {
                        $filePath = base_path("Modules/{$this->selectedModule}/lang/{$this->selectedLocale}/{$this->selectedFile}.php");
                        $this->writeTranslationFile($filePath, $this->translations);
                    }
                    break;
                case 'tenant':
                    if (function_exists('tenant') && tenant()) {
                        $this->translationManager->updateTenantTranslation(
                            tenant()->id,
                            $this->selectedLocale,
                            $this->selectedFile,
                            $this->translations
                        );
                    }
                    break;
            }

            // Cache'i temizle
            $this->clearTranslationCache();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Dosya kaydedilirken hata oluştu: ' . $e->getMessage());
        }
    }

    private function writeTranslationFile($filePath, $translations)
    {
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
        file_put_contents($filePath, $content);
    }

    private function clearTranslationCache()
    {
        switch ($this->selectedType) {
            case 'system':
                Cache::forget("translations.system.{$this->selectedLocale}");
                break;
            case 'module':
                if ($this->selectedModule) {
                    Cache::forget("translations.module.{$this->selectedModule}.{$this->selectedLocale}");
                }
                break;
            case 'tenant':
                if (function_exists('tenant') && tenant()) {
                    Cache::forget("translations.tenant." . tenant()->id . ".{$this->selectedLocale}");
                }
                break;
        }
    }

    public function createNewFile()
    {
        $this->validate([
            'newKey' => 'required|string|regex:/^[a-zA-Z0-9_-]+$/',
        ], [
            'newKey.required' => 'Dosya adı zorunludur',
            'newKey.regex' => 'Dosya adı sadece harf, rakam, tire ve alt çizgi içerebilir',
        ]);

        try {
            switch ($this->selectedType) {
                case 'system':
                    $filePath = base_path("lang/{$this->selectedLocale}/{$this->newKey}.php");
                    break;
                case 'module':
                    if (!$this->selectedModule) {
                        session()->flash('error', 'Modül seçilmeli');
                        return;
                    }
                    $filePath = base_path("Modules/{$this->selectedModule}/lang/{$this->selectedLocale}/{$this->newKey}.php");
                    break;
                case 'tenant':
                    if (!function_exists('tenant') || !tenant()) {
                        session()->flash('error', 'Tenant bulunamadı');
                        return;
                    }
                    $filePath = storage_path("app/tenants/" . tenant()->id . "/lang/{$this->selectedLocale}/{$this->newKey}.php");
                    break;
                default:
                    session()->flash('error', 'Geçersiz tip');
                    return;
            }

            if (file_exists($filePath)) {
                session()->flash('error', 'Bu dosya zaten mevcut');
                return;
            }

            $this->writeTranslationFile($filePath, []);
            $this->selectedFile = $this->newKey;
            $this->reset(['newKey']);
            $this->loadTranslationFiles();
            $this->loadTranslations();
            
            session()->flash('message', 'Yeni çeviri dosyası oluşturuldu');
        } catch (\Exception $e) {
            session()->flash('error', 'Dosya oluşturulurken hata: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('languagemanagement::admin.livewire.translation-manage-component')
            ->layout('layouts.admin');
    }
}