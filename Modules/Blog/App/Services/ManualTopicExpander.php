<?php

namespace Modules\Blog\App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Manuel Konu Genişletme Sistemi
 *
 * Kullanıcının girdiği manuel konuları akıllıca genişletir:
 *
 * 1. TEK KELİME (örn: "transpalet")
 *    → 10+ farklı açıdan konu üretir
 *    → "En iyi...", "Nasıl seçilir?", "Avantajları", vb.
 *
 * 2. UZUN BAŞLIK (örn: "A mı B mi?", "Nasıl yapılır?")
 *    → Birebir kullanılır, genişletme YAPILMAZ
 *
 * 3. GOOGLE-FRIENDLY FORMATLAR
 *    → "En iyi...", "X mi Y mi", "Nasıl...", "Neden..."
 */
class ManualTopicExpander
{
    /**
     * Tek kelime minimum uzunluğu (karakter)
     * Örn: "transpalet" = 10 karakter → Tek kelime
     */
    const SINGLE_WORD_MAX_LENGTH = 25;

    /**
     * Uzun başlık minimum kelime sayısı
     * Örn: "Akülü transpalet mi manuel transpalet mi?" = 6 kelime → Uzun başlık
     */
    const LONG_TITLE_MIN_WORDS = 4;

    /**
     * Manuel konuları genişlet
     *
     * @param array $manualTopics Manuel konu listesi (her satır bir konu)
     * @param int $maxExpansionPerTopic Tek kelime için kaç başlık üretilsin (varsayılan: 12)
     * @return array Genişletilmiş başlıklar
     */
    public function expand(array $manualTopics, int $maxExpansionPerTopic = 12): array
    {
        $expandedTopics = [];

        foreach ($manualTopics as $topic) {
            $topic = trim($topic);

            if (empty($topic)) {
                continue;
            }

            // Konu tipini tespit et
            $topicType = $this->detectTopicType($topic);

            Log::info('🔍 Manual Topic Analysis', [
                'topic' => $topic,
                'type' => $topicType,
                'word_count' => str_word_count($topic),
                'length' => strlen($topic),
            ]);

            if ($topicType === 'single_word') {
                // ✅ TEK KELİME → Genişlet
                $expanded = $this->expandSingleWord($topic, $maxExpansionPerTopic);
                $expandedTopics = array_merge($expandedTopics, $expanded);

                Log::info('✅ Single word expanded', [
                    'original' => $topic,
                    'expanded_count' => count($expanded),
                ]);

            } elseif ($topicType === 'long_title') {
                // ✅ UZUN BAŞLIK → Birebir kullan
                $expandedTopics[] = $topic;

                Log::info('✅ Long title used as-is', [
                    'title' => $topic,
                ]);
            } else {
                // ❓ ORTA UZUNLUK → Hafif genişletme (3-5 varyasyon)
                $expanded = $this->expandMediumPhrase($topic);
                $expandedTopics = array_merge($expandedTopics, $expanded);

                Log::info('✅ Medium phrase expanded', [
                    'original' => $topic,
                    'expanded_count' => count($expanded),
                ]);
            }
        }

        Log::info('📊 Manual Topic Expansion Complete', [
            'input_count' => count($manualTopics),
            'output_count' => count($expandedTopics),
        ]);

        return $expandedTopics;
    }

    /**
     * Konu tipini tespit et
     *
     * @param string $topic Konu
     * @return string 'single_word' | 'long_title' | 'medium_phrase'
     */
    protected function detectTopicType(string $topic): string
    {
        $wordCount = str_word_count($topic);
        $length = mb_strlen($topic);

        // 1. UZUN BAŞLIK: 4+ kelime VEYA soru işareti içeriyor
        if ($wordCount >= self::LONG_TITLE_MIN_WORDS || str_contains($topic, '?')) {
            return 'long_title';
        }

        // 2. TEK KELİME: 1-2 kelime VE 25 karakterden kısa
        if ($wordCount <= 2 && $length <= self::SINGLE_WORD_MAX_LENGTH) {
            return 'single_word';
        }

        // 3. ORTA UZUNLUK: Diğer durumlar
        return 'medium_phrase';
    }

