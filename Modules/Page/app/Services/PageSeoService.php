<?php

namespace Modules\Page\App\Services;

class PageSeoService
{
    /**
     * SEO konfigürasyon bilgilerini al
     */
    public static function getSeoConfig(): array
    {
        return config('page.seo', []);
    }

    /**
     * SEO alanlarının kurallarını al
     */
    public static function getSeoValidationRules(): array
    {
        $config = self::getSeoConfig();
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
    public static function getSeoLimits(): array
    {
        $config = self::getSeoConfig();
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
    public static function isFieldRequired(string $field): bool
    {
        $config = self::getSeoConfig();
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
     * SEO skorunu hesapla
     */
    public static function calculateSeoScore(array $data): array
    {
        $score = 0;
        $maxScore = 100;
        $checks = [];

        // Title kontrolü
        if (!empty($data['seo_title'])) {
            $titleLength = mb_strlen($data['seo_title']);
            if ($titleLength >= 30 && $titleLength <= 60) {
                $score += 25;
                $checks['title'] = ['status' => 'good', 'message' => 'Title uzunluğu optimum'];
            } elseif ($titleLength > 0) {
                $score += 10;
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
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 25;
                $checks['description'] = ['status' => 'good', 'message' => 'Description uzunluğu optimum'];
            } elseif ($descLength > 0) {
                $score += 10;
                $checks['description'] = ['status' => 'warning', 'message' => 'Description çok kısa veya uzun'];
            } else {
                $checks['description'] = ['status' => 'error', 'message' => 'Description eksik'];
            }
        } else {
            $checks['description'] = ['status' => 'error', 'message' => 'Description eksik'];
        }

        // Keywords kontrolü
        $keywords = self::parseKeywords($data['seo_keywords'] ?? '');
        if (count($keywords) >= 3 && count($keywords) <= 10) {
            $score += 25;
            $checks['keywords'] = ['status' => 'good', 'message' => 'Keyword sayısı optimum'];
        } elseif (count($keywords) > 0) {
            $score += 10;
            $checks['keywords'] = ['status' => 'warning', 'message' => 'Keyword sayısı az veya fazla'];
        } else {
            $checks['keywords'] = ['status' => 'error', 'message' => 'Keyword eksik'];
        }

        // Canonical URL kontrolü
        if (!empty($data['canonical_url'])) {
            if (filter_var($data['canonical_url'], FILTER_VALIDATE_URL)) {
                $score += 25;
                $checks['canonical'] = ['status' => 'good', 'message' => 'Canonical URL geçerli'];
            } else {
                $score += 5;
                $checks['canonical'] = ['status' => 'warning', 'message' => 'Canonical URL geçersiz'];
            }
        } else {
            $score += 15; // Canonical isteğe bağlı
            $checks['canonical'] = ['status' => 'info', 'message' => 'Canonical URL opsiyonel'];
        }

        return [
            'score' => $score,
            'percentage' => round(($score / $maxScore) * 100),
            'checks' => $checks
        ];
    }
}