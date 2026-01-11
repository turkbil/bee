<?php

declare(strict_types=1);

namespace Modules\ThemeManagement\app\Services;

/**
 * Sayfa başlığından otomatik sayfa türü tespiti
 */
class PageTypeDetector
{
    private array $pageTypePatterns = [
        'hizmetler' => [
            'hizmet', 'service', 'çözüm', 'destek', 'danışmanlık',
            'servis', 'yardım', 'uzman', 'konsültasyon'
        ],
        'ürünler' => [
            'ürün', 'product', 'satış', 'mağaza', 'shop', 'store',
            'katalog', 'market', 'alışveriş', 'pazarlama', 'tekli', 'tek'
        ],
        'hakkımızda' => [
            'hakkı', 'about', 'kim', 'tarih', 'vizyon', 'misyon',
            'ekip', 'team', 'kurucu', 'founder', 'biz'
        ],
        'portfolio' => [
            'portfolio', 'galeri', 'çalışma', 'proje', 'work',
            'örnek', 'referans', 'showcase', 'sample'
        ],
        'iletişim' => [
            'iletişim', 'contact', 'adres', 'telefon', 'email',
            'ulaş', 'mesaj', 'form', 'reach', 'call'
        ],
        'blog' => [
            'blog', 'makale', 'haber', 'yazı', 'article', 'news',
            'güncel', 'içerik', 'content', 'post'
        ],
        'anasayfa' => [
            'ana', 'home', 'index', 'giriş', 'main', 'welcome',
            'başlangıç', 'start', 'homepage'
        ],
        'fiyat' => [
            'fiyat', 'price', 'ücret', 'tarife', 'paket', 'plan',
            'cost', 'pricing', 'fee', 'rate'
        ],
        'sss' => [
            'sss', 'faq', 'soru', 'cevap', 'sorular', 'questions',
            'help', 'destek', 'bilgi'
        ]
    ];

    /**
     * Sayfa başlığından türü tespit et
     */
    public function detectFromTitle(string $title): string
    {
        $title = $this->normalizeText($title);

        foreach ($this->pageTypePatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($title, $pattern)) {
                    return $type;
                }
            }
        }

        return 'genel'; // Varsayılan
    }

    /**
     * Metni normalize et (Türkçe karakterler + küçük harf)
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
     * Tüm desteklenen sayfa türlerini getir
     */
    public function getSupportedPageTypes(): array
    {
        return array_keys($this->pageTypePatterns);
    }

    /**
     * Sayfa türü için pattern'leri getir
     */
    public function getPatternsForType(string $type): array
    {
        return $this->pageTypePatterns[$type] ?? [];
    }
}