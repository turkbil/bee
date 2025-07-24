<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class SeoWidget extends Component
{
    public array $seoData;
    public array $seoLimits;
    public array $validationRules;
    public string $language;
    public string $currentLanguage;
    public bool $showScore;
    
    /**
     * Global SEO Widget Component
     */
    public function __construct(
        array $seoData = [],
        array $seoLimits = [],
        array $validationRules = [],
        string $language = 'tr',
        string $currentLanguage = 'tr',
        bool $showScore = true
    ) {
        $this->seoData = $seoData;
        $this->seoLimits = $this->getDefaultLimits($seoLimits);
        $this->validationRules = $validationRules;
        $this->language = $language;
        $this->currentLanguage = $currentLanguage;
        $this->showScore = $showScore;
    }

    /**
     * Varsayılan SEO limitlerini getir
     */
    private function getDefaultLimits(array $customLimits): array
    {
        $defaults = [
            'seo_title' => 60,
            'seo_description' => 160,
            'seo_keywords_count' => 10,
            'canonical_url' => 255
        ];

        return array_merge($defaults, $customLimits);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(): array
    {
        $score = 0;
        $maxScore = 100;
        $checks = [];

        // Title kontrolü
        $titleLength = mb_strlen($this->seoData['seo_title'] ?? '');
        if ($titleLength >= 30 && $titleLength <= 60) {
            $score += 25;
            $checks['title'] = ['status' => 'good', 'message' => 'Title uzunluğu optimum'];
        } elseif ($titleLength > 0) {
            $score += 10;
            $checks['title'] = ['status' => 'warning', 'message' => 'Title çok kısa veya uzun'];
        } else {
            $checks['title'] = ['status' => 'error', 'message' => 'Title eksik'];
        }

        // Description kontrolü
        $descLength = mb_strlen($this->seoData['seo_description'] ?? '');
        if ($descLength >= 120 && $descLength <= 160) {
            $score += 25;
            $checks['description'] = ['status' => 'good', 'message' => 'Description uzunluğu optimum'];
        } elseif ($descLength > 0) {
            $score += 10;
            $checks['description'] = ['status' => 'warning', 'message' => 'Description çok kısa veya uzun'];
        } else {
            $checks['description'] = ['status' => 'error', 'message' => 'Description eksik'];
        }

        // Keywords kontrolü
        $keywords = $this->parseKeywords($this->seoData['seo_keywords'] ?? '');
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
        if (!empty($this->seoData['canonical_url'])) {
            if (filter_var($this->seoData['canonical_url'], FILTER_VALIDATE_URL)) {
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

    /**
     * Keyword'leri parse et
     */
    private function parseKeywords(?string $keywords): array
    {
        if (empty($keywords)) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $keywords)));
    }

    /**
     * Component view'ını render et
     */
    public function render(): View
    {
        $seoScore = $this->showScore ? $this->calculateSeoScore() : null;
        
        return view('components.seo-widget', [
            'seoData' => $this->seoData,
            'seoLimits' => $this->seoLimits,
            'seoScore' => $seoScore,
            'language' => $this->language
        ]);
    }
}