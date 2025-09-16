<?php

declare(strict_types=1);

namespace Modules\ThemeManagement\app\Services;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\AIPrompt;
use Modules\AI\App\Models\AICreditUsage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tenant;

/**
 * AI Content Generator Service
 *
 * Tema analizi ile uyumlu, AI destekli içerik üretimi sağlar.
 * Mevcut ai_prompts tablosunu kullanarak içerik şablonları yönetir.
 */
class AIContentGeneratorService
{
    private ThemeAnalyzerService $themeAnalyzer;
    private AIService $aiService;

    // Content Builder için özel prompt ID aralığı
    private const CONTENT_BUILDER_PROMPT_START = 5000;
    private const CREDIT_COSTS = [
        'simple' => 3,
        'moderate' => 5,
        'complex' => 10,
        'template' => 2
    ];

    public function __construct()
    {
        $this->themeAnalyzer = new ThemeAnalyzerService();
        $this->aiService = app(AIService::class);
    }

    /**
     * AI ile içerik üret
     */
    public function generateContent(array $params): array
    {
        try {
            Log::info('🚀 AIContentGeneratorService::generateContent başladı', $params);

            $tenantId = $params['tenant_id'] ?? tenant('id');
            $prompt = $params['prompt'] ?? '';
            $contentType = $params['content_type'] ?? 'auto';
            $length = $params['length'] ?? 'medium';
            $customInstructions = $params['custom_instructions'] ?? '';

            Log::info('📝 Parametreler hazırlandı', [
                'tenantId' => $tenantId,
                'prompt' => $prompt,
                'contentType' => $contentType,
                'length' => $length
            ]);

            // Tema analizini al
            Log::info('🎨 Tema analizi başlıyor...');
            $themeAnalysis = $this->themeAnalyzer->getThemePreview($tenantId);
            Log::info('✅ Tema analizi tamamlandı', [
                'framework' => $themeAnalysis['framework'] ?? 'unknown',
                'primary_color' => $themeAnalysis['primary_color'] ?? null,
                'has_dark_mode' => $themeAnalysis['has_dark_mode'] ?? false
            ]);

            // İçerik tipini belirle
            if ($contentType === 'auto') {
                $contentType = $this->detectContentType($prompt);
            }

            // Prompt'u hazırla
            Log::info('💭 AI prompt hazırlanıyor...');
            $finalPrompt = $this->buildPrompt([
                'user_prompt' => $prompt,
                'content_type' => $contentType,
                'theme_analysis' => $themeAnalysis,
                'length' => $length,
                'custom_instructions' => $customInstructions
            ]);
            Log::info('📄 AI prompt hazır', [
                'prompt_length' => strlen($finalPrompt),
                'first_100_chars' => substr($finalPrompt, 0, 100) . '...'
            ]);

            // AI'dan içerik üret - Doğrudan OpenAI servisi kullan
            Log::info('🤖 AI servisi çağrılıyor (Direct OpenAI)...');

            try {
                // OpenAI servisini doğrudan kullan - AIService bypass
                $openAIService = app(\Modules\AI\App\Services\OpenAIService::class);

                // Sadece user mesajı gönder, system prompt'suz
                $messages = [
                    [
                        'role' => 'user',
                        'content' => $finalPrompt
                    ]
                ];

                // OpenAI'ya doğrudan çağrı
                $aiResponse = $openAIService->ask($messages, false);

                Log::info('🔥 OpenAI Direct Response', [
                    'response_type' => gettype($aiResponse),
                    'is_array' => is_array($aiResponse),
                    'response_sample' => is_string($aiResponse) ? substr($aiResponse, 0, 200) : 'not string'
                ]);

                // Response'u string'e çevir
                if (is_array($aiResponse)) {
                    $aiResponse = $aiResponse['response'] ?? $aiResponse['content'] ?? json_encode($aiResponse);
                }

                // Markdown code block'larını temizle
                if (str_starts_with($aiResponse, '```html')) {
                    $aiResponse = str_replace('```html', '', $aiResponse);
                }
                if (str_starts_with($aiResponse, '```')) {
                    $aiResponse = preg_replace('/^```[a-z]*\n?/i', '', $aiResponse);
                }
                if (str_ends_with($aiResponse, '```')) {
                    $aiResponse = preg_replace('/```\s*$/i', '', $aiResponse);
                }

                // Trim whitespace
                $aiResponse = trim($aiResponse);
            } catch (\Exception $e) {
                Log::error('OpenAI Direct Call Error: ' . $e->getMessage());
                // Fallback olarak normal AIService kullan
                $aiResponse = $this->aiService->ask($finalPrompt, [
                    'temperature' => 0.7,
                    'max_tokens' => $this->getMaxTokens($length),
                    'context_type' => 'content_generation',
                    'skip_mode_override' => true,
                    'mode' => 'chat'
                ]);
            }
            Log::info('✅ AI yanıt alındı', [
                'response_length' => strlen($aiResponse),
                'has_content' => !empty($aiResponse),
                'first_200_chars' => substr($aiResponse, 0, 200) . '...'
            ]);

            // Kredi kullanımını kaydet
            Log::info('💰 Kredi kullanımı kaydediliyor...');
            $this->recordCreditUsage($tenantId, $contentType, $prompt);
            Log::info('✅ Kredi kullanımı kaydedildi');

            // HTML'i tema ile uyumlu hale getir
            Log::info('🧹 HTML işleniyor...');
            $processedContent = $this->processContent($aiResponse, $themeAnalysis);
            Log::info('✅ HTML işlendi', [
                'original_length' => strlen($aiResponse),
                'processed_length' => strlen($processedContent),
                'first_200_chars' => substr($processedContent, 0, 200) . '...'
            ]);

            $creditsUsed = $this->calculateCredits($contentType, $length);
            $result = [
                'success' => true,
                'content' => $processedContent,
                'credits_used' => $creditsUsed,
                'theme_matched' => true,
                'content_type' => $contentType,
                'meta' => [
                    'theme' => $themeAnalysis['theme_name'],
                    'framework' => $themeAnalysis['framework'],
                    'primary_color' => $themeAnalysis['primary_color']
                ]
            ];

            Log::info('🎉 İçerik üretimi BAŞARILI', [
                'final_content_length' => strlen($processedContent),
                'credits_used' => $creditsUsed,
                'metadata' => $result['meta'],
                'final_content_preview' => substr($processedContent, 0, 500)
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('❌ AI Content Generation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'İçerik üretilirken hata oluştu',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Hazır şablonları getir
     */
    public function getTemplates(): array
    {
        $cacheKey = 'ai_content_templates';

        return Cache::remember($cacheKey, 3600, function () {
            return [
                'hero' => [
                    'name' => 'Hero Section',
                    'description' => 'Etkileyici başlangıç bölümü',
                    'icon' => 'fa-flag',
                    'credits' => 2,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 1
                ],
                'features' => [
                    'name' => 'Özellikler',
                    'description' => '3-6 özellik kartı',
                    'icon' => 'fa-star',
                    'credits' => 3,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 2
                ],
                'pricing' => [
                    'name' => 'Fiyatlandırma',
                    'description' => 'Fiyat tabloları',
                    'icon' => 'fa-tag',
                    'credits' => 3,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 3
                ],
                'about' => [
                    'name' => 'Hakkımızda',
                    'description' => 'Şirket tanıtımı',
                    'icon' => 'fa-info-circle',
                    'credits' => 2,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 4
                ],
                'contact' => [
                    'name' => 'İletişim',
                    'description' => 'İletişim formu ve bilgileri',
                    'icon' => 'fa-envelope',
                    'credits' => 2,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 5
                ],
                'testimonials' => [
                    'name' => 'Referanslar',
                    'description' => 'Müşteri yorumları',
                    'icon' => 'fa-comment',
                    'credits' => 2,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 6
                ],
                'gallery' => [
                    'name' => 'Galeri',
                    'description' => 'Görsel galerisi',
                    'icon' => 'fa-images',
                    'credits' => 2,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 7
                ],
                'team' => [
                    'name' => 'Ekip',
                    'description' => 'Ekip üyeleri',
                    'icon' => 'fa-users',
                    'credits' => 3,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 8
                ],
                'faq' => [
                    'name' => 'S.S.S',
                    'description' => 'Sıkça sorulan sorular',
                    'icon' => 'fa-question-circle',
                    'credits' => 2,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 9
                ],
                'cta' => [
                    'name' => 'CTA',
                    'description' => 'Harekete geçirici bölüm',
                    'icon' => 'fa-bullhorn',
                    'credits' => 1,
                    'prompt_id' => self::CONTENT_BUILDER_PROMPT_START + 10
                ]
            ];
        });
    }

    /**
     * Şablondan içerik üret
     */
    public function generateFromTemplate(string $templateKey, array $params): array
    {
        $templates = $this->getTemplates();

        if (!isset($templates[$templateKey])) {
            return [
                'success' => false,
                'error' => 'Şablon bulunamadı'
            ];
        }

        $template = $templates[$templateKey];
        $params['content_type'] = $templateKey;
        $params['use_template'] = true;

        // Şablon promptunu al
        $templatePrompt = $this->getTemplatePrompt($template['prompt_id']);
        if ($templatePrompt) {
            $params['base_prompt'] = $templatePrompt;
        }

        return $this->generateContent($params);
    }

    /**
     * İçerik tipini otomatik tespit et
     */
    private function detectContentType(string $prompt): string
    {
        $prompt = Str::lower($prompt);

        $patterns = [
            'hero' => ['hero', 'başlangıç', 'giriş', 'ana bölüm'],
            'features' => ['özellik', 'feature', 'avantaj', 'fayda'],
            'pricing' => ['fiyat', 'paket', 'pricing', 'ücret'],
            'about' => ['hakkımızda', 'hakkında', 'about', 'biz kimiz'],
            'contact' => ['iletişim', 'contact', 'ulaş', 'adres'],
            'testimonials' => ['referans', 'yorum', 'testimonial', 'müşteri'],
            'gallery' => ['galeri', 'görsel', 'resim', 'gallery'],
            'team' => ['ekip', 'team', 'kadro', 'çalışan'],
            'faq' => ['sss', 'soru', 'faq', 'sorular'],
            'cta' => ['cta', 'aksiyon', 'harekete geç', 'call to action']
        ];

        foreach ($patterns as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($prompt, $keyword)) {
                    return $type;
                }
            }
        }

        return 'general';
    }

    /**
     * Prompt oluştur
     */
    private function buildPrompt(array $params): string
    {
        $userPrompt = $params['user_prompt'];
        $contentType = $params['content_type'];
        $themeAnalysis = $params['theme_analysis'];
        $length = $params['length'];
        $customInstructions = $params['custom_instructions'];

        // Temel prompt şablonu
        $basePrompt = $params['base_prompt'] ?? $this->getBasePrompt();

        // Tema bilgilerini ekle
        $themeContext = $this->buildThemeContext($themeAnalysis);

        // Uzunluk talimatları
        $lengthInstructions = $this->getLengthInstructions($length);

        // Final prompt
        $prompt = str_replace(
            ['{{user_prompt}}', '{{content_type}}', '{{theme_context}}', '{{length_instructions}}', '{{custom_instructions}}'],
            [$userPrompt, $contentType, $themeContext, $lengthInstructions, $customInstructions],
            $basePrompt
        );

        return $prompt;
    }

    /**
     * Tema bağlamını oluştur
     */
    private function buildThemeContext(array $themeAnalysis): string
    {
        $context = "TEMA BİLGİLERİ:\n";
        $context .= "- Framework: {$themeAnalysis['framework']}\n";
        $context .= "- Ana Renk: {$themeAnalysis['primary_color']}\n";
        $context .= "- İkincil Renk: {$themeAnalysis['secondary_color']}\n";
        $context .= "- Font: {$themeAnalysis['font_family']}\n";

        if ($themeAnalysis['has_dark_mode']) {
            $context .= "- Dark Mode: Destekleniyor\n";
        }

        if (!empty($themeAnalysis['components_available'])) {
            $context .= "- Kullanılabilir Componentler: " . implode(', ', $themeAnalysis['components_available']) . "\n";
        }

        // Framework'e özel talimatlar
        if ($themeAnalysis['framework'] === 'tailwind') {
            $context .= "\nTAILWIND CSS KURALLARI:\n";
            $context .= "- Utility class'ları kullan\n";
            $context .= "- Responsive prefix'leri ekle (sm:, md:, lg:)\n";
            $context .= "- Dark mode class'ları ekle (dark:)\n";
        } elseif ($themeAnalysis['framework'] === 'bootstrap') {
            $context .= "\nBOOTSTRAP KURALLARI:\n";
            $context .= "- Bootstrap grid sistemini kullan\n";
            $context .= "- Bootstrap component class'larını kullan\n";
            $context .= "- Responsive class'ları ekle\n";
        }

        return $context;
    }

    /**
     * İçeriği işle ve tema ile uyumlu hale getir
     */
    private function processContent(string $content, array $themeAnalysis): string
    {
        // Renkleri tema renkleriyle değiştir
        $content = $this->replaceColors($content, $themeAnalysis);

        // Framework'e göre class'ları düzenle
        if ($themeAnalysis['framework'] === 'tailwind') {
            $content = $this->processTailwindClasses($content);
        } elseif ($themeAnalysis['framework'] === 'bootstrap') {
            $content = $this->processBootstrapClasses($content);
        }

        // Alpine.js direktiflerini ekle
        $content = $this->addAlpineDirectives($content);

        // XSS temizliği
        $content = $this->sanitizeContent($content);

        return $content;
    }

    /**
     * Renkleri tema renkleriyle değiştir
     */
    private function replaceColors(string $content, array $themeAnalysis): string
    {
        // Genel renk değişimleri
        $colorMap = [
            '#3B82F6' => $themeAnalysis['primary_color'],
            '#6B7280' => $themeAnalysis['secondary_color'],
            'blue-500' => $this->colorToTailwind($themeAnalysis['primary_color']),
            'gray-500' => $this->colorToTailwind($themeAnalysis['secondary_color'])
        ];

        foreach ($colorMap as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    /**
     * Tailwind class'larını işle - Modern Tailwind 3.x
     */
    private function processTailwindClasses(string $content): string
    {
        // Modern Tailwind class replacements
        $modernReplacements = [
            // Spacing - Modern utilities
            'p-2' => 'p-2 sm:p-3 md:p-4 lg:p-6',
            'p-4' => 'p-4 sm:p-6 md:p-8 lg:p-10',
            'm-2' => 'm-2 sm:m-3 md:m-4',
            'space-y-2' => 'space-y-2 sm:space-y-3 md:space-y-4',
            'gap-2' => 'gap-2 sm:gap-3 md:gap-4 lg:gap-6',

            // Typography - Modern scale
            'text-sm' => 'text-sm sm:text-base',
            'text-lg' => 'text-lg sm:text-xl md:text-2xl',
            'text-xl' => 'text-xl sm:text-2xl md:text-3xl lg:text-4xl',
            'text-2xl' => 'text-2xl sm:text-3xl md:text-4xl lg:text-5xl',

            // Colors - Modern palette
            'bg-gray-100' => 'bg-gray-50 dark:bg-gray-900',
            'bg-gray-200' => 'bg-gray-100 dark:bg-gray-800',
            'text-gray-600' => 'text-gray-700 dark:text-gray-300',
            'text-gray-800' => 'text-gray-900 dark:text-gray-100',

            // Borders - Modern style
            'border' => 'border border-gray-200 dark:border-gray-700',
            'rounded' => 'rounded-lg',
            'shadow' => 'shadow-sm hover:shadow-md transition-shadow duration-200',

            // Flexbox/Grid - Modern layouts
            'flex' => 'flex flex-wrap',
            'grid' => 'grid gap-4 sm:gap-6',
            'grid-cols-2' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-2',
            'grid-cols-3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
            'grid-cols-4' => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
        ];

        foreach ($modernReplacements as $old => $new) {
            $content = str_replace("class=\"$old", "class=\"$new", $content);
            $content = str_replace(" $old ", " $new ", $content);
            $content = str_replace(" $old\"", " $new\"", $content);
        }

        // Container class - Modern responsive
        $content = str_replace(
            'class="container',
            'class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl',
            $content
        );

        // Buttons - Modern interactive states
        $content = preg_replace(
            '/class="([^"]*btn[^"]*)"/',
            'class="$1 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2"',
            $content
        );

        // Cards - Modern elevation
        $content = str_replace(
            'class="card',
            'class="card bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300',
            $content
        );

        // Dark mode support for all elements
        $content = preg_replace_callback('/class="([^"]*)"/', function($matches) {
            $classes = $matches[1];

            // Add dark mode variants
            if (strpos($classes, 'bg-') !== false && strpos($classes, 'dark:') === false) {
                if (strpos($classes, 'bg-white') !== false) {
                    $classes = str_replace('bg-white', 'bg-white dark:bg-gray-900', $classes);
                } elseif (preg_match('/bg-(\w+)-(\d+)/', $classes, $colorMatch)) {
                    $color = $colorMatch[1];
                    $shade = intval($colorMatch[2]);
                    $darkShade = max(100, 900 - $shade);
                    $classes .= " dark:bg-$color-$darkShade";
                }
            }

            if (strpos($classes, 'text-') !== false && strpos($classes, 'dark:') === false) {
                if (strpos($classes, 'text-black') !== false) {
                    $classes = str_replace('text-black', 'text-gray-900 dark:text-gray-100', $classes);
                } elseif (preg_match('/text-(\w+)-(\d+)/', $classes, $colorMatch)) {
                    $color = $colorMatch[1];
                    $shade = intval($colorMatch[2]);
                    $darkShade = max(100, 900 - $shade);
                    $classes .= " dark:text-$color-$darkShade";
                }
            }

            return "class=\"$classes\"";
        }, $content);

        // Animations - Modern micro-interactions
        $content = str_replace(
            '<button',
            '<button data-aos="fade-up" data-aos-duration="600"',
            $content
        );

        $content = str_replace(
            '<div class="card',
            '<div data-aos="fade-in" data-aos-duration="800" class="card',
            $content
        );

        return $content;
    }

    /**
     * Bootstrap class'larını işle
     */
    private function processBootstrapClasses(string $content): string
    {
        // Bootstrap 5 uyumluluğu
        $replacements = [
            'ml-' => 'ms-',
            'mr-' => 'me-',
            'pl-' => 'ps-',
            'pr-' => 'pe-'
        ];

        foreach ($replacements as $old => $new) {
            $content = str_replace($old, $new, $content);
        }

        return $content;
    }

    /**
     * Alpine.js direktiflerini ekle
     */
    private function addAlpineDirectives(string $content): string
    {
        // Interaktif elementlere Alpine.js ekle
        if (strpos($content, '<button') !== false) {
            $content = str_replace(
                '<button',
                '<button @click="handleClick"',
                $content
            );
        }

        // Accordion veya tab yapılarına x-show ekle
        if (strpos($content, 'accordion') !== false || strpos($content, 'tab') !== false) {
            $content = str_replace(
                '<div class="',
                '<div x-data="{ open: false }" class="',
                $content
            );
        }

        return $content;
    }

    /**
     * İçeriği temizle (XSS koruması)
     */
    private function sanitizeContent(string $content): string
    {
        // Script taglarını kaldır
        $content = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $content);

        // Tehlikeli attribute'ları kaldır
        $content = preg_replace('/on\w+="[^"]*"/i', '', $content);
        $content = preg_replace('/on\w+=\'[^\']*\'/i', '', $content);

        // Style içindeki expression'ları kaldır
        $content = preg_replace('/expression\s*\([^)]*\)/i', '', $content);

        return $content;
    }

    /**
     * Kredi kullanımını kaydet
     */
    private function recordCreditUsage(int $tenantId, string $contentType, string $prompt): void
    {
        try {
            $credits = $this->calculateCredits($contentType, 'medium');

            // Tenant kredi bakiyesini güncelle
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                $tenant->ai_credits_balance = max(0, $tenant->ai_credits_balance - $credits);
                $tenant->ai_last_used_at = now();
                $tenant->save();
            }

            // Kullanım logunu kaydet
            AICreditUsage::create([
                'tenant_id' => $tenantId,
                'user_id' => auth()->id() ?? 1,
                'feature_id' => 501, // Content Builder feature ID
                'credits_used' => $credits,
                'action' => 'content_generation',
                'details' => json_encode([
                    'content_type' => $contentType,
                    'prompt_length' => strlen($prompt),
                    'timestamp' => now()
                ]),
                'used_at' => now(),
                'created_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Credit usage recording failed: ' . $e->getMessage());
        }
    }

    /**
     * Kredi maliyetini hesapla
     */
    private function calculateCredits(string $contentType, string $length): int
    {
        $baseCredits = self::CREDIT_COSTS['moderate'];

        // İçerik tipine göre ayarla
        if (in_array($contentType, ['hero', 'cta'])) {
            $baseCredits = self::CREDIT_COSTS['simple'];
        } elseif (in_array($contentType, ['pricing', 'team', 'features'])) {
            $baseCredits = self::CREDIT_COSTS['complex'];
        }

        // Uzunluğa göre ayarla
        $lengthMultiplier = match($length) {
            'short' => 0.7,
            'long' => 1.5,
            default => 1.0
        };

        return (int) ceil($baseCredits * $lengthMultiplier);
    }

    /**
     * Maksimum token sayısını belirle
     */
    private function getMaxTokens(string $length): int
    {
        return match($length) {
            'short' => 500,
            'long' => 2000,
            default => 1000
        };
    }

    /**
     * Uzunluk talimatlarını al
     */
    private function getLengthInstructions(string $length): string
    {
        return match($length) {
            'short' => 'Kısa ve öz içerik üret. Maksimum 2-3 paragraf veya 3-4 element.',
            'long' => 'Detaylı ve kapsamlı içerik üret. 5-8 paragraf veya 6-10 element.',
            default => 'Orta boyutta içerik üret. 3-5 paragraf veya 4-6 element.'
        };
    }

    /**
     * Rengi Tailwind formatına çevir
     */
    private function colorToTailwind(string $hexColor): string
    {
        // Basit bir hex to tailwind dönüşümü
        $colors = [
            '#3B82F6' => 'blue-500',
            '#10B981' => 'green-500',
            '#EF4444' => 'red-500',
            '#F59E0B' => 'yellow-500',
            '#6B7280' => 'gray-500'
        ];

        return $colors[$hexColor] ?? 'blue-500';
    }

    /**
     * Temel prompt'u al - Modern Tailwind CSS
     */
    private function getBasePrompt(): string
    {
        return "HTML İÇERİK OLUŞTUR. Sen bir HTML kod üreticisisin. Sadece ve sadece HTML kodu döndür. Modern Tailwind CSS 3.x kullanarak HTML içerik üreteceksin.

{{theme_context}}

KULLANICI TALEBİ: {{user_prompt}}
İÇERİK TİPİ: {{content_type}}

{{length_instructions}}

{{custom_instructions}}

MODERN TAİLWİND KURALLARI:
1. Tailwind CSS 3.x utility class'larını kullan
2. Responsive prefix'leri kullan: sm:, md:, lg:, xl:, 2xl:
3. Dark mode desteği ekle: dark:bg-gray-900, dark:text-gray-100
4. Modern spacing kullan: space-y-4, gap-6, p-8
5. Gradient kullan: bg-gradient-to-r from-blue-500 to-purple-600
6. Hover/Focus state'leri ekle: hover:scale-105, focus:ring-2
7. Transition ekle: transition-all duration-300 ease-in-out
8. Modern shadow kullan: shadow-xl, shadow-2xl
9. Backdrop blur efektleri: backdrop-blur-sm
10. Grid ve Flexbox modern layout: grid grid-cols-1 md:grid-cols-3 gap-6

COMPONENT ÖRNEKLERİ:
- Card: <div class=\"bg-white dark:bg-gray-800 rounded-xl shadow-xl hover:shadow-2xl transition-shadow p-6\">
- Button: <button class=\"px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105\">
- Container: <div class=\"container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl\">
- Hero: <section class=\"relative min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900\">

İÇERİK YAPISI:
1. Her section için container kullan
2. İçerikleri modern card component'lere yerleştir
3. Görsel alanlar için aspect-ratio kullan: aspect-video, aspect-square
4. Icon'lar için Heroicons veya Font Awesome kullan
5. Animasyon için AOS (Animate On Scroll) data attribute'ları ekle

ÖNEMLİ: SADECE HTML KODU DÖNDÜR. Açıklama, prompt tekrarı veya başka bir şey EKLEME.
Doğrudan <section> veya <div> tagı ile başla ve HTML içerik üret.


ÖNEMLİ NOTLAR:
- Üretilen içerik çok kaliteli, profesyonel ve modern olmalı
- Karmaşık ve etkileyici tasarım kullan
- Gradient'ler ve gölge efektleri bol bol kullan
- İnteraktif hover efektleri ekle
- Görsel hiyerarşiyi sağla
- Dark mode için tüm elementlerin dark: alternatifini ekle
- Responsive tasarım zorunlu
- Modern web tasarım trendlerini takip et

SON KURAL: SADECE VE SADECE HTML KODU DÖNDÜR.
Açıklama yok, prompt tekrarı yok, yorum yok!
Doğrudan <section> ile başla ve ultra modern HTML üret.";
    }

    /**
     * Şablon promptunu al
     */
    private function getTemplatePrompt(int $promptId): ?string
    {
        $prompt = AIPrompt::where('prompt_id', $promptId)->first();
        return $prompt ? $prompt->content : null;
    }
}