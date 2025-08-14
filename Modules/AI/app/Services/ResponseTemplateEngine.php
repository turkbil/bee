<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\AI\App\Models\AIFeature;

/**
 * 🎨 RESPONSE TEMPLATE ENGINE V2 - Dynamic AI Response Formatting System
 * 
 * Bu engine AI yanıtlarını monoton 1-2-3 formatından kurtarır:
 * - Feature-aware template selection
 * - Dynamic response structuring
 * - Template validation and parsing
 * - Anti-monotony formatting rules
 * 
 * NASIL ÇALIŞIR:
 * 1. Feature'ın response_template JSON'ını parse eder
 * 2. Template rules'a göre prompt formatlar
 * 3. AI'dan yanıt alındıktan sonra template'e uygun şekilde post-process eder
 * 4. Monoton yapıları kırarak natural format üretir
 */
class ResponseTemplateEngine
{
    /**
     * 🎯 Template format types - Yanıt türleri
     */
    public const FORMAT_TYPES = [
        'narrative'    => 'Akıcı paragraf formatı (blog, makale)',
        'list'         => 'Liste formatı (adımlar, öneriler)',  
        'structured'   => 'Yapılandırılmış format (analiz, rapor)',
        'code'         => 'Kod formatı (programlama)',
        'table'        => 'Tablo formatı (karşılaştırma, veri)',
        'mixed'        => 'Karma format (çoklu bölüm)',
        'creative'     => 'Yaratıcı format (hikaye, konsept)',
        'technical'    => 'Teknik format (dokümantasyon)'
    ];

    /**
     * 🎭 Style types - Yazım tarzları
     */
    public const STYLE_TYPES = [
        'professional' => 'Profesyonel, resmi ton',
        'casual'       => 'Gündelik, samimi ton',
        'academic'     => 'Akademik, bilimsel ton', 
        'creative'     => 'Yaratıcı, artistik ton',
        'technical'    => 'Teknik, detaylı ton',
        'friendly'     => 'Arkadaşça, sıcak ton',
        'authoritative'=> 'Otoriter, güvenilir ton'
    ];

    /**
     * 🚫 Anti-monotony rules - Tekrarlı yapıları kıran kurallar
     */
    public const ANTI_MONOTONY_RULES = [
        'no_numbering'      => '1-2-3 şeklinde numaralandırma yapma',
        'use_paragraphs'    => 'Paragraf formatını tercih et', 
        'vary_structure'    => 'Yapıyı değiştir, monotonluktan kaçın',
        'contextual_format' => 'İçeriğe göre format belirle',
        'natural_flow'      => 'Doğal akış kullan',
        'avoid_bullets'     => 'Madde imlerinden kaçın',
        'mixed_formats'     => 'Farklı format türlerini karıştır',
        'dynamic_sections'  => 'Dinamik bölüm yapısı kullan'
    ];

    /**
     * 🔧 Feature-aware template mappings
     * Feature türüne göre otomatik template seçimi
     */
    public const FEATURE_TEMPLATE_MAPPINGS = [
        // Blog/Content features
        'blog' => [
            'format' => 'narrative',
            'style' => 'professional',
            'default_rules' => ['no_numbering', 'use_paragraphs', 'natural_flow']
        ],
        'makale' => [
            'format' => 'narrative', 
            'style' => 'academic',
            'default_rules' => ['no_numbering', 'use_paragraphs', 'contextual_format']
        ],
        'icerik' => [
            'format' => 'mixed',
            'style' => 'professional', 
            'default_rules' => ['vary_structure', 'dynamic_sections']
        ],
        
        // SEO/Analysis features
        'seo' => [
            'format' => 'structured',
            'style' => 'technical',
            'default_rules' => ['contextual_format', 'mixed_formats']
        ],
        'analiz' => [
            'format' => 'structured',
            'style' => 'technical',
            'default_rules' => ['contextual_format', 'dynamic_sections']
        ],
        
        // Code features
        'kod' => [
            'format' => 'code',
            'style' => 'technical',
            'default_rules' => ['contextual_format']
        ],
        'programlama' => [
            'format' => 'code',
            'style' => 'technical', 
            'default_rules' => ['contextual_format']
        ],
        
        // Translation features
        'ceviri' => [
            'format' => 'narrative',
            'style' => 'professional',
            'default_rules' => ['natural_flow', 'contextual_format']
        ],
        'translate' => [
            'format' => 'narrative',
            'style' => 'professional',
            'default_rules' => ['natural_flow', 'contextual_format']
        ],
        
        // Creative features
        'hikaye' => [
            'format' => 'creative',
            'style' => 'creative',
            'default_rules' => ['natural_flow', 'vary_structure']
        ],
        'yaratici' => [
            'format' => 'creative',
            'style' => 'creative',
            'default_rules' => ['natural_flow', 'mixed_formats']
        ]
    ];

