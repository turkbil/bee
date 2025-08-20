<?php

namespace Modules\SeoManagement\app\Services;

class AutoPriorityService
{
    /**
     * İçerik analizi yaparak otomatik SEO priority hesaplar
     * 
     * @param string $title Sayfa başlığı
     * @param string $slug URL slug
     * @param string $body İçerik (opsiyonel)
     * @param string $type Sayfa tipi (opsiyonel)
     * @param object|null $model Model instance (Page, Portfolio vs)
     * @return int 1-10 arası priority değeri
     */
    public static function calculateAutoPriority(
        string $title = '', 
        string $slug = '', 
        string $body = '', 
        string $type = '',
        $model = null
    ): int {
        $priority = 5; // Varsayılan orta değer
        
        // 1. URL DERİNLİK ANALİZİ (Ana faktör)
        $urlDepth = substr_count(trim($slug, '/'), '/');
        $depthScore = max(1, min(10, 8 - $urlDepth)); // Derinlik arttıkça priority azalır
        
        // 2. SAYFA TİPİ ANALİZİ
        $typeScore = self::getTypeScore($type, $slug, $model);
        
        // 3. İÇERİK KALİTE ANALİZİ
        $contentScore = self::getContentScore($title, $body);
        
        // 4. KEYWORD YOĞUNLUĞu ANALİZİ
        $keywordScore = self::getKeywordScore($title, $body);
        
        // SKORLARI BİRLEŞTİR (Ağırlıklı ortalama)
        $priority = round(
            ($depthScore * 0.4) +      // %40 URL derinliği
            ($typeScore * 0.3) +       // %30 Sayfa tipi  
            ($contentScore * 0.2) +    // %20 İçerik kalitesi
            ($keywordScore * 0.1)      // %10 Keyword yoğunluğu
        );
        
        // Güvenlik: 1-10 arasında tut
        return max(1, min(10, $priority));
    }
    
    /**
     * Sayfa tipine göre score hesapla
     */
    private static function getTypeScore(string $type, string $slug, $model = null): int
    {
        // 1. MODEL CONTEXT ANALİZİ (En güvenilir)
        if ($model) {
            // Page modeli kontrolü
            if (is_object($model) && method_exists($model, 'getTable')) {
                $tableName = $model->getTable();
                
                // Page modeli - is_homepage kontrolü
                if ($tableName === 'pages' && isset($model->is_homepage) && $model->is_homepage) {
                    return 10; // Ana sayfa = Max priority
                }
                
                // Modül tipine göre temel score
                switch ($tableName) {
                    case 'pages':
                        // Page modeli için özel slug analizi
                        return self::getPageSpecificScore($slug, $model);
                    case 'portfolios':
                        return 6; // Portfolio öğeleri
                    case 'announcements':
                        return 4; // Duyurular
                    case 'products':
                        return 6; // Ürünler
                    case 'categories':
                        return 7; // Kategoriler
                    default:
                        return 5; // Varsayılan
                }
            }
        }
        
        // 2. SLUG ANALİZİ (Backup)
        if (empty($slug) || $slug === '/' || $slug === 'home') {
            return 10; // Ana sayfa olabilir
        }
        
        // 3. ÖZEL SAYFALAR (Slug'dan tahmin)
        $specialPages = [
            'about' => 8, 'hakkimizda' => 8, 'about-us' => 8,
            'contact' => 7, 'iletisim' => 7, 'contact-us' => 7,
            'services' => 8, 'hizmetler' => 8, 'hizmetlerimiz' => 8,
            'products' => 8, 'urunler' => 8, 'urunlerimiz' => 8,
            'portfolio' => 7, 'portfolyo' => 7, 'galeri' => 7,
        ];
        
        $slugParts = explode('/', trim($slug, '/'));
        $firstSlug = $slugParts[0] ?? '';
        
        if (isset($specialPages[$firstSlug])) {
            return $specialPages[$firstSlug];
        }
        
        // 4. TİP STRING ANALİZİ (Son çare)
        switch (strtolower($type)) {
            case 'homepage':
            case 'home':
                return 10;
            case 'category':
            case 'categories':
                return 7;
            case 'product':
            case 'service':
                return 6;
            case 'blog':
            case 'post':
            case 'article':
                return 4;
            case 'page':
            default:
                return 5;
        }
    }
    
