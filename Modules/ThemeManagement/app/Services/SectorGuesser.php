<?php

declare(strict_types=1);

namespace Modules\ThemeManagement\app\Services;

/**
 * İçerik ve site adından sektör tahmini yapma servisi
 */
class SectorGuesser
{
    private array $sectorKeywords = [
        'teknoloji' => [
            'yazılım', 'web', 'uygulama', 'ai', 'yapay zeka', 'blockchain',
            'mobil', 'sistem', 'database', 'api', 'kod', 'geliştirme',
            'bilişim', 'software', 'development', 'programming', 'digital',
            'otomasyon', 'robotik', 'algoritma', 'veri', 'analiz',
            'teknoloji', 'it', 'bilgisayar', 'internet', 'online',
            'dijital', 'elektronik', 'network', 'server', 'hosting'
        ],
        'sağlık' => [
            'diş', 'doktor', 'tedavi', 'sağlık', 'hasta', 'klinik',
            'tıp', 'ameliyat', 'kontrol', 'muayene', 'hastane',
            'eczane', 'ilaç', 'terapi', 'rehabilitasyon', 'fizik',
            'diyetisyen', 'beslenme', 'vitamin', 'check-up'
        ],
        'güzellik' => [
            'kuaför', 'güzellik', 'saç', 'makyaj', 'cilt', 'masaj',
            'estetik', 'bakım', 'spa', 'solaryum', 'epilasyon',
            'botox', 'filler', 'protez', 'nail', 'tırnak', 'wax',
            'lash', 'kirpik', 'kaş', 'yüz', 'vücut'
        ],
        'eğitim' => [
            'kurs', 'eğitim', 'ders', 'öğretim', 'akademi', 'sınav',
            'dil', 'üniversite', 'okul', 'öğrenci', 'öğretmen',
            'diploma', 'sertifika', 'seminer', 'workshop', 'training',
            'koçluk', 'mentoring', 'rehberlik', 'ödev', 'müfradat'
        ],
        'hukuk' => [
            'avukat', 'hukuk', 'dava', 'mahkeme', 'miras', 'boşanma',
            'ceza', 'danışman', 'müvekkil', 'dilekçe', 'icra',
            'noter', 'sözleşme', 'anlaşma', 'tazminat', 'sigorta',
            'vekalet', 'savunma', 'iddia', 'karar', 'temyiz'
        ],
        'restoran' => [
            'yemek', 'restoran', 'pizza', 'kebap', 'cafe', 'menü',
            'lezzet', 'aşçı', 'mutfak', 'garson', 'rezervasyon',
            'paket', 'sipariş', 'fast food', 'burger', 'pasta',
            'tatlı', 'içecek', 'kahve', 'çay', 'alkol', 'bar'
        ],
        'inşaat' => [
            'inşaat', 'yapı', 'bina', 'ev', 'villa', 'apartman',
            'tadilat', 'dekorasyon', 'mimari', 'proje', 'çizim',
            'betonarme', 'çelik', 'boyama', 'döşeme', 'çatı',
            'bahçe', 'peyzaj', 'elektrik', 'tesisat', 'ısıtma'
        ],
        'finans' => [
            'finans', 'banka', 'kredi', 'yatırım', 'sigorta', 'para',
            'borsa', 'döviz', 'altın', 'emeklilik', 'tasarruf',
            'muhasebe', 'vergi', 'mali', 'ekonomi', 'fon',
            'hisse', 'tahvil', 'kâr', 'zarar', 'bilanço'
        ],
        'turizm' => [
            'otel', 'tatil', 'seyahat', 'turizm', 'rezervasyon', 'konaklama',
            'gezi', 'rehber', 'uçak', 'bilet', 'transfer', 'yacht',
            'deniz', 'plaj', 'dağ', 'doğa', 'kamp', 'motel',
            'pansiyon', 'apart', 'villa', 'balayı', 'honeymoon',
            'tur operatör', 'tur şirket', 'turist', 'turistik'
        ],
        'otomotiv' => [
            'araba', 'otomobil', 'motor', 'lastik', 'yedek parça',
            'tamير', 'servis', 'garage', 'mekanik', 'boyama',
            'kaporta', 'ekspertiz', 'muayene', 'plaka', 'ruhsat',
            'ehliyet', 'sürücü', 'trafik', 'araç', 'vasıta'
        ]
    ];

