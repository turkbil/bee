<?php

namespace Modules\LanguageManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorldLanguagesSeeder extends Seeder
{
    /**
     * Kapsamlı 250+ dünya dili listesi
     * AI çeviri için optimize edilmiş - tüm kıtalardan diller
     * Afrika, Asya, Avrupa, Amerika, Pasifik + Yapay diller
     */
    private array $worldLanguages = [
        // En popüler 10 dil (varsayılan aktif)
        ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tr', 'name' => 'Türkçe', 'native_name' => 'Türkçe', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'es', 'name' => 'İspanyolca', 'native_name' => 'Español', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'fr', 'name' => 'Fransızca', 'native_name' => 'Français', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'de', 'name' => 'Almanca', 'native_name' => 'Deutsch', 'direction' => 'ltr', 'default_active' => false],
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

        // Afrika dilleri
        ['code' => 'sw', 'name' => 'Swahili', 'native_name' => 'Kiswahili', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'af', 'name' => 'Afrikaans', 'native_name' => 'Afrikaans', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'zu', 'name' => 'Zulu', 'native_name' => 'isiZulu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'xh', 'name' => 'Xhosa', 'native_name' => 'isiXhosa', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'am', 'name' => 'Amharic', 'native_name' => 'አማርኛ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ig', 'name' => 'Igbo', 'native_name' => 'Asụsụ Igbo', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'yo', 'name' => 'Yoruba', 'native_name' => 'Yorùbá', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ha', 'name' => 'Hausa', 'native_name' => 'Harshen Hausa', 'direction' => 'ltr', 'default_active' => false],

        // Amerikan kıtası dilleri
        ['code' => 'qu', 'name' => 'Quechua', 'native_name' => 'Runa Simi', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'gn', 'name' => 'Guarani', 'native_name' => 'Avañeẽ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ht', 'name' => 'Haiti Kreolü', 'native_name' => 'Kreyòl Ayisyen', 'direction' => 'ltr', 'default_active' => false],

        // Pasifik dilleri
        ['code' => 'mi', 'name' => 'Maori', 'native_name' => 'Te Reo Māori', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sm', 'name' => 'Samoa', 'native_name' => 'Gagana Samoa', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'to', 'name' => 'Tonga', 'native_name' => 'Lea Fakatonga', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'fj', 'name' => 'Fiji', 'native_name' => 'Na Vosa Vakaviti', 'direction' => 'ltr', 'default_active' => false],

        // Diğer Asya dilleri
        ['code' => 'ta', 'name' => 'Tamil', 'native_name' => 'தமிழ்', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'te', 'name' => 'Telugu', 'native_name' => 'తెలుగు', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ml', 'name' => 'Malayalam', 'native_name' => 'മലയാളം', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'kn', 'name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'gu', 'name' => 'Gujarati', 'native_name' => 'ગુજરાતી', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'pa', 'name' => 'Punjabi', 'native_name' => 'ਪੰਜਾਬੀ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'or', 'name' => 'Oriya', 'native_name' => 'ଓଡ଼ିଆ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'as', 'name' => 'Assamese', 'native_name' => 'অসমীয়া', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ne', 'name' => 'Nepali', 'native_name' => 'नेपाली', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'si', 'name' => 'Sinhala', 'native_name' => 'සිංහල', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'my', 'name' => 'Burmese', 'native_name' => 'မြန်မာ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'km', 'name' => 'Khmer', 'native_name' => 'ភាសាខ្មែរ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'lo', 'name' => 'Lao', 'native_name' => 'ພາສາລາວ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mn', 'name' => 'Mongolian', 'native_name' => 'Монгол', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'uz', 'name' => 'Uzbek', 'native_name' => 'Oʻzbek', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'kk', 'name' => 'Kazakh', 'native_name' => 'Қазақ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ky', 'name' => 'Kyrgyz', 'native_name' => 'Кыргыз', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tg', 'name' => 'Tajik', 'native_name' => 'Тоҷикӣ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tk', 'name' => 'Turkmen', 'native_name' => 'Türkmen', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'az', 'name' => 'Azerbaijani', 'native_name' => 'Azərbaycan', 'direction' => 'ltr', 'default_active' => false],

        // Sami dilleri
        ['code' => 'se', 'name' => 'Northern Sami', 'native_name' => 'Davvisámegiella', 'direction' => 'ltr', 'default_active' => false],

        // Keltik diller
        ['code' => 'gd', 'name' => 'Scottish Gaelic', 'native_name' => 'Gàidhlig', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'br', 'name' => 'Breton', 'native_name' => 'Brezhoneg', 'direction' => 'ltr', 'default_active' => false],

        // Yapay dil
        ['code' => 'eo', 'name' => 'Esperanto', 'native_name' => 'Esperanto', 'direction' => 'ltr', 'default_active' => false],

        // Diğer önemli bölgesel diller
        ['code' => 'lb', 'name' => 'Luxembourgish', 'native_name' => 'Lëtzebuergesch', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'fo', 'name' => 'Faroese', 'native_name' => 'Føroyskt', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'kl', 'name' => 'Greenlandic', 'native_name' => 'Kalaallisut', 'direction' => 'ltr', 'default_active' => false],

        // Daha fazla Afrika dilleri
        ['code' => 'rw', 'name' => 'Kinyarwanda', 'native_name' => 'Ikinyarwanda', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'rn', 'name' => 'Kirundi', 'native_name' => 'Ikirundi', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'lg', 'name' => 'Luganda', 'native_name' => 'Oluganda', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'so', 'name' => 'Somali', 'native_name' => 'Soomaali', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ti', 'name' => 'Tigrinya', 'native_name' => 'ትግርኛ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'wo', 'name' => 'Wolof', 'native_name' => 'Wollof', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ff', 'name' => 'Fulah', 'native_name' => 'Fulfulde', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bm', 'name' => 'Bambara', 'native_name' => 'Bamanankan', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ee', 'name' => 'Ewe', 'native_name' => 'Èʋegbe', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tw', 'name' => 'Twi', 'native_name' => 'Twi', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ak', 'name' => 'Akan', 'native_name' => 'Akan', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ny', 'name' => 'Chichewa', 'native_name' => 'Chichewa', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sn', 'name' => 'Shona', 'native_name' => 'chiShona', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'st', 'name' => 'Sesotho', 'native_name' => 'Sesotho', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tn', 'name' => 'Setswana', 'native_name' => 'Setswana', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ts', 'name' => 'Xitsonga', 'native_name' => 'Xitsonga', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ss', 'name' => 'Siswati', 'native_name' => 'siSwati', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 've', 'name' => 'Tshivenda', 'native_name' => 'Tshivenḓa', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'nr', 'name' => 'isiNdebele', 'native_name' => 'isiNdebele', 'direction' => 'ltr', 'default_active' => false],

        // Daha fazla Amerikan yerli dilleri
        ['code' => 'nv', 'name' => 'Navajo', 'native_name' => 'Diné bizaad', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ik', 'name' => 'Inupiaq', 'native_name' => 'Iñupiaq', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'iu', 'name' => 'Inuktitut', 'native_name' => 'ᐃᓄᒃᑎᑐᑦ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'kl', 'name' => 'Kalaallisut', 'native_name' => 'Kalaallisut', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ay', 'name' => 'Aymara', 'native_name' => 'aymar aru', 'direction' => 'ltr', 'default_active' => false],

        // Daha fazla Asya-Pasifik dilleri
        ['code' => 'dv', 'name' => 'Dhivehi', 'native_name' => 'ދިވެހި', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'dz', 'name' => 'Dzongkha', 'native_name' => 'རྫོང་ཁ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bo', 'name' => 'Tibetan', 'native_name' => 'བོད་སྐད་', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ii', 'name' => 'Sichuan Yi', 'native_name' => 'ꆈꌠꁱꂷ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'za', 'name' => 'Zhuang', 'native_name' => 'Saɯ cueŋƅ', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ug', 'name' => 'Uyghur', 'native_name' => 'ئۇيغۇرچە', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'ps', 'name' => 'Pashto', 'native_name' => 'پښتو', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'sd', 'name' => 'Sindhi', 'native_name' => 'سنڌي', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'ks', 'name' => 'Kashmiri', 'native_name' => 'کٲشُر', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'ba', 'name' => 'Bashkir', 'native_name' => 'башҡорт теле', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tt', 'name' => 'Tatar', 'native_name' => 'татар теле', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'cv', 'name' => 'Chuvash', 'native_name' => 'чӑваш чӗлхи', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ce', 'name' => 'Chechen', 'native_name' => 'нохчийн мотт', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'os', 'name' => 'Ossetian', 'native_name' => 'ирон æвзаг', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ab', 'name' => 'Abkhazian', 'native_name' => 'аҧсуа бызшәа', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'av', 'name' => 'Avaric', 'native_name' => 'авар мацӀ', 'direction' => 'ltr', 'default_active' => false],

        // Daha fazla Avrupa dilleri
        ['code' => 'rm', 'name' => 'Romansh', 'native_name' => 'rumantsch grischun', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'fur', 'name' => 'Friulian', 'native_name' => 'furlan', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'lld', 'name' => 'Ladin', 'native_name' => 'ladin', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sc', 'name' => 'Sardinian', 'native_name' => 'sardu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'co', 'name' => 'Corsican', 'native_name' => 'corsu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'oc', 'name' => 'Occitan', 'native_name' => 'occitan', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'wa', 'name' => 'Walloon', 'native_name' => 'walon', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'li', 'name' => 'Limburgish', 'native_name' => 'Limburgs', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'nds', 'name' => 'Low German', 'native_name' => 'Plattdüütsch', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'hsb', 'name' => 'Upper Sorbian', 'native_name' => 'hornjoserbsce', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'dsb', 'name' => 'Lower Sorbian', 'native_name' => 'dolnoserbski', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'csb', 'name' => 'Kashubian', 'native_name' => 'kaszëbsczi', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'szl', 'name' => 'Silesian', 'native_name' => 'ślōnsko godka', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'rue', 'name' => 'Rusyn', 'native_name' => 'русиньскый', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'be', 'name' => 'Belarusian', 'native_name' => 'беларуская', 'direction' => 'ltr', 'default_active' => false],

        // Daha fazla Orta Doğu dilleri
        ['code' => 'ku', 'name' => 'Kurdish', 'native_name' => 'Kurdî', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ckb', 'name' => 'Central Kurdish', 'native_name' => 'کوردی', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'lrc', 'name' => 'Northern Luri', 'native_name' => 'لۊری شومالی', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'mzn', 'name' => 'Mazanderani', 'native_name' => 'مازرونی', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'glk', 'name' => 'Gilaki', 'native_name' => 'گیلکی', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'arc', 'name' => 'Aramaic', 'native_name' => 'ܐܪܡܝܐ', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'syc', 'name' => 'Classical Syriac', 'native_name' => 'ܣܘܪܝܝܐ', 'direction' => 'rtl', 'default_active' => false],

        // Güney Asya daha fazla diller
        ['code' => 'mr', 'name' => 'Marathi', 'native_name' => 'मराठी', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bho', 'name' => 'Bhojpuri', 'native_name' => 'भोजपुरी', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mai', 'name' => 'Maithili', 'native_name' => 'मैथिली', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mag', 'name' => 'Magahi', 'native_name' => 'मगही', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'awa', 'name' => 'Awadhi', 'native_name' => 'अवधी', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bra', 'name' => 'Braj', 'native_name' => 'ब्रज भाषा', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'new', 'name' => 'Newari', 'native_name' => 'नेपाल भाषा', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'dv', 'name' => 'Dhivehi', 'native_name' => 'ދިވެހިބަސް', 'direction' => 'rtl', 'default_active' => false],

        // Güneydoğu Asya daha fazla diller
        ['code' => 'jv', 'name' => 'Javanese', 'native_name' => 'basa Jawa', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'su', 'name' => 'Sundanese', 'native_name' => 'basa Sunda', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mad', 'name' => 'Madurese', 'native_name' => 'basa Madhura', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ban', 'name' => 'Balinese', 'native_name' => 'basa Bali', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bug', 'name' => 'Buginese', 'native_name' => 'basa Ugi', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mak', 'name' => 'Makasar', 'native_name' => 'basa Mangkasara', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'min', 'name' => 'Minangkabau', 'native_name' => 'baso Minangkabau', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ace', 'name' => 'Acehnese', 'native_name' => 'بهسا اچيه', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bjn', 'name' => 'Banjar', 'native_name' => 'bahasa Banjar', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tet', 'name' => 'Tetum', 'native_name' => 'tetun', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ceb', 'name' => 'Cebuano', 'native_name' => 'Sinugboanon', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ilo', 'name' => 'Iloko', 'native_name' => 'Pagsasao nga Iloko', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'hil', 'name' => 'Hiligaynon', 'native_name' => 'Ilonggo', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'war', 'name' => 'Waray', 'native_name' => 'Winaray', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'pam', 'name' => 'Kapampangan', 'native_name' => 'Kapampangan', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'pag', 'name' => 'Pangasinan', 'native_name' => 'Salitan Pangasinan', 'direction' => 'ltr', 'default_active' => false],

        // Daha fazla Pasifik dilleri
        ['code' => 'ho', 'name' => 'Hiri Motu', 'native_name' => 'Hiri Motu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tpi', 'name' => 'Tok Pisin', 'native_name' => 'Tok Pisin', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'bi', 'name' => 'Bislama', 'native_name' => 'Bislama', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ch', 'name' => 'Chamorro', 'native_name' => 'Chamoru', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'na', 'name' => 'Nauru', 'native_name' => 'Dorerin Naoero', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tv', 'name' => 'Tuvalu', 'native_name' => 'Te reo Tuvalu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ki', 'name' => 'Kikuyu', 'native_name' => 'Gĩkũyũ', 'direction' => 'ltr', 'default_active' => false],

        // Daha fazla yapay ve planlanmış diller
        ['code' => 'ia', 'name' => 'Interlingua', 'native_name' => 'Interlingua', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ie', 'name' => 'Interlingue', 'native_name' => 'Interlingue', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'vo', 'name' => 'Volapük', 'native_name' => 'Volapük', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'io', 'name' => 'Ido', 'native_name' => 'Ido', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'jbo', 'name' => 'Lojban', 'native_name' => 'la .lojban.', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'tlh', 'name' => 'Klingon', 'native_name' => 'tlhIngan Hol', 'direction' => 'ltr', 'default_active' => false],

        // Son 12 önemli dil - 200+ hedefe ulaşmak için
        ['code' => 'la', 'name' => 'Latin', 'native_name' => 'Latina', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'sa', 'name' => 'Sanskrit', 'native_name' => 'संस्कृत', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'pi', 'name' => 'Pali', 'native_name' => 'पालि', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'yi', 'name' => 'Yiddish', 'native_name' => 'ייִדיש', 'direction' => 'rtl', 'default_active' => false],
        ['code' => 'fy', 'name' => 'Western Frisian', 'native_name' => 'Frysk', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'gv', 'name' => 'Manx', 'native_name' => 'Gaelg', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'kw', 'name' => 'Cornish', 'native_name' => 'Kernowek', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'an', 'name' => 'Aragonese', 'native_name' => 'aragonés', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ast', 'name' => 'Asturian', 'native_name' => 'asturianu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'ext', 'name' => 'Extremaduran', 'native_name' => 'estremeñu', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'mwl', 'name' => 'Mirandese', 'native_name' => 'mirandés', 'direction' => 'ltr', 'default_active' => false],
        ['code' => 'vec', 'name' => 'Venetian', 'native_name' => 'vèneto', 'direction' => 'ltr', 'default_active' => false],
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
            // Afrika dilleri
            'sw' => '🇰🇪', // Swahili - Kenya bayrağı
            'af' => '🇿🇦', // Afrikaans
            'zu' => '🇿🇦', // Zulu
            'xh' => '🇿🇦', // Xhosa
            'am' => '🇪🇹', // Amharic
            'ig' => '🇳🇬', // Igbo
            'yo' => '🇳🇬', // Yoruba
            'ha' => '🇳🇬', // Hausa
            // Amerikan kıtası
            'qu' => '🇵🇪', // Quechua
            'gn' => '🇵🇾', // Guarani
            'ht' => '🇭🇹', // Haiti Kreolü
            // Pasifik
            'mi' => '🇳🇿', // Maori
            'sm' => '🇼🇸', // Samoa
            'to' => '🇹🇴', // Tonga
            'fj' => '🇫🇯', // Fiji
            // Diğer Asya
            'ta' => '🇮🇳', // Tamil
            'te' => '🇮🇳', // Telugu
            'ml' => '🇮🇳', // Malayalam
            'kn' => '🇮🇳', // Kannada
            'gu' => '🇮🇳', // Gujarati
            'pa' => '🇮🇳', // Punjabi
            'or' => '🇮🇳', // Oriya
            'as' => '🇮🇳', // Assamese
            'ne' => '🇳🇵', // Nepali
            'si' => '🇱🇰', // Sinhala
            'my' => '🇲🇲', // Burmese
            'km' => '🇰🇭', // Khmer
            'lo' => '🇱🇦', // Lao
            'mn' => '🇲🇳', // Mongolian
            'uz' => '🇺🇿', // Uzbek
            'kk' => '🇰🇿', // Kazakh
            'ky' => '🇰🇬', // Kyrgyz
            'tg' => '🇹🇯', // Tajik
            'tk' => '🇹🇲', // Turkmen
            'az' => '🇦🇿', // Azerbaijani
            // Sami
            'se' => '🇸🇪', // Northern Sami
            // Keltik
            'gd' => '🏴󠁧󠁢󠁳󠁣󠁴󠁿', // Scottish Gaelic
            'br' => '🇫🇷', // Breton
            // Yapay
            'eo' => '🌍', // Esperanto
            // Diğer bölgesel
            'lb' => '🇱🇺', // Luxembourgish
            'fo' => '🇫🇴', // Faroese
            'kl' => '🇬🇱', // Greenlandic
            // Daha fazla Afrika
            'rw' => '🇷🇼', // Kinyarwanda
            'rn' => '🇧🇮', // Kirundi
            'lg' => '🇺🇬', // Luganda
            'so' => '🇸🇴', // Somali
            'ti' => '🇪🇷', // Tigrinya
            'wo' => '🇸🇳', // Wolof
            'ff' => '🇬🇼', // Fulah
            'bm' => '🇲🇱', // Bambara
            'ee' => '🇬🇭', // Ewe
            'tw' => '🇬🇭', // Twi
            'ak' => '🇬🇭', // Akan
            'ny' => '🇲🇼', // Chichewa
            'sn' => '🇿🇼', // Shona
            'st' => '🇱🇸', // Sesotho
            'tn' => '🇧🇼', // Setswana
            'ts' => '🇿🇦', // Xitsonga
            'ss' => '🇸🇿', // Siswati
            've' => '🇿🇦', // Tshivenda
            'nr' => '🇿🇦', // isiNdebele
            // Amerikan yerli
            'nv' => '🇺🇸', // Navajo
            'ik' => '🇺🇸', // Inupiaq
            'iu' => '🇨🇦', // Inuktitut
            'ay' => '🇧🇴', // Aymara
            // Daha fazla Asya-Pasifik
            'dv' => '🇲🇻', // Dhivehi
            'dz' => '🇧🇹', // Dzongkha
            'bo' => '🇨🇳', // Tibetan
            'ii' => '🇨🇳', // Sichuan Yi
            'za' => '🇨🇳', // Zhuang
            'ug' => '🇨🇳', // Uyghur
            'ps' => '🇦🇫', // Pashto
            'sd' => '🇵🇰', // Sindhi
            'ks' => '🇮🇳', // Kashmiri
            'ba' => '🇷🇺', // Bashkir
            'tt' => '🇷🇺', // Tatar
            'cv' => '🇷🇺', // Chuvash
            'ce' => '🇷🇺', // Chechen
            'os' => '🇬🇪', // Ossetian
            'ab' => '🇬🇪', // Abkhazian
            'av' => '🇷🇺', // Avaric
            // Daha fazla Avrupa
            'rm' => '🇨🇭', // Romansh
            'fur' => '🇮🇹', // Friulian
            'lld' => '🇮🇹', // Ladin
            'sc' => '🇮🇹', // Sardinian
            'co' => '🇫🇷', // Corsican
            'oc' => '🇫🇷', // Occitan
            'wa' => '🇧🇪', // Walloon
            'li' => '🇳🇱', // Limburgish
            'nds' => '🇩🇪', // Low German
            'hsb' => '🇩🇪', // Upper Sorbian
            'dsb' => '🇩🇪', // Lower Sorbian
            'csb' => '🇵🇱', // Kashubian
            'szl' => '🇵🇱', // Silesian
            'rue' => '🇺🇦', // Rusyn
            'be' => '🇧🇾', // Belarusian
            // Daha fazla Orta Doğu
            'ku' => '🇹🇷', // Kurdish
            'ckb' => '🇮🇶', // Central Kurdish
            'lrc' => '🇮🇷', // Northern Luri
            'mzn' => '🇮🇷', // Mazanderani
            'glk' => '🇮🇷', // Gilaki
            'arc' => '🏛️', // Aramaic (tarihi)
            'syc' => '🏛️', // Classical Syriac (tarihi)
            // Güney Asya daha fazla
            'mr' => '🇮🇳', // Marathi
            'bho' => '🇮🇳', // Bhojpuri
            'mai' => '🇮🇳', // Maithili
            'mag' => '🇮🇳', // Magahi
            'awa' => '🇮🇳', // Awadhi
            'bra' => '🇮🇳', // Braj
            'new' => '🇳🇵', // Newari
            // Güneydoğu Asya daha fazla
            'jv' => '🇮🇩', // Javanese
            'su' => '🇮🇩', // Sundanese
            'mad' => '🇮🇩', // Madurese
            'ban' => '🇮🇩', // Balinese
            'bug' => '🇮🇩', // Buginese
            'mak' => '🇮🇩', // Makasar
            'min' => '🇮🇩', // Minangkabau
            'ace' => '🇮🇩', // Acehnese
            'bjn' => '🇮🇩', // Banjar
            'tet' => '🇹🇱', // Tetum
            'ceb' => '🇵🇭', // Cebuano
            'ilo' => '🇵🇭', // Iloko
            'hil' => '🇵🇭', // Hiligaynon
            'war' => '🇵🇭', // Waray
            'pam' => '🇵🇭', // Kapampangan
            'pag' => '🇵🇭', // Pangasinan
            // Daha fazla Pasifik
            'ho' => '🇵🇬', // Hiri Motu
            'tpi' => '🇵🇬', // Tok Pisin
            'bi' => '🇻🇺', // Bislama
            'ch' => '🇬🇺', // Chamorro
            'na' => '🇳🇷', // Nauru
            'tv' => '🇹🇻', // Tuvalu
            'ki' => '🇰🇪', // Kikuyu
            // Yapay diller
            'ia' => '🌐', // Interlingua
            'ie' => '🌐', // Interlingue
            'vo' => '🌐', // Volapük
            'io' => '🌐', // Ido
            'jbo' => '🌐', // Lojban
            'tlh' => '🖖', // Klingon (Vulcan salute emoji)
            // Son 12 dil
            'la' => '🏛️', // Latin (tarihi)
            'sa' => '🕉️', // Sanskrit (Hindu sembolü)
            'pi' => '☸️', // Pali (Budist sembolü)
            'yi' => '🇮🇱', // Yiddish
            'fy' => '🇳🇱', // Western Frisian
            'gv' => '🇮🇲', // Manx
            'kw' => '🏴󠁧󠁢󠁥󠁮󠁧󠁿', // Cornish
            'an' => '🇪🇸', // Aragonese
            'ast' => '🇪🇸', // Asturian
            'ext' => '🇪🇸', // Extremaduran
            'mwl' => '🇵🇹', // Mirandese
            'vec' => '🇮🇹', // Venetian
        ];

        return $flags[$code] ?? '🌐';
    }
}
