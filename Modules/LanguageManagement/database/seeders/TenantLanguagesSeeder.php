<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LanguageManagement\app\Models\TenantLanguage;
use App\Helpers\TenantHelpers;

class TenantLanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bu seeder hem central hem tenant'ta çalışabilir
        if (TenantHelpers::isCentral()) {
            $this->command->info('TenantLanguagesSeeder central veritabanında çalışıyor...');
        } else {
            $this->command->info('TenantLanguagesSeeder tenant veritabanında çalışıyor...');
        }
        
        // Mevcut dilleri temizle (fresh start için)
        TenantLanguage::query()->delete();
        
        // Tenant'a göre dil konfigürasyonu
        $tenantSpecificLanguages = $this->getTenantSpecificLanguages();
        
        foreach ($tenantSpecificLanguages as $language) {
            TenantLanguage::create($language);
        }
        
        // is_default kolonunu tenant'ın varsayılan locale'ine göre ayarla
        $this->syncDefaultLanguageColumn();
    }
    
    /**
     * Tenant'a göre özel dil konfigürasyonu
     */
    private function getTenantSpecificLanguages(): array
    {
        // Temel dil tanımları
        $allLanguages = [
            'tr' => [
                'code' => 'tr',
                'name' => 'Turkish',
                'native_name' => 'Türkçe',
                'direction' => 'ltr',
                'flag_icon' => '🇹🇷',
                'is_active' => true,
                'url_prefix_mode' => 'except_default',
                'sort_order' => 1,
            ],
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'flag_icon' => '🇺🇸',
                'is_active' => true,
                'url_prefix_mode' => 'except_default',
                'sort_order' => 2,
            ],
            'ar' => [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'direction' => 'rtl',
                'flag_icon' => '🇸🇦',
                'is_active' => true,
                'url_prefix_mode' => 'except_default',
                'sort_order' => 3,
            ],
        ];
        
        // Tenant'a göre hangi dilleri aktif edecek
        if (TenantHelpers::isCentral()) {
            // Central tenant: Tüm diller (tr, en, ar)
            return array_values($allLanguages);
        }
        
        // Tenant context'inde - tenant bilgisinden domain al
        $currentDomain = null;
        try {
            $tenant = tenant();
            if ($tenant && $tenant->domains->first()) {
                $currentDomain = $tenant->domains->first()->domain;
            }
        } catch (\Exception $e) {
            // Tenant context yoksa tüm dilleri döndür
            return array_values($allLanguages);
        }
        
        // Domain'e göre dil konfigürasyonu - DİNAMİK SİSTEM
        switch ($currentDomain) {
            case 'laravel.test':
                // laravel.test: Central domain - Tüm diller (TR, EN, AR)
                return array_values($allLanguages);
                
            case 'a.test':
                // a.test: EN varsayılan + TR
                return [
                    $allLanguages['en'],  // Varsayılan
                    $allLanguages['tr']
                ];
                
            case 'b.test':
                // b.test: EN + AR (TR yok)
                return [
                    $allLanguages['ar'],  // Varsayılan
                    $allLanguages['en']
                ];
                
            case 'c.test':
                // c.test: Sadece EN
                return [
                    $allLanguages['en']   // Sadece İngilizce
                ];
                
            default:
                // Bilinmeyen tenant'lar: Varsayılan dili + TR (güvenli seçenek)
                $tenantDefaultLocale = $tenant?->tenant_default_locale ?? 'tr';
                
                $languages = [$allLanguages['tr']]; // TR her zaman dahil
                
                if ($tenantDefaultLocale !== 'tr' && isset($allLanguages[$tenantDefaultLocale])) {
                    array_unshift($languages, $allLanguages[$tenantDefaultLocale]); // Varsayılanı başa ekle
                }
                
                return $languages;
        }
    }
    
    /**
     * is_default kolonunu tenant'ın varsayılan locale'ine göre senkronize et
     */
    private function syncDefaultLanguageColumn(): void
    {
        try {
            // Önce tüm is_default'ları false yap
            TenantLanguage::query()->update(['is_default' => false]);
            
            // Tenant default locale'i bul
            $tenant = null;
            if (TenantHelpers::isCentral()) {
                // Central: laravel.test
                $defaultCode = 'tr';
            } else {
                $tenant = tenant();
                $defaultCode = $tenant?->tenant_default_locale ?? 'tr';
            }
            
            // Varsayılan dili true yap
            TenantLanguage::where('code', $defaultCode)
                ->update(['is_default' => true]);
                
        } catch (\Exception $e) {
            $this->command->warn("is_default sync failed: " . $e->getMessage());
        }
    }
}