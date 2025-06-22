<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TranslationFileManager
{
    protected $systemLangPath;
    protected $tenantStoragePath;
    
    public function __construct()
    {
        $this->systemLangPath = lang_path();
        $this->tenantStoragePath = storage_path('app/tenants');
    }

    /**
     * Sistem dili dosyalarını getir
     */
    public function getSystemTranslations(string $locale): array
    {
        $cacheKey = "system_translations_{$locale}";
        
        return Cache::remember($cacheKey, 3600, function() use ($locale) {
            $langPath = $this->systemLangPath . "/{$locale}";
            
            if (!File::exists($langPath)) {
                return [];
            }
            
            $translations = [];
            $files = File::files($langPath);
            
            foreach ($files as $file) {
                $filename = $file->getFilenameWithoutExtension();
                $filePath = $file->getPathname();
                
                if ($file->getExtension() === 'php') {
                    $content = include $filePath;
                    if (is_array($content)) {
                        $translations[$filename] = $content;
                    }
                }
            }
            
            return $translations;
        });
    }

    /**
     * Modül dili dosyalarını getir
     */
    public function getModuleTranslations(string $module, string $locale): array
    {
        $cacheKey = "module_translations_{$module}_{$locale}";
        
        return Cache::remember($cacheKey, 3600, function() use ($module, $locale) {
            $modulePath = base_path("Modules/{$module}/lang/{$locale}");
            
            if (!File::exists($modulePath)) {
                return [];
            }
            
            $translations = [];
            $files = File::files($modulePath);
            
            foreach ($files as $file) {
                $filename = $file->getFilenameWithoutExtension();
                $filePath = $file->getPathname();
                
                if ($file->getExtension() === 'php') {
                    $content = include $filePath;
                    if (is_array($content)) {
                        $translations[$filename] = $content;
                    }
                }
            }
            
            return $translations;
        });
    }

    /**
     * Tenant dili dosyalarını getir
     */
    public function getTenantTranslations(string $tenantId, string $locale): array
    {
        $cacheKey = "tenant_translations_{$tenantId}_{$locale}";
        
        return Cache::remember($cacheKey, 1800, function() use ($tenantId, $locale) {
            $tenantLangPath = $this->tenantStoragePath . "/{$tenantId}/lang/{$locale}";
            
            if (!File::exists($tenantLangPath)) {
                return [];
            }
            
            $translations = [];
            $files = File::files($tenantLangPath);
            
            foreach ($files as $file) {
                $filename = $file->getFilenameWithoutExtension();
                $filePath = $file->getPathname();
                
                if ($file->getExtension() === 'php') {
                    $content = include $filePath;
                    if (is_array($content)) {
                        $translations[$filename] = $content;
                    }
                }
            }
            
            return $translations;
        });
    }

    /**
     * Tenant çeviri dosyasını güncelle
     */
    public function updateTenantTranslation(string $tenantId, string $locale, string $file, array $translations): bool
    {
        try {
            $tenantLangPath = $this->tenantStoragePath . "/{$tenantId}/lang/{$locale}";
            $filePath = $tenantLangPath . "/{$file}.php";
            
            // Klasör yoksa oluştur
            if (!File::exists($tenantLangPath)) {
                File::makeDirectory($tenantLangPath, 0755, true);
            }
            
            // Dosya içeriğini hazırla
            $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
            
            // Dosyayı yaz
            File::put($filePath, $content);
            
            // Cache'i temizle
            $cacheKey = "tenant_translations_{$tenantId}_{$locale}";
            Cache::forget($cacheKey);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Tenant translation update failed', [
                'tenant_id' => $tenantId,
                'locale' => $locale,
                'file' => $file,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Yeni dil dosyaları oluştur
     */
    public function createLanguageFiles(string $locale, array $modules = []): bool
    {
        try {
            // 1. Sistem dil dosyalarını oluştur
            $this->createSystemLanguageFiles($locale);
            
            // 2. Modül dil dosyalarını oluştur
            foreach ($modules as $module) {
                $this->createModuleLanguageFiles($module, $locale);
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Language files creation failed', [
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Sistem dil dosyalarını oluştur
     */
    protected function createSystemLanguageFiles(string $locale): void
    {
        $langPath = $this->systemLangPath . "/{$locale}";
        
        if (!File::exists($langPath)) {
            File::makeDirectory($langPath, 0755, true);
        }
        
        // Temel sistem dosyaları
        $systemFiles = [
            'common' => [
                'save' => 'Kaydet',
                'cancel' => 'İptal',
                'delete' => 'Sil',
                'edit' => 'Düzenle',
                'create' => 'Oluştur',
                'update' => 'Güncelle',
                'back' => 'Geri',
                'yes' => 'Evet',
                'no' => 'Hayır',
                'active' => 'Aktif',
                'inactive' => 'Pasif',
                'enabled' => 'Etkin',
                'disabled' => 'Devre Dışı',
            ],
            'admin' => [
                'dashboard' => 'Kontrol Paneli',
                'settings' => 'Ayarlar',
                'users' => 'Kullanıcılar',
                'profile' => 'Profil',
                'logout' => 'Çıkış',
                'welcome' => 'Hoş Geldiniz',
            ],
            'errors' => [
                '404' => 'Sayfa Bulunamadı',
                '403' => 'Erişim Reddedildi',
                '500' => 'Sunucu Hatası',
                'not_found' => 'Kayıt bulunamadı',
                'access_denied' => 'Bu işlem için yetkiniz yok',
            ]
        ];
        
        foreach ($systemFiles as $fileName => $content) {
            $filePath = $langPath . "/{$fileName}.php";
            if (!File::exists($filePath)) {
                $fileContent = "<?php\n\nreturn " . var_export($content, true) . ";\n";
                File::put($filePath, $fileContent);
            }
        }
    }

    /**
     * Modül dil dosyalarını oluştur
     */
    protected function createModuleLanguageFiles(string $module, string $locale): void
    {
        $moduleLangPath = base_path("Modules/{$module}/lang/{$locale}");
        
        if (!File::exists($moduleLangPath)) {
            File::makeDirectory($moduleLangPath, 0755, true);
        }
        
        // Temel modül dosyaları
        $moduleFiles = [
            'general' => [
                'title' => Str::title($module),
                'description' => $module . ' modülü',
                'list' => $module . ' Listesi',
                'create' => 'Yeni ' . $module,
                'edit' => $module . ' Düzenle',
            ],
            'messages' => [
                'created' => $module . ' başarıyla oluşturuldu',
                'updated' => $module . ' başarıyla güncellendi',
                'deleted' => $module . ' başarıyla silindi',
                'not_found' => $module . ' bulunamadı',
            ]
        ];
        
        foreach ($moduleFiles as $fileName => $content) {
            $filePath = $moduleLangPath . "/{$fileName}.php";
            if (!File::exists($filePath)) {
                $fileContent = "<?php\n\nreturn " . var_export($content, true) . ";\n";
                File::put($filePath, $fileContent);
            }
        }
    }

    /**
     * Tüm cache'leri temizle
     */
    public function clearAllTranslationCache(): void
    {
        $keys = [
            'system_translations_*',
            'module_translations_*',
            'tenant_translations_*'
        ];
        
        foreach ($keys as $pattern) {
            Cache::forget($pattern);
        }
        
        // Laravel'in translation cache'ini de temizle
        if (function_exists('artisan')) {
            \Artisan::call('cache:clear');
        }
    }

    /**
     * Belirli tenant'ın translation cache'ini temizle
     */
    public function clearTenantTranslationCache(string $tenantId): void
    {
        $pattern = "tenant_translations_{$tenantId}_*";
        Cache::forget($pattern);
    }

    /**
     * Modül translation cache'ini temizle
     */
    public function clearModuleTranslationCache(string $module): void
    {
        $pattern = "module_translations_{$module}_*";
        Cache::forget($pattern);
    }
}