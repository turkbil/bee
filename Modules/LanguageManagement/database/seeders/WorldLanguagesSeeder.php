<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorldLanguagesSeeder extends Seeder
{
    /**
     * Popüler 50 dünya dili listesi
     * AI çeviri için optimize edilmiş
     */
    private array $worldLanguages = [
        // En popüler 10 dil (varsayılan aktif)
        ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'default_active' => true],
        ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe', 'direction' => 'ltr', 'default_active' => true],
        ['code' => 'es', 'name' => 'İspanyolca', 'native_name' => 'Español', 'direction' => 'ltr', 'default_active' => true],
        ['code' => 'fr', 'name' => 'Fransızca', 'native_name' => 'Français', 'direction' => 'ltr', 'default_active' => true],
        ['code' => 'de', 'name' => 'Almanca', 'native_name' => 'Deutsch', 'direction' => 'ltr', 'default_active' => true],
        ['code' => 'it', 'name' => 'İtalyanca', 'native_name' => 'Italiano', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'pt', 'name' => 'Portekizce', 'native_name' => 'Português', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ru', 'name' => 'Rusça', 'native_name' => 'Русский', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'zh', 'name' => 'Çince', 'native_name' => '中文', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ja', 'name' => 'Japonca', 'native_name' => '日本語', 'direction' => 'ltr', 'default_active' => false],
        
        // Avrupa dilleri
        ['code' => 'nl', 'name' => 'Hollandaca', 'native_name' => 'Nederlands', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'pl', 'name' => 'Lehçe', 'native_name' => 'Polski', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sv', 'name' => 'İsveççe', 'native_name' => 'Svenska', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'no', 'name' => 'Norveççe', 'native_name' => 'Norsk', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'da', 'name' => 'Danca', 'native_name' => 'Dansk', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'fi', 'name' => 'Fince', 'native_name' => 'Suomi', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'cs', 'name' => 'Çekçe', 'native_name' => 'Čeština', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'hu', 'name' => 'Macarca', 'native_name' => 'Magyar', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ro', 'name' => 'Romence', 'native_name' => 'Română', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'el', 'name' => 'Yunanca', 'native_name' => 'Ελληνικά', 'direction' => 'ltr', 'default_active' => false],
        
        // Asya dilleri
        ['code' => 'ko', 'name' => 'Korece', 'native_name' => '한국어', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'th', 'name' => 'Tayca', 'native_name' => 'ไทย', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'vi', 'name' => 'Vietnamca', 'native_name' => 'Tiếng Việt', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'id', 'name' => 'Endonezce', 'native_name' => 'Bahasa Indonesia', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ms', 'name' => 'Malayca', 'native_name' => 'Bahasa Melayu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'hi', 'name' => 'Hintçe', 'native_name' => 'हिन्दी', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bn', 'name' => 'Bengalce', 'native_name' => 'বাংলা', 'direction' => 'ltr', 'default_active' => false],
        
        // Ortadoğu dilleri
        ['code' => 'ar', 'name' => 'Arapça', 'native_name' => 'العربية', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'fa', 'name' => 'Farsça', 'native_name' => 'فارسی', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'he', 'name' => 'İbranice', 'native_name' => 'עברית', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'ur', 'name' => 'Urduca', 'native_name' => 'اردو', 'direction' => 'rtl', 'default_active' => false],
        
        // Slav dilleri
        ['code' => 'uk', 'name' => 'Ukraynaca', 'native_name' => 'Українська', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bg', 'name' => 'Bulgarca', 'native_name' => 'Български', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sr', 'name' => 'Sırpça', 'native_name' => 'Српски', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'hr', 'name' => 'Hırvatça', 'native_name' => 'Hrvatski', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sk', 'name' => 'Slovakça', 'native_name' => 'Slovenčina', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sl', 'name' => 'Slovence', 'native_name' => 'Slovenščina', 'direction' => 'ltr', 'default_active' => false],
        
        // Baltık dilleri
        ['code' => 'lt', 'name' => 'Litvanca', 'native_name' => 'Lietuvių', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'lv', 'name' => 'Letonca', 'native_name' => 'Latviešu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'et', 'name' => 'Estonca', 'native_name' => 'Eesti', 'direction' => 'ltr', 'default_active' => false],
        
        // Diğer önemli diller
        ['code' => 'ca', 'name' => 'Katalanca', 'native_name' => 'Català', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'eu', 'name' => 'Baskça', 'native_name' => 'Euskara', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ga', 'name' => 'İrlandaca', 'native_name' => 'Gaeilge', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'cy', 'name' => 'Galce', 'native_name' => 'Cymraeg', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'is', 'name' => 'İzlandaca', 'native_name' => 'Íslenska', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mt', 'name' => 'Maltaca', 'native_name' => 'Malti', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sq', 'name' => 'Arnavutça', 'native_name' => 'Shqip', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mk', 'name' => 'Makedonca', 'native_name' => 'Македонски', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'hy', 'name' => 'Ermenice', 'native_name' => 'Հայերեն', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ka', 'name' => 'Gürcüce', 'native_name' => 'ქართული', 'direction' => 'ltr', 'default_active' => false],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('WorldLanguagesSeeder başlatıldı');
        
        $addedCount = 0;
        $updatedCount = 0;
        
        // tenant_languages tablosunu manuel kontrol ile güncelle
        foreach ($this->worldLanguages as $index => $langData) {
            // Mevcut kaydı kontrol et
            $existing = DB::table('tenant_languages')
                ->where('code', $langData['code'])
                ->first();
            
            if (!$existing) {
                // Yeni kayıt ekle - DİĞER DİLLER (GIZLI)
                DB::table('tenant_languages')->insert([
                    'code' => $langData['code'],
                    'name' => $langData['name'],
                    'native_name' => $langData['native_name'] ?? $langData['name'],
                    'direction' => $langData['direction'] ?? 'ltr',
                    'is_active' => false, // Hepsi pasif
                    'is_visible' => false, // HEPSİ GİZLİ - diğer diller kategorisi
                    'is_default' => false, // Varsayılan dil değil
                    'sort_order' => $index + 1,
                    'flag_icon' => $this->getFlagEmoji($langData['code']),
                    'url_prefix_mode' => 'except_default',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $addedCount++;
                Log::info("Yeni dil eklendi: {$langData['name']} ({$langData['code']})");
            } else {
                // Mevcut kaydı güncelle (mevcut değerleri koru)
                $updateData = [
                    'name' => $langData['name'],
                    'native_name' => $langData['native_name'] ?? $langData['name'],
                    'direction' => $langData['direction'] ?? 'ltr',
                    'flag_icon' => $this->getFlagEmoji($langData['code']),
                    'updated_at' => now(),
                ];
                
                // MEVCUT DİLLERİ DOKUNMA - sadece eksik alanları güncelle
                if ($existing->is_visible === null) {
                    $updateData['is_visible'] = false; // Yeni eklenenler gizli olsun
                }
                if ($existing->url_prefix_mode === null) {
                    $updateData['url_prefix_mode'] = 'except_default';
                }
                if ($existing->sort_order === null) {
                    $updateData['sort_order'] = $index + 1;
                }
                
                DB::table('tenant_languages')
                    ->where('code', $langData['code'])
                    ->update($updateData);
                
                $updatedCount++;
                Log::info("Mevcut dil güncellendi: {$langData['name']} ({$langData['code']})");
            }
        }
        
        Log::info("WorldLanguagesSeeder tamamlandı - {$addedCount} yeni dil eklendi, {$updatedCount} dil güncellendi");
    }

    /**
     * Dil koduna göre bayrak emoji döndür
     */
    private function getFlagEmoji(string $code): string
    {
        $flags = [
            'tr' => '🇹🇷',
            'en' => '🇬🇧',
            'es' => '🇪🇸',
            'fr' => '🇫🇷',
            'de' => '🇩🇪',
            'it' => '🇮🇹',
            'pt' => '🇵🇹',
            'ru' => '🇷🇺',
            'zh' => '🇨🇳',
            'ja' => '🇯🇵',
            'ko' => '🇰🇷',
            'ar' => '🇸🇦',
            'nl' => '🇳🇱',
            'pl' => '🇵🇱',
            'sv' => '🇸🇪',
            'no' => '🇳🇴',
            'da' => '🇩🇰',
            'fi' => '🇫🇮',
            'cs' => '🇨🇿',
            'hu' => '🇭🇺',
            'ro' => '🇷🇴',
            'el' => '🇬🇷',
            'th' => '🇹🇭',
            'vi' => '🇻🇳',
            'id' => '🇮🇩',
            'ms' => '🇲🇾',
            'hi' => '🇮🇳',
            'bn' => '🇧🇩',
            'fa' => '🇮🇷',
            'he' => '🇮🇱',
            'ur' => '🇵🇰',
            'uk' => '🇺🇦',
            'bg' => '🇧🇬',
            'sr' => '🇷🇸',
            'hr' => '🇭🇷',
            'sk' => '🇸🇰',
            'sl' => '🇸🇮',
            'lt' => '🇱🇹',
            'lv' => '🇱🇻',
            'et' => '🇪🇪',
            'ca' => '🇪🇸', // Katalanca - İspanya bayrağı
            'eu' => '🇪🇸', // Baskça - İspanya bayrağı
            'ga' => '🇮🇪',
            'cy' => '🏴', // Galce - siyah bayrak (complex flag yerine)
            'is' => '🇮🇸',
            'mt' => '🇲🇹',
            'sq' => '🇦🇱',
            'mk' => '🇲🇰',
            'hy' => '🇦🇲',
            'ka' => '🇬🇪',
        ];
        
        return $flags[$code] ?? '🌐';
    }
}