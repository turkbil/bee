<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AdminTitleHelper 
{
    /**
     * Browser sekmesi için title: "Sayfalar - Sayfa Listesi - Türk Bilişim"
     */
    public static function generateTitle(): string
    {
        try {
            $moduleTitle = self::getModuleTitleFromDB();
            $pretitle = View::shared('pretitle') ?? self::getFallbackPretitle();
            $company = self::getCompanyName();
            
            return "$moduleTitle - $pretitle - $company";
            
        } catch (\Exception $e) {
            return config('app.name', 'Admin Panel');
        }
    }
    
    /**
     * Page header için page title: "Sayfalar" (DB'den)
     */
    public static function generatePageTitle(): string
    {
        try {
            return self::getModuleTitleFromDB();
        } catch (\Exception $e) {
            return 'Yönetim';
        }
    }
    
    /**
     * Page header için pretitle: "Sayfa Listesi" (Blade'den)
     */
    public static function generatePretitle(): string
    {
        return View::shared('pretitle') ?? self::getFallbackPretitle();
    }
    
    /**
     * Mevcut modülün title'ını DB'den çek (JSON destekli)
     */
    private static function getModuleTitleFromDB(): string
    {
        $currentModule = self::getCurrentModule();
        
        if (!$currentModule) {
            return 'Admin';
        }
        
        try {
            // 1. module_tenant_settings.title JSON kontrolü (öncelik)
            $tenantSetting = DB::table('module_tenant_settings')
                ->where('module_name', $currentModule)
                ->first();
            
            if ($tenantSetting && $tenantSetting->title) {
                $titleJson = json_decode($tenantSetting->title, true);
                $currentLang = app()->getLocale(); // tr, en, ar
                
                if (isset($titleJson[$currentLang])) {
                    return $titleJson[$currentLang]; // "Sayfalar"
                }
                
                // Fallback: İlk bulunan dil
                if (!empty($titleJson)) {
                    return array_values($titleJson)[0];
                }
            }
            
            // 2. modules.display_name fallback
            $moduleData = DB::table('modules')
                ->where('name', $currentModule)
                ->first();
                
            if ($moduleData && $moduleData->display_name) {
                return $moduleData->display_name; // "Sayfalar Yönetimi"
            }
            
        } catch (\Exception $e) {
            // Hata olursa devam et
        }
        
        // 3. Final fallback - temiz module adı
        return self::cleanModuleName($currentModule);
    }
    
    /**
     * Mevcut route'dan modül adını çıkar
     */
    private static function getCurrentModule(): ?string
    {
        $route = Route::currentRouteName();
        
        if (!$route || !str_starts_with($route, 'admin.')) {
            return null;
        }
        
        $parts = explode('.', $route);
        return $parts[1] ?? null; // admin.page.index -> "page"
    }
    
    /**
     * Fallback pretitle üret (Blade'de tanımlı değilse)
     */
    private static function getFallbackPretitle(): string
    {
        $route = Route::currentRouteName();
        
        if (!$route || !str_starts_with($route, 'admin.')) {
            return 'Yönetim Paneli';
        }
        
        $parts = explode('.', $route);
        $action = end($parts);
        
        // ID kontrolü (manage/5 durumu için)
        if (is_numeric($action) && count($parts) >= 3) {
            $actualAction = $parts[count($parts) - 2];
            if ($actualAction === 'manage') {
                return 'Düzenleme';
            }
        }
        
        // Basit action mapping
        return match($action) {
            'index' => 'Liste',
            'create' => 'Yeni Ekleme',
            'manage' => 'Yeni Ekleme',
            'edit' => 'Düzenleme',
            'show' => 'Görüntüleme',
            default => 'Yönetim'
        };
    }
    
    /**
     * Module adını temizle
     */
    private static function cleanModuleName(string $module): string
    {
        $cleanName = str_replace(['management', '-', '_'], [' Yönetimi', ' ', ' '], $module);
        return ucfirst(trim($cleanName));
    }
    
    /**
     * Company adını settings'ten çek
     */
    private static function getCompanyName(): string
    {
        try {
            // 1. settings_values tablosunda ID=6'ya bak
            $settingValue = DB::table('settings_values')
                ->where('id', 6)
                ->first();
                
            if ($settingValue && !empty($settingValue->value)) {
                return $settingValue->value;
            }
            
            // 2. settings tablosunda site_name ara
            $generalSetting = DB::table('settings')
                ->whereIn('key', ['site_name', 'company_name'])
                ->first();
                
            if ($generalSetting && !empty($generalSetting->value)) {
                return $generalSetting->value;
            }
            
        } catch (\Exception $e) {
            // Hata olursa devam et
        }
        
        // 3. Final fallback
        return config('app.name', 'Sistem');
    }
}