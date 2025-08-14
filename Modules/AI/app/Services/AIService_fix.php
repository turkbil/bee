    /**
     * Build translation prompt
     */
    private function buildTranslationPrompt(string $text, string $fromLang, string $toLang, string $context, bool $preserveHtml): string
    {
        $languageNames = [
            'tr' => 'Türkçe',
            'en' => 'English', 
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'it' => 'Italiano',
            'ar' => 'العربية',
            'da' => 'Dansk',
            'bn' => 'বাংলা',
            'sq' => 'Shqip',          // Arnavutça
            'zh' => '中文',           // Çince
            'ja' => '日本語',         // Japonca
            'ko' => '한국어',         // Korece
            'ru' => 'Русский',       // Rusça
            'pt' => 'Português',     // Portekizce
            'nl' => 'Nederlands',    // Hollandaca
            'sv' => 'Svenska',       // İsveççe
            'no' => 'Norsk',         // Norveççe
            'fi' => 'Suomi',         // Fince
            'pl' => 'Polski',        // Lehçe
            'cs' => 'Čeština',       // Çekçe
            'hu' => 'Magyar',        // Macarca
            'ro' => 'Română',        // Rumence
            'he' => 'עברית',         // İbranice
            'hi' => 'हिन्दी',         // Hintçe
            'th' => 'ไทย',           // Tayca
            'vi' => 'Tiếng Việt',    // Vietnamca
            'id' => 'Bahasa Indonesia', // Endonezce
            'fa' => 'فارسی',         // Farsça
            'ur' => 'اردو'           // Urduca
        ];

        $fromLanguageName = $languageNames[$fromLang] ?? $fromLang;
        $toLanguageName = $languageNames[$toLang] ?? $toLang;

        $contextInstructions = match($context) {
            'title' => 'Bu bir başlık metnidir. Kısa, net ve SEO dostu olmalıdır.',
            'seo_title' => 'Bu bir SEO başlığıdır. 60 karakter sınırında, anahtar kelime içermeli ve tıklanabilir olmalıdır.',
            'seo_description' => 'Bu bir SEO açıklamasıdır. 160 karakter sınırında, çekici ve bilgilendirici olmalıdır.',
            'seo_keywords' => 'Bunlar SEO anahtar kelimeleridir. Virgülle ayrılmış şekilde çevir.',
            'html_content' => 'Bu HTML içeriğidir. HTML etiketlerini koruyarak sadece metin kısmını çevir.',
            default => 'Bu genel bir metindir. Doğal ve akıcı bir şekilde çevir.'
        };

        $htmlInstructions = $preserveHtml ? "\n- HTML etiketlerini aynen koru, sadece metin içeriğini çevir" : "";

        return "Sen profesyonel bir çevirmensin. Aşağıdaki metni {$fromLanguageName} dilinden {$toLanguageName} diline çevir.

CONTEXT: {$contextInstructions}

ÇEVİRİ KURALLARI:
- Doğal ve akıcı bir çeviri yap
- Kültürel bağlamı koru
- Teknik terimleri doğru çevir{$htmlInstructions}
- Sadece çeviriyi döndür, başka açıklama ekleme

ÇEVİRİLECEK METİN:
{$text}";
    }