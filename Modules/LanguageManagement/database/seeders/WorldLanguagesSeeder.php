<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorldLanguagesSeeder extends Seeder
{
    /**
     * 🎯 GÜÇLÜ AI DESTEKLİ DİLLER - Ana kategorideki diller
     * Bu diller OpenAI, Anthropic, Gemini gibi büyük modellerde MÜKEMMEL çeviri kalitesi sağlar
     * is_main_language = true → Modal'da uyarı yok, direkt çeviri
     */
    private array $strongAiLanguages = [
        // Tier 1: ULTRA GÜÇLÜ - Görünür diller (varsayılan aktif)
        ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'is_visible' => true, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe', 'direction' => 'ltr', 'is_visible' => true, 'is_active' => false, 'ai_quality' => 'excellent'],
        
        // Tier 1: ULTRA GÜÇLÜ - Gizli ama mükemmel AI desteği
        ['code' => 'es', 'name' => 'İspanyolca', 'native_name' => 'Español', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'fr', 'name' => 'Fransızca', 'native_name' => 'Français', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'de', 'name' => 'Almanca', 'native_name' => 'Deutsch', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'it', 'name' => 'İtalyanca', 'native_name' => 'Italiano', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'pt', 'name' => 'Portekizce', 'native_name' => 'Português', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'ru', 'name' => 'Rusça', 'native_name' => 'Русский', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'zh', 'name' => 'Çince', 'native_name' => '中文', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'ja', 'name' => 'Japonca', 'native_name' => '日本語', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        ['code' => 'ar', 'name' => 'Arapça', 'native_name' => 'العربية', 'direction' => 'rtl', 'is_visible' => true, 'is_active' => true, 'ai_quality' => 'excellent'],
        ['code' => 'ko', 'name' => 'Korece', 'native_name' => '한국어', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'excellent'],
        
        // Tier 2: ÇOK GÜÇLÜ - Avrupa dilleri
        ['code' => 'nl', 'name' => 'Hollandaca', 'native_name' => 'Nederlands', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'very_good'],
        ['code' => 'pl', 'name' => 'Lehçe', 'native_name' => 'Polski', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'very_good'],
        ['code' => 'sv', 'name' => 'İsveççe', 'native_name' => 'Svenska', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'very_good'],
        ['code' => 'no', 'name' => 'Norveççe', 'native_name' => 'Norsk', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'very_good'],
        ['code' => 'da', 'name' => 'Danca', 'native_name' => 'Dansk', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'very_good'],
        ['code' => 'fi', 'name' => 'Fince', 'native_name' => 'Suomi', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'very_good'],
        ['code' => 'cs', 'name' => 'Çekçe', 'native_name' => 'Čeština', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'hu', 'name' => 'Macarca', 'native_name' => 'Magyar', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'ro', 'name' => 'Romence', 'native_name' => 'Română', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'el', 'name' => 'Yunanca', 'native_name' => 'Ελληνικά', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'bg', 'name' => 'Bulgarca', 'native_name' => 'Български', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        
        // Tier 3: GÜÇLÜ - Büyük diller
        ['code' => 'hi', 'name' => 'Hintçe', 'native_name' => 'हिन्दी', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'fa', 'name' => 'Farsça', 'native_name' => 'فارسی', 'direction' => 'rtl', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'th', 'name' => 'Tayca', 'native_name' => 'ไทย', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'vi', 'name' => 'Vietnamca', 'native_name' => 'Tiếng Việt', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'id', 'name' => 'Endonezce', 'native_name' => 'Bahasa Indonesia', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'ms', 'name' => 'Malayca', 'native_name' => 'Bahasa Melayu', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'he', 'name' => 'İbranice', 'native_name' => 'עברית', 'direction' => 'rtl', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'uk', 'name' => 'Ukraynaca', 'native_name' => 'Українська', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        
        // Tier 4: İYİ - Büyük kullanıcı kitleli diller (AI destekli)
        ['code' => 'bn', 'name' => 'Bengalce', 'native_name' => 'বাংলা', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'ur', 'name' => 'Urduca', 'native_name' => 'اردو', 'direction' => 'rtl', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'sw', 'name' => 'Swahili', 'native_name' => 'Kiswahili', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'hr', 'name' => 'Hırvatça', 'native_name' => 'Hrvatski', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'sk', 'name' => 'Slovakça', 'native_name' => 'Slovenčina', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'sl', 'name' => 'Slovence', 'native_name' => 'Slovenščina', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'lt', 'name' => 'Litvanca', 'native_name' => 'Lietuvių', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'lv', 'name' => 'Letonca', 'native_name' => 'Latviešu', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'et', 'name' => 'Estonca', 'native_name' => 'Eesti', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'ca', 'name' => 'Katalanca', 'native_name' => 'Català', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'af', 'name' => 'Afrikaans', 'native_name' => 'Afrikaans', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'tl', 'name' => 'Filipino', 'native_name' => 'Filipino', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
        ['code' => 'az', 'name' => 'Azerice', 'native_name' => 'Azərbaycan dili', 'direction' => 'ltr', 'is_visible' => false, 'is_active' => false, 'ai_quality' => 'good'],
    ];

    /**
     * ⚠️ ZAYIF AI DESTEKLİ DİLLER - Dikkatli kullanım gerektiren diller
     * Bu diller AI modellerinde sınırlı destek görür, çeviri kalitesi değişken olabilir
     * is_main_language = false → Modal'da uyarı çıkar: "Bu dil için AI çevirisi sınırlı"
     */
    private array $weakAiLanguages = [
        // Küçük Avrupa dilleri - Sınırlı AI desteği
        ['code' => 'sr', 'name' => 'Sırpça', 'native_name' => 'Српски', 'direction' => 'ltr'],
        ['code' => 'eu', 'name' => 'Baskça', 'native_name' => 'Euskara', 'direction' => 'ltr'],
        ['code' => 'ga', 'name' => 'İrlandaca', 'native_name' => 'Gaeilge', 'direction' => 'ltr'],
        ['code' => 'cy', 'name' => 'Galce', 'native_name' => 'Cymraeg', 'direction' => 'ltr'],
        ['code' => 'is', 'name' => 'İzlandaca', 'native_name' => 'Íslenska', 'direction' => 'ltr'],
        ['code' => 'mt', 'name' => 'Maltaca', 'native_name' => 'Malti', 'direction' => 'ltr'],
        ['code' => 'sq', 'name' => 'Arnavutça', 'native_name' => 'Shqip', 'direction' => 'ltr'],
        ['code' => 'mk', 'name' => 'Makedonca', 'native_name' => 'Македонски', 'direction' => 'ltr'],
        ['code' => 'hy', 'name' => 'Ermenice', 'native_name' => 'Հայերեն', 'direction' => 'ltr'],
        ['code' => 'ka', 'name' => 'Gürcüce', 'native_name' => 'ქართული', 'direction' => 'ltr'],
        ['code' => 'be', 'name' => 'Belarusça', 'native_name' => 'беларуская', 'direction' => 'ltr'],

        // Afrika dilleri - AI desteği zayıf
        ['code' => 'zu', 'name' => 'Zulu', 'native_name' => 'isiZulu', 'direction' => 'ltr'],
        ['code' => 'xh', 'name' => 'Xhosa', 'native_name' => 'isiXhosa', 'direction' => 'ltr'],
        ['code' => 'am', 'name' => 'Amharic', 'native_name' => 'አማርኛ', 'direction' => 'ltr'],
        ['code' => 'ig', 'name' => 'Igbo', 'native_name' => 'Asụsụ Igbo', 'direction' => 'ltr'],
        ['code' => 'yo', 'name' => 'Yoruba', 'native_name' => 'Yorùbá', 'direction' => 'ltr'],
        ['code' => 'ha', 'name' => 'Hausa', 'native_name' => 'Harshen Hausa', 'direction' => 'ltr'],

        // Asya dilleri - Sınırlı destek
        ['code' => 'ta', 'name' => 'Tamil', 'native_name' => 'தமிழ்', 'direction' => 'ltr'],
        ['code' => 'te', 'name' => 'Telugu', 'native_name' => 'తెలుగు', 'direction' => 'ltr'],
        ['code' => 'ml', 'name' => 'Malayalam', 'native_name' => 'മലയാളം', 'direction' => 'ltr'],
        ['code' => 'kn', 'name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ', 'direction' => 'ltr'],
        ['code' => 'gu', 'name' => 'Gujarati', 'native_name' => 'ગુજરાતી', 'direction' => 'ltr'],
        ['code' => 'pa', 'name' => 'Punjabi', 'native_name' => 'ਪੰਜਾਬੀ', 'direction' => 'ltr'],
        ['code' => 'mr', 'name' => 'Marathi', 'native_name' => 'मराठी', 'direction' => 'ltr'],
        ['code' => 'ne', 'name' => 'Nepali', 'native_name' => 'नेपाली', 'direction' => 'ltr'],
        ['code' => 'si', 'name' => 'Sinhala', 'native_name' => 'සිංහල', 'direction' => 'ltr'],
        ['code' => 'my', 'name' => 'Burmese', 'native_name' => 'မြန်မာ', 'direction' => 'ltr'],
        ['code' => 'km', 'name' => 'Khmer', 'native_name' => 'ភាសាខ្មែរ', 'direction' => 'ltr'],
        ['code' => 'lo', 'name' => 'Lao', 'native_name' => 'ພາសາລາວ', 'direction' => 'ltr'],
        ['code' => 'mn', 'name' => 'Mongolian', 'native_name' => 'Монгол', 'direction' => 'ltr'],
        ['code' => 'uz', 'name' => 'Uzbek', 'native_name' => 'Oʻzbek', 'direction' => 'ltr'],
        ['code' => 'kk', 'name' => 'Kazakh', 'native_name' => 'Қазақ', 'direction' => 'ltr'],
        ['code' => 'ky', 'name' => 'Kyrgyz', 'native_name' => 'Кыргыз', 'direction' => 'ltr'],
        ['code' => 'tg', 'name' => 'Tajik', 'native_name' => 'Тоҷикӣ', 'direction' => 'ltr'],
        ['code' => 'tk', 'name' => 'Turkmen', 'native_name' => 'Türkmen', 'direction' => 'ltr'],

        // Güneydoğu Asya - Zayıf destek
        ['code' => 'jv', 'name' => 'Javanese', 'native_name' => 'basa Jawa', 'direction' => 'ltr'],
        ['code' => 'su', 'name' => 'Sundanese', 'native_name' => 'basa Sunda', 'direction' => 'ltr'],
        ['code' => 'ceb', 'name' => 'Cebuano', 'native_name' => 'Sinugboanon', 'direction' => 'ltr'],

        // Orta Doğu - Sınırlı AI desteği
        ['code' => 'ku', 'name' => 'Kurdish', 'native_name' => 'Kurdî', 'direction' => 'ltr'],
        ['code' => 'ckb', 'name' => 'Central Kurdish', 'native_name' => 'کوردی', 'direction' => 'rtl'],
        ['code' => 'ps', 'name' => 'Pashto', 'native_name' => 'پښتو', 'direction' => 'rtl'],
        ['code' => 'sd', 'name' => 'Sindhi', 'native_name' => 'سنڌي', 'direction' => 'rtl'],

        // Amerikan kıtası
        ['code' => 'qu', 'name' => 'Quechua', 'native_name' => 'Runa Simi', 'direction' => 'ltr'],
        ['code' => 'gn', 'name' => 'Guarani', 'native_name' => 'Avañeẽ', 'direction' => 'ltr'],
        ['code' => 'ht', 'name' => 'Haiti Kreolü', 'native_name' => 'Kreyòl Ayisyen', 'direction' => 'ltr'],
        ['code' => 'ay', 'name' => 'Aymara', 'native_name' => 'aymar aru', 'direction' => 'ltr'],

        // Pasifik
        ['code' => 'mi', 'name' => 'Maori', 'native_name' => 'Te Reo Māori', 'direction' => 'ltr'],
        ['code' => 'sm', 'name' => 'Samoa', 'native_name' => 'Gagana Samoa', 'direction' => 'ltr'],
        ['code' => 'to', 'name' => 'Tonga', 'native_name' => 'Lea Fakatonga', 'direction' => 'ltr'],
        ['code' => 'fj', 'name' => 'Fiji', 'native_name' => 'Na Vosa Vakaviti', 'direction' => 'ltr'],

        // Yapay diller
        ['code' => 'eo', 'name' => 'Esperanto', 'native_name' => 'Esperanto', 'direction' => 'ltr'],
        ['code' => 'ia', 'name' => 'Interlingua', 'native_name' => 'Interlingua', 'direction' => 'ltr'],
        ['code' => 'vo', 'name' => 'Volapük', 'native_name' => 'Volapük', 'direction' => 'ltr'],

        // Tarihi diller
        ['code' => 'la', 'name' => 'Latin', 'native_name' => 'Latina', 'direction' => 'ltr'],
        ['code' => 'sa', 'name' => 'Sanskrit', 'native_name' => 'संस्कृत', 'direction' => 'ltr'],
        ['code' => 'pi', 'name' => 'Pali', 'native_name' => 'पालि', 'direction' => 'ltr'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('WorldLanguagesSeeder başlatıldı');
        
        $mainCount = 0;
        $otherCount = 0;
        
        // 1. GÜÇLÜ AI DESTEKLİ DİLLER - Ana kategoride
        foreach ($this->strongAiLanguages as $index => $langData) {
            $existing = DB::table('tenant_languages')
                ->where('code', $langData['code'])
                ->first();
            
            if (!$existing) {
                DB::table('tenant_languages')->insert([
                    'code' => $langData['code'],
                    'name' => $langData['name'],
                    'native_name' => $langData['native_name'],
                    'direction' => $langData['direction'],
                    'is_active' => $langData['is_active'],
                    'is_visible' => $langData['is_visible'],
                    'is_main_language' => true, // Güçlü AI destekli diller = Ana dil
                    'is_default' => false,
                    'sort_order' => $index + 1,
                    'flag_icon' => $this->getFlagEmoji($langData['code']),
                    'url_prefix_mode' => 'except_default',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $mainCount++;
            } else {
                // Ana dillerin mevcut durumunu koru, sadece bayrak güncelle
                DB::table('tenant_languages')
                    ->where('code', $langData['code'])
                    ->update([
                        'name' => $langData['name'],
                        'native_name' => $langData['native_name'],
                        'flag_icon' => $this->getFlagEmoji($langData['code']),
                        'updated_at' => now(),
                    ]);
            }
        }
        
        // 2. ZAYIF AI DESTEKLİ DİLLER - Uyarılı kullanım
        foreach ($this->weakAiLanguages as $index => $langData) {
            $existing = DB::table('tenant_languages')
                ->where('code', $langData['code'])
                ->first();
            
            if (!$existing) {
                DB::table('tenant_languages')->insert([
                    'code' => $langData['code'],
                    'name' => $langData['name'],
                    'native_name' => $langData['native_name'],
                    'direction' => $langData['direction'],
                    'is_active' => false,
                    'is_visible' => false, // GİZLİ
                    'is_main_language' => false, // Zayıf AI destekli = Uyarı gerekir
                    'is_default' => false,
                    'sort_order' => count($this->strongAiLanguages) + $index + 1,
                    'flag_icon' => $this->getFlagEmoji($langData['code']),
                    'url_prefix_mode' => 'except_default',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $otherCount++;
            }
        }
        
        Log::info("WorldLanguagesSeeder tamamlandı - Güçlü AI destekli: {$mainCount}, Zayıf AI destekli: {$otherCount}");
    }

    /**
     * Dil koduna göre bayrak emoji döndür
     */
    private function getFlagEmoji(string $code): string
    {
        $flags = [
            'tr' => '🇹🇷', 'en' => '🇬🇧', 'es' => '🇪🇸', 'fr' => '🇫🇷', 'de' => '🇩🇪',
            'it' => '🇮🇹', 'pt' => '🇵🇹', 'ru' => '🇷🇺', 'zh' => '🇨🇳', 'ja' => '🇯🇵',
            'ko' => '🇰🇷', 'ar' => '🇸🇦', 'hi' => '🇮🇳', 'th' => '🇹🇭', 'vi' => '🇻🇳',
            'nl' => '🇳🇱', 'pl' => '🇵🇱', 'sv' => '🇸🇪', 'no' => '🇳🇴', 'da' => '🇩🇰',
            'fi' => '🇫🇮', 'cs' => '🇨🇿', 'hu' => '🇭🇺', 'ro' => '🇷🇴', 'el' => '🇬🇷',
            'id' => '🇮🇩', 'ms' => '🇲🇾', 'bn' => '🇧🇩', 'fa' => '🇮🇷', 'he' => '🇮🇱',
            'uk' => '🇺🇦', 'bg' => '🇧🇬', 'sr' => '🇷🇸', 'hr' => '🇭🇷', 'sk' => '🇸🇰',
            'sl' => '🇸🇮', 'lt' => '🇱🇹', 'lv' => '🇱🇻', 'et' => '🇪🇪', 'ca' => '🇪🇸',
            'eu' => '🇪🇸', 'ga' => '🇮🇪', 'cy' => '🏴', 'is' => '🇮🇸', 'mt' => '🇲🇹',
            'sq' => '🇦🇱', 'mk' => '🇲🇰', 'hy' => '🇦🇲', 'ka' => '🇬🇪', 'be' => '🇧🇾',
            'sw' => '🇰🇪', 'af' => '🇿🇦', 'zu' => '🇿🇦', 'xh' => '🇿🇦', 'am' => '🇪🇹',
            'ig' => '🇳🇬', 'yo' => '🇳🇬', 'ha' => '🇳🇬', 'ta' => '🇮🇳', 'te' => '🇮🇳',
            'ml' => '🇮🇳', 'kn' => '🇮🇳', 'gu' => '🇮🇳', 'pa' => '🇮🇳', 'mr' => '🇮🇳',
            'ne' => '🇳🇵', 'si' => '🇱🇰', 'my' => '🇲🇲', 'km' => '🇰🇭', 'lo' => '🇱🇦',
            'mn' => '🇲🇳', 'uz' => '🇺🇿', 'kk' => '🇰🇿', 'ky' => '🇰🇬', 'tg' => '🇹🇯',
            'tk' => '🇹🇲', 'az' => '🇦🇿', 'jv' => '🇮🇩', 'su' => '🇮🇩', 'ceb' => '🇵🇭',
            'tl' => '🇵🇭', 'ur' => '🇵🇰', 'ku' => '🇹🇷', 'ckb' => '🇮🇶', 'ps' => '🇦🇫',
            'sd' => '🇵🇰', 'qu' => '🇵🇪', 'gn' => '🇵🇾', 'ht' => '🇭🇹', 'ay' => '🇧🇴',
            'mi' => '🇳🇿', 'sm' => '🇼🇸', 'to' => '🇹🇴', 'fj' => '🇫🇯', 'eo' => '🌍',
            'ia' => '🌐', 'vo' => '🌐', 'la' => '🏛️', 'sa' => '🕉️', 'pi' => '☸️',
        ];
        
        return $flags[$code] ?? '🌐';
    }
}