    /**
     * İçerik ve site adından sektör tahmin et
     */
    public function guessSector(string $content, string $siteName): string
    {
        $text = $this->normalizeText($content . ' ' . $siteName);
        $scores = [];

        // Her sektör için skor hesapla
        foreach ($this->sectorKeywords as $sector => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                // Çok kısa kelimeler için tam kelime eşleşmesi gerekli (3 karakter ve altı)
                if (strlen($keyword) <= 3) {
                    // Tam kelime eşleşmesi kontrol et
                    if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $text)) {
                        $score++;

                        // Site adında geçerse bonus puan
                        if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $this->normalizeText($siteName))) {
                            $score += 2;
                        }
                    }
                } else {
                    // Uzun kelimeler için contains yeterli
                    if (str_contains($text, $keyword)) {
                        $score++;

                        // Site adında geçerse bonus puan
                        if (str_contains($this->normalizeText($siteName), $keyword)) {
                            $score += 2;
                        }
                    }
                }
            }
            $scores[$sector] = $score;
        }

        // En yüksek skoru al
        arsort($scores);
        $topSector = array_key_first($scores);

        // Minimum eşik kontrolü (hiç eşleşme yoksa genel)
        return ($scores[$topSector] > 0) ? $topSector : 'genel';
    }

    /**
     * Metni normalize et
     */
    private function normalizeText(string $text): string
    {
        $text = strtolower($text);

        // Türkçe karakter dönüşümleri
        $replacements = [
            'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ı' => 'i',
            'ö' => 'o', 'ç' => 'c', 'Ğ' => 'g', 'Ü' => 'u',
            'Ş' => 's', 'İ' => 'i', 'Ö' => 'o', 'Ç' => 'c'
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Sektör için anahtar kelimeleri getir
     */
    public function getKeywordsForSector(string $sector): array
    {
        return $this->sectorKeywords[$sector] ?? [];
    }

    /**
     * Tüm desteklenen sektörleri getir
     */
    public function getSupportedSectors(): array
    {
        return array_keys($this->sectorKeywords);
    }

    /**
     * Sektör tahminini detaylı analiz ile döndür
     */
    public function guessWithDetails(string $content, string $siteName): array
    {
        $text = $this->normalizeText($content . ' ' . $siteName);
        $scores = [];
        $matchedKeywords = [];

        foreach ($this->sectorKeywords as $sector => $keywords) {
            $score = 0;
            $matches = [];

            foreach ($keywords as $keyword) {
                // Çok kısa kelimeler için tam kelime eşleşmesi gerekli (3 karakter ve altı)
                if (strlen($keyword) <= 3) {
                    if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $text)) {
                        $score++;
                        $matches[] = $keyword;

                        if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $this->normalizeText($siteName))) {
                            $score += 2;
                        }
                    }
                } else {
                    if (str_contains($text, $keyword)) {
                        $score++;
                        $matches[] = $keyword;

                        if (str_contains($this->normalizeText($siteName), $keyword)) {
                            $score += 2;
                        }
                    }
                }
            }

            $scores[$sector] = $score;
            $matchedKeywords[$sector] = $matches;
        }

        arsort($scores);

        return [
            'predicted_sector' => array_key_first($scores),
            'confidence_scores' => $scores,
            'matched_keywords' => $matchedKeywords,
            'top_sectors' => array_slice($scores, 0, 3, true)
        ];
    }
}