    /**
     * Page modeli için özel score hesaplama
     */
    private static function getPageSpecificScore(string $slug, $model): int
    {
        // Ana sayfa kontrolü zaten yapıldı, diğer özel durumlar
        
        // Slug bazlı sayfa önemi
        $pageImportance = [
            'about' => 8, 'hakkimizda' => 8, 'about-us' => 8,
            'contact' => 7, 'iletisim' => 7, 'contact-us' => 7, 'bize-ulasin' => 7,
            'services' => 8, 'hizmetler' => 8, 'hizmetlerimiz' => 8,
            'products' => 8, 'urunler' => 8, 'urunlerimiz' => 8,
            'privacy' => 5, 'gizlilik' => 5, 'privacy-policy' => 5,
            'terms' => 5, 'kosullar' => 5, 'kullanim-kosullari' => 5,
            'faq' => 6, 'sss' => 6, 'sikca-sorulan-sorular' => 6,
        ];
        
        $slugParts = explode('/', trim($slug, '/'));
        $firstSlug = $slugParts[0] ?? '';
        
        if (isset($pageImportance[$firstSlug])) {
            return $pageImportance[$firstSlug];
        }
        
        // URL derinliği bazlı score (Pages için)
        $depth = count($slugParts);
        if ($depth === 1) return 6; // Ana seviye sayfa
        if ($depth === 2) return 5; // İkinci seviye
        return 4; // Daha derin sayfalar
    }
    
    /**
     * İçerik kalitesine göre score hesapla
     */
    private static function getContentScore(string $title, string $body): int
    {
        $score = 3; // Varsayılan orta
        
        // Title analizi
        $titleLength = mb_strlen($title);
        if ($titleLength >= 40 && $titleLength <= 60) {
            $score += 2; // İdeal title uzunluğu
        } elseif ($titleLength >= 20) {
            $score += 1; // Kabul edilebilir
        }
        
        // Body analizi (varsa)
        if (!empty($body)) {
            $bodyLength = mb_strlen(strip_tags($body));
            
            if ($bodyLength > 2000) {
                $score += 2; // Uzun detaylı içerik
            } elseif ($bodyLength > 500) {
                $score += 1; // Orta uzunluk
            }
            
            // HTML tag varlığı (zengin içerik)
            if (preg_match('/<(h[1-6]|img|a|strong|em)/i', $body)) {
                $score += 1;
            }
        }
        
        return min(10, $score);
    }
    
    /**
     * Keyword yoğunluğuna göre score hesapla
     */
    private static function getKeywordScore(string $title, string $body): int
    {
        if (empty($title)) {
            return 3;
        }
        
        $score = 3; // Varsayılan
        
        // Title'da tekrarlanan kelimeler (spam kontrolü)
        $titleWords = str_word_count(strtolower($title), 1, 'çğıöşüÇĞIÖŞÜ');
        $uniqueWords = array_unique($titleWords);
        
        if (count($titleWords) > 0) {
            $uniqueRatio = count($uniqueWords) / count($titleWords);
            
            if ($uniqueRatio > 0.8) {
                $score += 2; // Çeşitli kelimeler
            } elseif ($uniqueRatio > 0.6) {
                $score += 1; // Orta çeşitlilik
            }
        }
        
        // Body'de title kelimelerinin geçmesi
        if (!empty($body) && !empty($titleWords)) {
            $bodyText = strtolower(strip_tags($body));
            $foundWords = 0;
            
            foreach ($titleWords as $word) {
                if (mb_strlen($word) > 3 && strpos($bodyText, $word) !== false) {
                    $foundWords++;
                }
            }
            
            if ($foundWords > 0) {
                $score += min(2, $foundWords); // Keyword tutarlılığı
            }
        }
        
        return min(10, $score);
    }
    
    /**
     * Hızlı JavaScript için basit hesaplama
     */
    public static function calculateQuickPriority(string $title = '', string $slug = ''): int
    {
        // Basit hız için sadece temel faktörler
        $urlDepth = substr_count(trim($slug, '/'), '/');
        $depthScore = max(1, min(10, 8 - $urlDepth));
        
        $titleLength = mb_strlen($title);
        $titleScore = ($titleLength >= 20 && $titleLength <= 60) ? 6 : 4;
        
        $priority = round(($depthScore * 0.7) + ($titleScore * 0.3));
        
        return max(1, min(10, $priority));
    }
}