<?php

namespace App\Services;

class GlobalSeoService
{
    /**
     * Modül için SEO konfigürasyon bilgilerini al
     */
    public static function getSeoConfig(string $module = 'default'): array
    {
        // Önce modül-specific config'e bak, yoksa default kullan
        $moduleConfig = config("{$module}.seo", []);
        $defaultConfig = config('seo.default', []);
        
        return array_merge($defaultConfig, $moduleConfig);
    }

    /**
     * SEO alanlarının kurallarını al
     */
    public static function getSeoValidationRules(string $module = 'default'): array
    {
        $config = self::getSeoConfig($module);
        $rules = [];

        foreach ($config['fields'] ?? [] as $field => $settings) {
            $fieldRules = [];
            
            if ($settings['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }
            
            if (isset($settings['max_length'])) {
                $fieldRules[] = 'max:' . $settings['max_length'];
            }
            
            $rules[$field] = implode('|', $fieldRules);
        }

        return $rules;
    }

    /**
     * SEO alanlarının karakter limitlerini al
     */
    public static function getSeoLimits(string $module = 'default'): array
    {
        $config = self::getSeoConfig($module);
        $limits = [];

        foreach ($config['fields'] ?? [] as $field => $settings) {
            if (isset($settings['max_length'])) {
                $limits[$field] = $settings['max_length'];
            }
            if (isset($settings['max_keywords'])) {
                $limits[$field . '_count'] = $settings['max_keywords'];
            }
        }

        return $limits;
    }

    /**
     * SEO alanının gerekli olup olmadığını kontrol et
     */
    public static function isFieldRequired(string $field, string $module = 'default'): bool
    {
        $config = self::getSeoConfig($module);
        return $config['fields'][$field]['required'] ?? false;
    }

    /**
     * Keywords string'ini array'e çevir
     */
    public static function parseKeywords(?string $keywords): array
    {
        if (empty($keywords)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $keywords)));
    }

    /**
     * Keywords array'ini string'e çevir
     */
    public static function stringifyKeywords(array $keywords): string
    {
        return implode(', ', array_filter($keywords));
    }

    /**
     * SEO skorunu hesapla (modül-specific kurallarla)
     */
    public static function calculateSeoScore(array $data, string $module = 'default'): array
    {
        $config = self::getSeoConfig($module);
        $scoring = $config['scoring'] ?? self::getDefaultScoringRules();
        
        $score = 0;
        $maxScore = 100;
        $checks = [];

        // Title kontrolü
        if (!empty($data['seo_title'])) {
            $titleLength = mb_strlen($data['seo_title']);
            $titleRules = $scoring['title'] ?? ['min' => 30, 'max' => 60, 'weight' => 25];
            
            if ($titleLength >= $titleRules['min'] && $titleLength <= $titleRules['max']) {
                $score += $titleRules['weight'];
                $checks['title'] = ['status' => 'good', 'message' => 'Title uzunluğu optimum'];
            } elseif ($titleLength > 0) {
                $score += ($titleRules['weight'] * 0.4);
                $checks['title'] = ['status' => 'warning', 'message' => 'Title çok kısa veya uzun'];
            } else {
                $checks['title'] = ['status' => 'error', 'message' => 'Title eksik'];
            }
        } else {
            $checks['title'] = ['status' => 'error', 'message' => 'Title eksik'];
        }

        // Description kontrolü
        if (!empty($data['seo_description'])) {
            $descLength = mb_strlen($data['seo_description']);
            $descRules = $scoring['description'] ?? ['min' => 120, 'max' => 160, 'weight' => 25];
            
            if ($descLength >= $descRules['min'] && $descLength <= $descRules['max']) {
                $score += $descRules['weight'];
                $checks['description'] = ['status' => 'good', 'message' => 'Description uzunluğu optimum'];
            } elseif ($descLength > 0) {
                $score += ($descRules['weight'] * 0.4);
                $checks['description'] = ['status' => 'warning', 'message' => 'Description çok kısa veya uzun'];
            } else {
                $checks['description'] = ['status' => 'error', 'message' => 'Description eksik'];
            }
        } else {
            $checks['description'] = ['status' => 'error', 'message' => 'Description eksik'];
        }

        // Keywords kontrolü
        $keywords = self::parseKeywords($data['seo_keywords'] ?? '');
        $keywordRules = $scoring['keywords'] ?? ['min' => 3, 'max' => 10, 'weight' => 25];
        
        if (count($keywords) >= $keywordRules['min'] && count($keywords) <= $keywordRules['max']) {
            $score += $keywordRules['weight'];
            $checks['keywords'] = ['status' => 'good', 'message' => 'Keyword sayısı optimum'];
        } elseif (count($keywords) > 0) {
            $score += ($keywordRules['weight'] * 0.4);
            $checks['keywords'] = ['status' => 'warning', 'message' => 'Keyword sayısı az veya fazla'];
        } else {
            $checks['keywords'] = ['status' => 'error', 'message' => 'Keyword eksik'];
        }

        // Canonical URL kontrolü
        $canonicalRules = $scoring['canonical'] ?? ['weight' => 25, 'optional_score' => 15];
        
        if (!empty($data['canonical_url'])) {
            if (filter_var($data['canonical_url'], FILTER_VALIDATE_URL)) {
                $score += $canonicalRules['weight'];
                $checks['canonical'] = ['status' => 'good', 'message' => 'Canonical URL geçerli'];
            } else {
                $score += ($canonicalRules['weight'] * 0.2);
                $checks['canonical'] = ['status' => 'warning', 'message' => 'Canonical URL geçersiz'];
            }
        } else {
            $score += $canonicalRules['optional_score']; // Canonical isteğe bağlı
            $checks['canonical'] = ['status' => 'info', 'message' => 'Canonical URL opsiyonel'];
        }

        return [
            'score' => $score,
            'percentage' => round(($score / $maxScore) * 100),
            'checks' => $checks,
            'module' => $module
        ];
    }