    /**
     * 🎯 MAIN METHOD: Build template-aware system prompt
     * 
     * @param AIFeature $feature - AI feature instance
     * @param array $options - Additional options
     * @return string - Enhanced system prompt with template instructions
     */
    public function buildTemplateAwarePrompt(AIFeature $feature, array $options = []): string
    {
        try {
            // Template'i parse et
            $template = $this->parseTemplate($feature);
            
            // Feature-aware template detection
            $detectedTemplate = $this->detectFeatureTemplate($feature->slug);
            
            // Template merge (detected + custom)
            $finalTemplate = $this->mergeTemplates($detectedTemplate, $template);
            
            // Anti-monotony instructions oluştur
            $antiMonotonyInstructions = $this->buildAntiMonotonyInstructions($finalTemplate);
            
            // Template-based response format instructions
            $formatInstructions = $this->buildFormatInstructions($finalTemplate);
            
            // Cache template build
            $this->cacheTemplateBuild($feature->slug, $finalTemplate);
            
            // Final prompt assembly
            return $this->assembleTemplatePrompt($antiMonotonyInstructions, $formatInstructions, $options);
            
        } catch (\Exception $e) {
            Log::warning('ResponseTemplateEngine failed', [
                'feature_slug' => $feature->slug,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to basic anti-monotony rules
            return $this->buildBasicAntiMonotonyPrompt();
        }
    }

    /**
     * 🔧 Parse feature's response template JSON
     */
    private function parseTemplate(AIFeature $feature): array
    {
        if (empty($feature->response_template)) {
            return [];
        }
        
        try {
            // response_template zaten array ise decode etme
            if (is_array($feature->response_template)) {
                $template = $feature->response_template;
            } else {
                $template = json_decode($feature->response_template, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('Invalid template JSON', [
                        'feature_slug' => $feature->slug,
                        'json_error' => json_last_error_msg()
                    ]);
                    return [];
                }
            }
            
            return $this->validateTemplate($template);
            
        } catch (\Exception $e) {
            Log::warning('Template parsing failed', [
                'feature_slug' => $feature->slug,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * 🎯 Detect template based on feature slug/name
     */
    private function detectFeatureTemplate(string $featureSlug): array
    {
        $slug = strtolower($featureSlug);
        
        // Direct mapping check
        foreach (self::FEATURE_TEMPLATE_MAPPINGS as $pattern => $template) {
            if (str_contains($slug, $pattern)) {
                return $template;
            }
        }
        
        // Keyword-based detection
        if (str_contains($slug, 'blog') || str_contains($slug, 'makale') || str_contains($slug, 'yazi')) {
            return self::FEATURE_TEMPLATE_MAPPINGS['blog'];
        }
        
        if (str_contains($slug, 'seo') || str_contains($slug, 'analiz') || str_contains($slug, 'rapor')) {
            return self::FEATURE_TEMPLATE_MAPPINGS['seo'];
        }
        
        if (str_contains($slug, 'kod') || str_contains($slug, 'code') || str_contains($slug, 'programlama')) {
            return self::FEATURE_TEMPLATE_MAPPINGS['kod'];
        }
        
        if (str_contains($slug, 'cevir') || str_contains($slug, 'translate') || str_contains($slug, 'dil')) {
            return self::FEATURE_TEMPLATE_MAPPINGS['ceviri'];
        }
        
        // Default fallback
        return [
            'format' => 'mixed',
            'style' => 'professional',
            'default_rules' => ['no_numbering', 'use_paragraphs', 'vary_structure']
        ];
    }

    /**
     * 🔀 Merge detected template with custom template
     */
    private function mergeTemplates(array $detected, array $custom): array
    {
        // Custom template takes precedence
        $merged = array_merge($detected, $custom);
        
        // Merge rules
        $detectedRules = $detected['default_rules'] ?? [];
        $customRules = $custom['rules'] ?? [];
        $merged['rules'] = array_unique(array_merge($detectedRules, $customRules));
        
        return $merged;
    }

    /**
     * 🚫 Build anti-monotony instructions
     */
    private function buildAntiMonotonyInstructions(array $template): string
    {
        $rules = $template['rules'] ?? ['no_numbering', 'use_paragraphs'];
        $instructions = [];
        
        $instructions[] = "=== YANIT FORMATI KURALLARI ===";
        $instructions[] = "⚠️ ÖNEMLİ: Monoton 1-2-3 formatından KAÇIN! Yaratıcı ve doğal format kullanın.";
        $instructions[] = "";
        
        foreach ($rules as $rule) {
            if (isset(self::ANTI_MONOTONY_RULES[$rule])) {
                $instructions[] = "🚫 " . strtoupper($rule). ": " . self::ANTI_MONOTONY_RULES[$rule];
            }
        }
        
        $instructions[] = "";
        $instructions[] = "✅ YAPIN: Akıcı paragraflar, doğal geçişler, çeşitli yapılar kullanın";
        $instructions[] = "❌ YAPMAYIN: 1., 2., 3. şeklinde sıralama, tekrarlı yapılar";
        
        return implode("\n", $instructions);
    }

    /**
     * 📝 Build format-specific instructions
     */
    private function buildFormatInstructions(array $template): string
    {
        $format = $template['format'] ?? 'mixed';
        $style = $template['style'] ?? 'professional';
        $sections = $template['sections'] ?? [];
        
        $instructions = [];
        $instructions[] = "=== YANIT YAPISINI OLUŞTUR ===";
        $instructions[] = "📋 Format Türü: " . strtoupper($format) . " (" . (self::FORMAT_TYPES[$format] ?? 'Karma format') . ")";
        $instructions[] = "🎭 Yazım Stili: " . strtoupper($style) . " (" . (self::STYLE_TYPES[$style] ?? 'Profesyonel ton') . ")";
        $instructions[] = "";
        
        // Format-specific instructions
        $instructions[] = $this->getFormatSpecificInstructions($format);
        
        // Section instructions if defined
        if (!empty($sections)) {
            $instructions[] = "📑 Bölüm Yapısı:";
            foreach ($sections as $index => $section) {
                $sectionTitle = $section['title'] ?? "Bölüm " . ($index + 1);
                $sectionType = $section['type'] ?? 'paragraph';
                $instructions[] = "  • {$sectionTitle} ({$sectionType})";
                
                if (!empty($section['instruction'])) {
                    $instructions[] = "    → " . $section['instruction'];
                }
            }
            $instructions[] = "";
        }
        
        return implode("\n", $instructions);
    }

    /**
     * 📋 Get format-specific detailed instructions
     */
    private function getFormatSpecificInstructions(string $format): string
    {
        return match($format) {
            'narrative' => "📖 AKIcı PARAGRAF FORMATI:\n  • Doğal paragraflar oluştur\n  • Geçişleri yumuşat\n  • Hikaye anlatır gibi yaz\n  • Numaralandırma kullanma",
            
            'structured' => "🏗️ YAPILANDIRILMIŞ FORMAT:\n  • Net başlıklar kullan\n  • Alt bölümler oluştur\n  • Tablo/liste karışımı\n  • Sonuç ve öneriler ekle",
            
            'code' => "💻 KOD FORMATI:\n  • Kod blokları kullan\n  • Açıklama + kod kombinasyonu\n  • Örnek kullanımlar göster\n  • Teknik detayları dahil et",
            
            'creative' => "🎨 YARATICI FORMAT:\n  • Özgün yaklaşım kullan\n  • Metaforlar ve analojiler\n  • Görsel öğeler öner\n  • Etkileşimli unsurlar",
            
            'table' => "📊 TABLO FORMATI:\n  • Markdown tabloları kullan\n  • Karşılaştırma odaklı\n  • Veriler net organize\n  • Özet bilgiler ekle",
            
            'mixed' => "🔀 KARMA FORMAT:\n  • Farklı format türleri\n  • Bölümlere göre uyarla\n  • Dinamik geçişler\n  • Çeşitlilik sağla",
            
            'technical' => "⚙️ TEKNİK FORMAT:\n  • Detaylı açıklamalar\n  • Adım adım yaklaşım\n  • Teknik terimler kullan\n  • Referanslar ekle",
            
            default => "📝 GENEL FORMAT:\n  • Doğal akış koru\n  • Okuyucu odaklı yaz\n  • Net ve anlaşılır ol\n  • Monotonluktan kaçın"
        };
    }

    /**
     * 📦 Assemble final template-aware prompt
     */
    private function assembleTemplatePrompt(string $antiMonotony, string $formatInstructions, array $options = []): string
    {
        $prompt = [];
        
        $prompt[] = $antiMonotony;
        $prompt[] = "---";
        $prompt[] = $formatInstructions;
        
        // Additional context if provided
        if (!empty($options['additional_context'])) {
            $prompt[] = "---";
            $prompt[] = "=== EK BAĞLAM ===";
            $prompt[] = $options['additional_context'];
        }
        
        $prompt[] = "---";
        $prompt[] = "🎯 SONUÇ: Yukarıdaki kurallara uygun, yaratıcı ve doğal bir yanıt üret!";
        
        return implode("\n\n", $prompt);
    }

    /**
     * ✅ Validate template structure
     */
    private function validateTemplate(array $template): array
    {
        // Required fields validation
        $validTemplate = [];
        
        // Format validation
        if (isset($template['format']) && in_array($template['format'], array_keys(self::FORMAT_TYPES))) {
            $validTemplate['format'] = $template['format'];
        }
        
        // Style validation
        if (isset($template['style']) && in_array($template['style'], array_keys(self::STYLE_TYPES))) {
            $validTemplate['style'] = $template['style'];
        }
        
        // Rules validation
        if (isset($template['rules']) && is_array($template['rules'])) {
            $validRules = [];
            foreach ($template['rules'] as $rule) {
                if (isset(self::ANTI_MONOTONY_RULES[$rule])) {
                    $validRules[] = $rule;
                }
            }
            $validTemplate['rules'] = $validRules;
        }
        
        // Sections validation
        if (isset($template['sections']) && is_array($template['sections'])) {
            $validTemplate['sections'] = $template['sections']; // Deep validation could be added
        }
        
        return $validTemplate;
    }

    /**
     * 💾 Cache template build for performance
     */
    private function cacheTemplateBuild(string $featureSlug, array $template): void
    {
        $cacheKey = "response_template:{$featureSlug}";
        Cache::put($cacheKey, $template, now()->addHours(24));
    }

    /**
     * 🔄 Fallback basic anti-monotony prompt
     */
    private function buildBasicAntiMonotonyPrompt(): string
    {
        return "=== YANIT FORMATI KURALLARI ===\n" .
               "⚠️ ÖNEMLİ: Monoton 1-2-3 formatından KAÇIN!\n\n" .
               "🚫 NO_NUMBERING: Otomatik numaralandırma yapma\n" .
               "🚫 USE_PARAGRAPHS: Paragraf formatını tercih et\n" .
               "🚫 VARY_STRUCTURE: Yapıyı değiştir, monotonluktan kaçın\n\n" .
               "✅ YAPIN: Akıcı paragraflar, doğal geçişler, çeşitli yapılar kullanın\n" .
               "❌ YAPMAYIN: 1., 2., 3. şeklinde sıralama, tekrarlı yapılar\n\n" .
               "🎯 SONUÇ: Yaratıcı ve doğal bir yanıt formatı kullan!";
    }

    /**
     * 🎯 STATIC METHOD: Quick template-aware prompt for external usage
     */
    public static function getQuickAntiMonotonyPrompt(string $featureSlug = ''): string
    {
        $engine = new self();
        
        if (empty($featureSlug)) {
            return $engine->buildBasicAntiMonotonyPrompt();
        }
        
        $detectedTemplate = $engine->detectFeatureTemplate($featureSlug);
        $antiMonotonyInstructions = $engine->buildAntiMonotonyInstructions($detectedTemplate);
        $formatInstructions = $engine->buildFormatInstructions($detectedTemplate);
        
        return $engine->assembleTemplatePrompt($antiMonotonyInstructions, $formatInstructions);
    }

    /**
     * 📊 Get template statistics for debugging
     */
    public function getTemplateStats(): array
    {
        return [
            'total_format_types' => count(self::FORMAT_TYPES),
            'total_style_types' => count(self::STYLE_TYPES),
            'total_anti_monotony_rules' => count(self::ANTI_MONOTONY_RULES),
            'total_feature_mappings' => count(self::FEATURE_TEMPLATE_MAPPINGS),
            'supported_formats' => array_keys(self::FORMAT_TYPES),
            'supported_styles' => array_keys(self::STYLE_TYPES),
        ];
    }
}