    /**
     * Tek kelimeyi genişlet (12 farklı açı)
     *
     * Örn: "transpalet" →
     * - "En İyi Transpalet Modelleri"
     * - "Transpalet Nasıl Seçilir?"
     * - "Elektrikli vs Manuel Transpalet"
     * - vb.
     *
     * @param string $keyword Tek kelime (örn: "transpalet")
     * @param int $maxCount Maksimum genişletme sayısı
     * @return array Genişletilmiş başlıklar
     */
    protected function expandSingleWord(string $keyword, int $maxCount = 12): array
    {
        // Kelimenin ilk harfi büyük olacak şekilde formatla
        $keywordCapitalized = mb_convert_case($keyword, MB_CASE_TITLE, 'UTF-8');

        $expansionTemplates = [
            // Google'da aranan "En iyi..." formatları
            "En İyi {$keywordCapitalized} Modelleri [year]",
            "En İyi {$keywordCapitalized} Markaları",
            "{$keywordCapitalized} Tavsiyesi: En İyi Seçenekler",

            // "Nasıl..." formatları (How-to)
            "{$keywordCapitalized} Nasıl Seçilir? Kapsamlı Rehber",
            "{$keywordCapitalized} Nasıl Kullanılır? Adım Adım Kılavuz",
            "{$keywordCapitalized} Bakımı Nasıl Yapılır?",

            // "X mi Y mi" karşılaştırma formatları
            "Elektrikli {$keywordCapitalized} mi Manuel {$keywordCapitalized} mi?",
            "{$keywordCapitalized} Kiralama mı Satın Alma mı?",
            "Yeni {$keywordCapitalized} mi İkinci El {$keywordCapitalized} mi?",

            // "Neden..." formatları
            "Neden {$keywordCapitalized} Kullanmalısınız?",
            "{$keywordCapitalized} Neden Önemlidir?",

            // Avantaj/Fiyat formatları
            "{$keywordCapitalized} Avantajları ve Dezavantajları",
            "{$keywordCapitalized} Fiyatları [year]: Güncel Fiyat Listesi",
            "{$keywordCapitalized} Teknik Özellikleri: Detaylı İnceleme",

            // Problem/Çözüm formatları
            "{$keywordCapitalized} Seçerken Dikkat Edilmesi Gerekenler",
            "{$keywordCapitalized} Kullanımında Yapılan Yaygın Hatalar",
            "{$keywordCapitalized} ile İlgili Sık Sorulan Sorular",

            // Sektör/Uygulama formatları
            "Hangi Sektörlerde {$keywordCapitalized} Kullanılır?",
            "{$keywordCapitalized} Çeşitleri ve Kullanım Alanları",
            "{$keywordCapitalized} ile Verimliliği Artırın",
        ];

        // [year] placeholder'ını mevcut yılla değiştir
        $currentYear = date('Y');
        $expanded = array_map(function($template) use ($currentYear) {
            return str_replace('[year]', $currentYear, $template);
        }, $expansionTemplates);

        // Maksimum sayıya göre sınırla
        return array_slice($expanded, 0, $maxCount);
    }

    /**
     * Orta uzunluktaki ifadeleri hafif genişlet (3-5 varyasyon)
     *
     * Örn: "elektrikli transpalet" →
     * - "Elektrikli Transpalet Modelleri"
     * - "Elektrikli Transpalet Fiyatları"
     * - "Elektrikli Transpalet Nasıl Seçilir?"
     *
     * @param string $phrase Orta uzunluk ifade
     * @return array Hafif genişletilmiş başlıklar
     */
    protected function expandMediumPhrase(string $phrase): array
    {
        // Kelimenin ilk harfi büyük
        $phraseCapitalized = mb_convert_case($phrase, MB_CASE_TITLE, 'UTF-8');
        $currentYear = date('Y');

        return [
            // Orijinal (birebir kullan)
            $phraseCapitalized,

            // Hafif varyasyonlar
            "{$phraseCapitalized} Modelleri",
            "{$phraseCapitalized} Fiyatları {$currentYear}",
            "{$phraseCapitalized} Nasıl Seçilir?",
            "En İyi {$phraseCapitalized} Markaları",
        ];
    }

    /**
     * Manuel konu listesini Settings'ten parse et
     *
     * Settings'teki textarea her satır bir konu içerir:
     * - Boş satırlar atlanır
     * - Trim yapılır
     *
     * @param string|null $manualTopicsText Settings'teki textarea içeriği
     * @return array Manuel konu listesi
     */
    public static function parseManualTopicsFromSettings(?string $manualTopicsText): array
    {
        if (empty($manualTopicsText)) {
            return [];
        }

        // Satırlara böl
        $lines = explode("\n", $manualTopicsText);

        // Trim ve boş satırları temizle
        $topics = array_map('trim', $lines);
        $topics = array_filter($topics, fn($topic) => !empty($topic));

        return array_values($topics);
    }
}