    /**
     * Varsayılan scoring kuralları
     */
    private static function getDefaultScoringRules(): array
    {
        return [
            'title' => ['min' => 30, 'max' => 60, 'weight' => 25],
            'description' => ['min' => 120, 'max' => 160, 'weight' => 25],
            'keywords' => ['min' => 3, 'max' => 10, 'weight' => 25],
            'canonical' => ['weight' => 25, 'optional_score' => 15]
        ];
    }

    /**
     * Modül için SEO field'larını al
     */
    public static function getSeoFields(string $module = 'default'): array
    {
        $config = self::getSeoConfig($module);
        return $config['fields'] ?? self::getDefaultFields();
    }

    /**
     * Varsayılan SEO field'ları
     */
    private static function getDefaultFields(): array
    {
        return [
            'seo_title' => [
                'required' => true,
                'max_length' => 60,
                'type' => 'text'
            ],
            'seo_description' => [
                'required' => true,
                'max_length' => 160,
                'type' => 'textarea'
            ],
            'seo_keywords' => [
                'required' => false,
                'max_keywords' => 10,
                'type' => 'text'
            ],
            'canonical_url' => [
                'required' => false,
                'type' => 'url'
            ]
        ];
    }

    /**
     * Modül için SEO validation mesajlarını al
     */
    public static function getSeoValidationMessages(string $module = 'default'): array
    {
        $config = self::getSeoConfig($module);
        return $config['validation_messages'] ?? [
            'seo_title.required' => 'SEO başlık alanı zorunludur.',
            'seo_title.max' => 'SEO başlık en fazla 60 karakter olabilir.',
            'seo_description.required' => 'SEO açıklama alanı zorunludur.',
            'seo_description.max' => 'SEO açıklama en fazla 160 karakter olabilir.',
            'canonical_url.url' => 'Canonical URL geçerli bir URL olmalıdır.'
        ];
    }

    /**
     * Modül-specific SEO helper metotları
     */
    public static function getModuleSpecificSeoData(string $module, array $data): array
    {
        switch ($module) {
            case 'page':
                return self::processPageSeoData($data);
            case 'portfolio':
                return self::processPortfolioSeoData($data);
            case 'blog':
                return self::processBlogSeoData($data);
            default:
                return self::processDefaultSeoData($data);
        }
    }

    /**
     * Page modülü için özel SEO işlemleri
     */
    private static function processPageSeoData(array $data): array
    {
        // Page-specific SEO logic
        return $data;
    }

    /**
     * Portfolio modülü için özel SEO işlemleri
     */
    private static function processPortfolioSeoData(array $data): array
    {
        // Portfolio-specific SEO logic
        return $data;
    }

    /**
     * Blog modülü için özel SEO işlemleri
     */
    private static function processBlogSeoData(array $data): array
    {
        // Blog-specific SEO logic
        return $data;
    }

    /**
     * Varsayılan SEO işlemleri
     */
    private static function processDefaultSeoData(array $data): array
    {
        return $data;
    }
}