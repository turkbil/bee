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
     * 🎨 ULTRA MİNİMAL KURALLAR - AI tamamen özgür!
     */
    public const BASIC_RULES = [
        'use_pdf_data'       => 'PDF\'deki TÜM veriyi kullan - hiçbirini atla',
        'premium_landing'    => 'ULTRA PREMIUM LANDING PAGE - çok uzun ve detaylı',
        'full_sections'      => 'Hero + Features + Specs + Gallery + About + CTA - minimum 6 section',
        'modern_design'      => 'Modern gradients, hover effects, glass morphism',
        'responsive_design'  => 'Mobile-first, dark mode, Tailwind CSS',
        'no_explanation'     => 'Hiç açıklama yapma, direkt HTML üret'
    ];

    /**
     * 🎯 EVRENSEL PATTERN DETECTION - 5 TEMEL TİP
     */
    public const UNIVERSAL_PATTERNS = [
        'SHOWCASE' => [
            'keywords' => ['product', 'service', 'feature', 'model', 'price', 'specifications', 'capacity', 'weight', 'size', 'technical', 'ürün', 'hizmet', 'özellik', 'fiyat', 'kapasite'],
            'structure' => 'Hero + Features + Gallery + Specs + CTA',
            'description' => 'Product/Service showcase'
        ],
        'INFORMATIVE' => [
            'keywords' => ['how', 'what', 'description', 'definition', 'information', 'guide', 'about', 'nasıl', 'nedir', 'açıklama', 'bilgi', 'rehber'],
            'structure' => 'Content blocks + FAQ + Steps',
            'description' => 'Information presentation'
        ],
        'COMPARISON' => [
            'keywords' => ['vs', 'versus', 'compare', 'comparison', 'table', 'chart', 'option', 'difference', 'karşı', 'tablo', 'seçenek'],
            'structure' => 'Tables + Charts + Side-by-side',
            'description' => 'Comparison layout'
        ],
        'COLLECTION' => [
            'keywords' => ['list', 'catalog', 'catalogue', 'variety', 'selection', 'gallery', 'items', 'collection', 'range', 'liste', 'katalog', 'çeşit', 'galeri'],
            'structure' => 'Grid + Cards + Filter',
            'description' => 'Multi-item collection'
        ],
        'PROCESS' => [
            'keywords' => ['step', 'process', 'procedure', 'workflow', 'stage', 'phase', 'instruction', 'adım', 'süreç', 'aşama', 'işlem'],
            'structure' => 'Timeline + Steps + Progress',
            'description' => 'Process explanation'
        ]
    ];

    /**
     * 📱 EXPANDED MODERN COMPONENT LIBRARY - 2024 PREMIUM STANDARDS
     */
    public const MODERN_CSS_PATTERNS = [
        // LAYOUT COMPONENTS
        'responsive_cards' => 'bg-white dark:bg-gray-800 rounded-2xl p-4 sm:p-6 md:p-8 hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/20 transition-all duration-300',
        'hero_sections' => 'min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-purple-900',
        'feature_cards' => 'group relative bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-8 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-gray-100 dark:border-gray-700',
        'pricing_cards' => 'relative bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 border-transparent hover:border-blue-500 hover:scale-105 transition-all duration-300 shadow-lg',
        'testimonial_cards' => 'bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300',

        // BUTTONS & INTERACTIONS
        'premium_buttons' => 'px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:scale-110 hover:shadow-xl active:scale-95 transition-all duration-200',
        'outline_buttons' => 'px-6 py-3 border-2 border-blue-600 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-600 hover:text-white hover:scale-105 transition-all duration-200',
        'ghost_buttons' => 'px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200',
        'floating_action' => 'fixed bottom-6 right-6 w-14 h-14 bg-blue-600 text-white rounded-full shadow-2xl hover:scale-110 hover:shadow-blue-500/50 transition-all duration-300',

        // TYPOGRAPHY & HEADINGS
        'responsive_headings' => 'text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent',
        'gradient_text' => 'bg-gradient-to-r from-purple-600 via-blue-600 to-teal-600 bg-clip-text text-transparent font-bold',
        'hero_text' => 'text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-tight',
        'subtitle_text' => 'text-lg sm:text-xl md:text-2xl text-gray-600 dark:text-gray-300 leading-relaxed',

        // LAYOUT & CONTAINERS
        'mobile_containers' => 'container mx-auto px-4 sm:px-6 md:px-8 lg:px-12 py-8 sm:py-12 md:py-16 lg:py-24',
        'section_spacing' => 'py-12 sm:py-16 md:py-20 lg:py-24 xl:py-32',
        'responsive_grids' => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 md:gap-8',
        'masonry_grid' => 'columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-6 space-y-6',
        'flexbox_center' => 'flex flex-col md:flex-row items-center justify-center gap-6 md:gap-12',

        // VISUAL EFFECTS
        'glass_morphism' => 'backdrop-blur-xl bg-white/10 dark:bg-gray-900/20 border border-white/20 dark:border-gray-700/30',
        'neon_glow' => 'shadow-lg shadow-blue-500/50 hover:shadow-2xl hover:shadow-blue-500/75 transition-shadow duration-300',
        'gradient_borders' => 'bg-gradient-to-r from-blue-500 to-purple-500 p-[2px] rounded-2xl',
        'floating_elements' => 'transform hover:-translate-y-1 hover:scale-105 transition-all duration-300',
        'parallax_bg' => 'bg-fixed bg-center bg-cover bg-no-repeat relative overflow-hidden',

        // MOBILE OPTIMIZATIONS
        'touch_targets' => 'min-h-[44px] min-w-[44px] flex items-center justify-center',
        'mobile_typography' => 'text-sm sm:text-base md:text-lg lg:text-xl leading-relaxed',
        'mobile_spacing' => 'space-y-4 sm:space-y-6 md:space-y-8 lg:space-y-12',
        'mobile_navigation' => 'fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-50',

        // DARK MODE & THEMING
        'dark_mode_transitions' => 'transition-colors duration-300 ease-in-out',
        'theme_aware_bg' => 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white',
        'theme_aware_borders' => 'border-gray-200 dark:border-gray-700',
        'theme_aware_text' => 'text-gray-600 dark:text-gray-300',

        // SPECIALTY COMPONENTS
        'timeline_item' => 'relative pl-8 pb-8 before:absolute before:left-0 before:top-2 before:w-4 before:h-4 before:bg-blue-500 before:rounded-full before:content-[""]',
        'progress_bar' => 'w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden',
        'badge_modern' => 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'image_overlay' => 'relative overflow-hidden rounded-2xl group before:absolute before:inset-0 before:bg-gradient-to-t before:from-black/50 before:to-transparent before:opacity-0 hover:before:opacity-100 before:transition-opacity before:duration-300',
        'stats_card' => 'text-center p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl border border-gray-100 dark:border-gray-600',

        // ANIMATION PRESETS
        'fade_in_up' => 'opacity-0 translate-y-8 animate-fade-in-up',
        'slide_in_left' => 'opacity-0 -translate-x-8 animate-slide-in-left',
        'bounce_in' => 'animate-bounce-in',
        'pulse_glow' => 'animate-pulse-glow',
        'rotating_border' => 'animate-rotating-border'
    ];

    /**
     * 🎭 Style types - Yazım tarzları
     */
    public const STYLE_TYPES = [
        'professional'   => 'Profesyonel, resmi ton',
        'casual'         => 'Gündelik, samimi ton',
        'academic'       => 'Akademik, bilimsel ton',
        'creative'       => 'Yaratıcı, artistik ton',
        'technical'      => 'Teknik, detaylı ton',
        'friendly'       => 'Arkadaşça, sıcak ton',
        'authoritative'  => 'Otoriter, güvenilir ton',
        'modern_premium' => 'Modern premium tasarım (gradients, glass morphism, ultra typography)'
    ];

    /**
     * 🏢 SECTOR-BASED COMPONENT PATTERNS - Sektör bazlı otomatik tema sistemi
     */
    public const SECTOR_PATTERNS = [
        'technology' => [
            'colors' => ['blue', 'purple', 'cyan', 'indigo'],
            'components' => ['hero_sections', 'feature_cards', 'stats_card', 'timeline_item', 'neon_glow'],
            'keywords' => ['technology', 'software', 'app', 'digital', 'innovation', 'AI', 'tech', 'startup', 'teknoloji', 'yazılım', 'uygulama'],
            'style' => 'modern_premium',
            'layout' => 'Hero + Features + Tech Specs + Demo + CTA'
        ],
        'healthcare' => [
            'colors' => ['green', 'teal', 'blue', 'emerald'],
            'components' => ['responsive_cards', 'testimonial_cards', 'stats_card', 'badge_modern'],
            'keywords' => ['health', 'medical', 'doctor', 'clinic', 'hospital', 'treatment', 'sağlık', 'doktor', 'tedavi', 'hastane'],
            'style' => 'professional',
            'layout' => 'Hero + Services + Doctors + Testimonials + Contact'
        ],
        'finance' => [
            'colors' => ['emerald', 'green', 'blue', 'slate'],
            'components' => ['pricing_cards', 'stats_card', 'timeline_item', 'progress_bar'],
            'keywords' => ['finance', 'bank', 'investment', 'money', 'credit', 'loan', 'finans', 'banka', 'yatırım', 'para'],
            'style' => 'professional',
            'layout' => 'Hero + Services + Pricing + Security + Trust Indicators'
        ],
        'education' => [
            'colors' => ['orange', 'yellow', 'blue', 'purple'],
            'components' => ['feature_cards', 'timeline_item', 'progress_bar', 'badge_modern'],
            'keywords' => ['education', 'school', 'course', 'learning', 'student', 'teacher', 'eğitim', 'okul', 'kurs', 'öğrenci'],
            'style' => 'friendly',
            'layout' => 'Hero + Courses + Instructors + Student Success + Enrollment'
        ],
        'ecommerce' => [
            'colors' => ['red', 'orange', 'purple', 'pink'],
            'components' => ['responsive_cards', 'pricing_cards', 'badge_modern', 'floating_elements'],
            'keywords' => ['shop', 'store', 'product', 'buy', 'sell', 'commerce', 'retail', 'mağaza', 'ürün', 'satış'],
            'style' => 'casual',
            'layout' => 'Hero + Featured Products + Categories + Reviews + Cart CTA'
        ],
        'restaurant' => [
            'colors' => ['orange', 'red', 'yellow', 'amber'],
            'components' => ['image_overlay', 'responsive_cards', 'testimonial_cards', 'stats_card'],
            'keywords' => ['restaurant', 'food', 'menu', 'dining', 'chef', 'cuisine', 'restoran', 'yemek', 'menü', 'şef'],
            'style' => 'creative',
            'layout' => 'Hero + Menu + Chef + Gallery + Reservations'
        ],
        'real_estate' => [
            'colors' => ['slate', 'gray', 'blue', 'green'],
            'components' => ['image_overlay', 'stats_card', 'responsive_cards', 'masonry_grid'],
            'keywords' => ['real estate', 'property', 'house', 'apartment', 'rent', 'buy', 'emlak', 'ev', 'kiralık', 'satılık'],
            'style' => 'professional',
            'layout' => 'Hero + Properties + Search + Agent + Contact'
        ],
        'agency' => [
            'colors' => ['purple', 'pink', 'blue', 'indigo'],
            'components' => ['hero_sections', 'feature_cards', 'testimonial_cards', 'timeline_item'],
            'keywords' => ['agency', 'creative', 'design', 'marketing', 'branding', 'ajans', 'kreatif', 'tasarım', 'pazarlama'],
            'style' => 'creative',
            'layout' => 'Hero + Services + Portfolio + Team + Contact'
        ],
        'fitness' => [
            'colors' => ['green', 'orange', 'red', 'yellow'],
            'components' => ['stats_card', 'timeline_item', 'progress_bar', 'feature_cards'],
            'keywords' => ['fitness', 'gym', 'workout', 'health', 'training', 'exercise', 'spor', 'antrenman', 'egzersiz'],
            'style' => 'friendly',
            'layout' => 'Hero + Programs + Trainers + Results + Membership'
        ],
        'consulting' => [
            'colors' => ['blue', 'slate', 'gray', 'indigo'],
            'components' => ['timeline_item', 'stats_card', 'testimonial_cards', 'feature_cards'],
            'keywords' => ['consulting', 'consultant', 'business', 'strategy', 'advisory', 'danışmanlık', 'iş', 'strateji'],
            'style' => 'professional',
            'layout' => 'Hero + Services + Expertise + Case Studies + Contact'
        ]
    ];

    /**
     * 🚫 Anti-monotony rules - Tekrarlı yapıları kıran kurallar
     */
    public const ANTI_MONOTONY_RULES = [
        'no_numbering'           => '1-2-3 şeklinde numaralandırma yapma',
        'use_paragraphs'         => 'Paragraf formatını tercih et',
        'vary_structure'         => 'Yapıyı değiştir, monotonluktan kaçın',
        'contextual_format'      => 'İçeriğe göre format belirle',
        'natural_flow'           => 'Doğal akış kullan',
        'avoid_bullets'          => 'Madde imlerinden kaçın',
        'mixed_formats'          => 'Farklı format türlerini karıştır',
        'dynamic_sections'       => 'Dinamik bölüm yapısı kullan',

        // Premium Landing Rules
        'sector_color_detection' => 'İçeriğe göre sektör tespit et ve renk paleti seç',
        'auto_sector_detect'     => 'Otomatik sektör analizi: endüstriyel→orange, teknoloji→blue, sağlık→teal',
        'glass_morphism'         => 'Glass morphism: bg-white/10 backdrop-blur-md kullan',
        'typography_hierarchy'   => 'Typography: text-4xl lg:text-5xl (hero), text-2xl lg:text-3xl (headings)',
        'breathing_space'        => 'Optimal spacing: py-8, py-12, py-16 (content-focused, efficient)',
        'premium_gradients'      => 'Modern gradients: bg-gradient-to-br from-[color] via-[color] to-[color]',
        'dark_mode_mandatory'    => 'Dark mode her element için zorunlu: dark:bg-gray-900, dark:text-white',
        'hover_interactions'     => 'Hover effects: hover:-translate-y-4 hover:shadow-2xl transition-all',
        'modern_curves'          => 'Modern curves: rounded-3xl everywhere, rounded-2xl minimum'
    ];

    /**
     * 🚀 MOBILE-FIRST RESPONSIVE MASTER PROMPT - 2024 STANDARDS
     */
    public static function generateResponsiveMasterPrompt(string $userInput, ?string $pdfContent = null, int $partNumber = 1): string
    {
        // 🎯 SON ÇARE: FULL EXAMPLE FORCING
        $basePrompt = "KOMPLE HTML SAYFA ÜRET! HİÇ YORUM YAPMA!\n\n";

        // 🚫 RESİM YASAĞI - HİÇBİR GÖRSEL KULLANMA!
        $basePrompt .= "⚠️ MUTLAK YASAK: <img>, background-image, hero.jpg, .png, .webp, .svg KULLANMA!\n";
        $basePrompt .= "⚠️ SADECE İKON: <i class=\"fas fa-xxx\"> kullan, hiç resim URL'i yazma!\n";
        $basePrompt .= "⚠️ GÖRSEL YOK: Hiçbir src=\"\" attribute'u kullanma!\n";
        $basePrompt .= "⚠️ PDF GÖRSELLER: PDF'deki görseller varsa SADECE AÇIKLAMA olarak kullan, HTML'e ekleme!\n\n";

        if ($pdfContent) {
            $basePrompt .= "PDF: {$pdfContent}\n\n";
        }

        $basePrompt .= "GÖREV: {$userInput}\n\n";

        // TAM 15 KART ÖRNEK - AI HİÇ SEÇENEK BIRAKMA!
        $basePrompt .= "AŞAĞI PATTERN'İ TAM KOPYALA, 15 KART YAZ:\n\n";

        // Pattern start
        $basePrompt .= "<section class=\"py-16\">\n";
        $basePrompt .= "<div class=\"container mx-auto px-4\">\n";
        $basePrompt .= "<h2 class=\"text-4xl font-bold text-center mb-12\">Özellikler</h2>\n";
        $basePrompt .= "<div class=\"grid md:grid-cols-2 lg:grid-cols-3 gap-8\">\n\n";

        // 3 tam örnek kart
        for ($i = 1; $i <= 3; $i++) {
            $basePrompt .= "<div class=\"bg-white dark:bg-gray-800 p-6 rounded-xl hover:scale-105 transition-all\">\n";
            $basePrompt .= "  <i class=\"fas fa-cog text-4xl text-blue-600 mb-4\"></i>\n";
            $basePrompt .= "  <h3 class=\"text-xl font-bold mb-2\">Özellik Başlık {$i}</h3>\n";
            $basePrompt .= "  <p class=\"mb-3\">Bu özellik hakkında en az elli kelimelik çok detaylı bir açıklama yazısı bulunmaktadır. Bu açıklama ürünün bu özelliğini derinlemesine tanımlayarak kullanıcıların tam olarak ne kazanacaklarını anlamalarını sağlar. Teknik detaylar, faydalar ve kullanım senaryoları hakkında kapsamlı bilgi verir.</p>\n";
            $basePrompt .= "  <p>İkinci paragraf olarak ek detaylar ve özelliğin diğer sistemlerle entegrasyonu hakkında bilgiler yer alır. Bu kısım da yeterli uzunlukta olmalı.</p>\n";
            $basePrompt .= "</div>\n\n";
        }

        $basePrompt .= "<!-- AYNI PATTERN İLE 12 KART DAHA YAZ! TOPLAM 15 KART OLACAK! -->\n\n";
        $basePrompt .= "</div></div></section>\n\n";

        // 5 tam örnek tablo satırı
        $basePrompt .= "AŞAĞI TABLO PATTERN'İ TAM KOPYALA, 20 SATIR YAZ:\n\n";
        $basePrompt .= "<section class=\"py-16\">\n";
        $basePrompt .= "<div class=\"container mx-auto px-4\">\n";
        $basePrompt .= "<h2 class=\"text-4xl font-bold text-center mb-12\">Teknik Özellikler</h2>\n";
        $basePrompt .= "<table class=\"w-full\">\n";
        $basePrompt .= "<thead><tr><th class=\"p-4 text-left\">Özellik</th><th class=\"p-4 text-left\">Değer</th></tr></thead>\n";
        $basePrompt .= "<tbody>\n\n";

        for ($i = 1; $i <= 5; $i++) {
            $basePrompt .= "<tr>\n";
            $basePrompt .= "  <td class=\"p-4 font-medium\">Teknik Özellik {$i}</td>\n";
            $basePrompt .= "  <td class=\"p-4\">Bu teknik özelliğin değeri hakkında en az on beş kelimelik detaylı açıklama ve teknik spesifikasyon bilgileri ile birlikte performans metrikleri</td>\n";
            $basePrompt .= "</tr>\n\n";
        }

        $basePrompt .= "<!-- AYNI PATTERN İLE 15 SATIR DAHA YAZ! TOPLAM 20 SATIR OLACAK! -->\n\n";
        $basePrompt .= "</tbody></table>\n";
        $basePrompt .= "</div></section>\n\n";

        // DİL ZORLAMA - Tenant locale'e göre
        $tenantLocale = app()->getLocale() ?? 'tr';
        $languageMap = [
            'tr' => 'TÜRKÇE',
            'en' => 'ENGLISH',
            'de' => 'DEUTSCH',
            'fr' => 'FRANÇAIS'
        ];

        $targetLanguage = $languageMap[$tenantLocale] ?? 'TÜRKÇE';
        $basePrompt .= "DİL ZORLAMA: TÜM METİNLER {$targetLanguage} DİLİNDE OLACAK!\n";
        $basePrompt .= "BAŞLIKLAR, AÇIKLAMALAR, TÜM İÇERİK {$targetLanguage}!\n\n";

        $basePrompt .= "KOMPLE SAYFA ÜRET! YORUM DEĞİL, KOD!";

        return $basePrompt;
    }

    /**
     * 🚀 LEGACY MINIMAL PROMPT - Kullanılmıyor artık
     * @deprecated Use generateResponsiveMasterPrompt instead
     */
    public static function generateMinimalPrompt(string $userInput, ?string $pdfContent = null): string
    {
        return self::generateResponsiveMasterPrompt($userInput, $pdfContent);
    }

    /**
     * 🎯 EVRENSEL PATTERN DETECTION - PDF yapısına göre otomatik seçim
     */
    public static function detectUniversalPattern(string $content): string
    {
        $content = strtolower($content);
        $scores = [];

        // Her pattern için score hesapla
        foreach (self::UNIVERSAL_PATTERNS as $patternName => $pattern) {
            $score = 0;
            foreach ($pattern['keywords'] as $keyword) {
                $score += substr_count($content, strtolower($keyword));
            }
            $scores[$patternName] = $score;
        }

        // En yüksek score'u alan pattern'i dön
        $detectedPattern = array_key_first($scores);
        $maxScore = 0;

        foreach ($scores as $pattern => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $detectedPattern = $pattern;
            }
        }

        // Score çok düşükse default SHOWCASE
        return $maxScore > 0 ? $detectedPattern : 'SHOWCASE';
    }

    /**
     * 🎨 Pattern page içeriğinden layout pattern tespit et
     */
    public static function detectPatternPageStructure(string $patternContent): array
    {
        $structure = [
            'sections' => [],
            'layout_type' => 'unknown',
            'css_classes' => [],
            'components' => []
        ];

        try {
            // Section'ları tespit et
            if (preg_match_all('/<section[^>]*class="([^"]*)"/i', $patternContent, $matches)) {
                $structure['sections'] = array_unique($matches[1]);
            }

            // Grid layout tespit et
            if (str_contains($patternContent, 'grid-cols')) {
                $structure['layout_type'] = 'grid';
            } elseif (str_contains($patternContent, 'flex')) {
                $structure['layout_type'] = 'flex';
            }

            // Component tiplerini tespit et
            if (str_contains($patternContent, 'card')) {
                $structure['components'][] = 'cards';
            }
            if (str_contains($patternContent, 'table')) {
                $structure['components'][] = 'tables';
            }
            if (str_contains($patternContent, 'form')) {
                $structure['components'][] = 'forms';
            }

            // CSS sınıflarını çıkar
            if (preg_match_all('/class="([^"]*)"/i', $patternContent, $matches)) {
                $allClasses = [];
                foreach ($matches[1] as $classList) {
                    $allClasses = array_merge($allClasses, explode(' ', $classList));
                }
                $structure['css_classes'] = array_unique(array_filter($allClasses));
            }

            \Log::info('🎨 Pattern page yapısı analiz edildi', $structure);

        } catch (\Exception $e) {
            \Log::error('Pattern page analizi başarısız', ['error' => $e->getMessage()]);
        }

        return $structure;
    }

    /**
     * 🚀 PATTERN-AWARE RESPONSIVE MASTER PROMPT
     */
    public static function generatePatternAwarePrompt(string $userInput, ?string $pdfContent = null): string
    {
        // 🏢 SECTOR DETECTION - Sektör bazlı otomatik tema
        $detectedSector = self::detectSector($userInput . ' ' . ($pdfContent ?? ''));

        // Pattern detection
        $detectedPattern = 'SHOWCASE'; // default
        $isPatternPage = str_contains($userInput, '[PATTERN KULLANIMI]');

        if ($pdfContent && !$isPatternPage) {
            $detectedPattern = self::detectUniversalPattern($pdfContent);
        } elseif ($isPatternPage) {
            // Pattern page kullanımında farklı approach
            $detectedPattern = 'PATTERN_BASED';
        }

        $patternInfo = self::UNIVERSAL_PATTERNS[$detectedPattern] ?? [
            'structure' => 'Pattern-based custom structure',
            'description' => 'Custom pattern from existing page'
        ];

        // Base responsive prompt'u al
        $basePrompt = self::generateResponsiveMasterPrompt($userInput, $pdfContent);

        // 🎨 SECTOR-BASED THEME INTEGRATION
        if ($detectedSector && $detectedSector !== 'general') {
            $sectorTheme = self::applySectorTheme($detectedSector);
            $basePrompt .= "\n\n🏢 SECTOR THEME APPLIED: {$detectedSector}\n";
            $basePrompt .= $sectorTheme;
        }

        // Pattern-specific instructions ekle
        if ($isPatternPage) {
            $patternPrompt = "\n\n🎨 DETECTED: PATTERN PAGE USAGE\n";
            $patternPrompt .= "📋 APPROACH: Pattern-based structure cloning\n";
            $patternPrompt .= "📖 TYPE: Custom pattern from existing page\n\n";
        } else {
            $patternPrompt = "\n\n🎯 DETECTED PATTERN: {$detectedPattern}\n";
            $patternPrompt .= "📋 STRUCTURE: {$patternInfo['structure']}\n";
            $patternPrompt .= "📖 TYPE: {$patternInfo['description']}\n\n";
        }

        // Pattern'e göre özel talimatlar
        if ($isPatternPage) {
            $patternPrompt .= "🎨 PATTERN PAGE CLONING RULES:\n";
            $patternPrompt .= "• Exact structure: Verilen HTML yapısını TAKİP ET\n";
            $patternPrompt .= "• CSS classes: Aynı Tailwind sınıflarını KULLAN\n";
            $patternPrompt .= "• Layout preservation: Düzen ve sıralamayi KORU\n";
            $patternPrompt .= "• Component mirroring: Card, grid, form yapılarını AYNI ŞEKİLDE tekrarla\n";
            $patternPrompt .= "• Content adaptation: İçeriği değiştir ama yapıyı KORU\n";
            $patternPrompt .= "• Responsive consistency: dark: sınıfları da aynı şekilde UYGULa\n";
        } else {
            switch ($detectedPattern) {
                case 'SHOWCASE':
                    $patternPrompt .= "🎨 SHOWCASE PATTERN RULES:\n";
                    $patternPrompt .= "• Hero section: Ürün/hizmet highlight\n";
                    $patternPrompt .= "• Features grid: Özellik kartları\n";
                    $patternPrompt .= "• Gallery: Görsel showcase\n";
                    $patternPrompt .= "• Specifications: Detaylı teknik tablo\n";
                    $patternPrompt .= "• Strong CTA: Satış odaklı çağrı\n";
                    break;

                case 'INFORMATIVE':
                    $patternPrompt .= "📚 INFORMATIVE PATTERN RULES:\n";
                    $patternPrompt .= "• Content blocks: Bilgi bölümleri\n";
                    $patternPrompt .= "• FAQ section: Sık sorulan sorular\n";
                    $patternPrompt .= "• Step-by-step: Açıklayıcı adımlar\n";
                    $patternPrompt .= "• Educational tone: Öğretici yaklaşım\n";
                    break;

                case 'COMPARISON':
                    $patternPrompt .= "⚖️ COMPARISON PATTERN RULES:\n";
                    $patternPrompt .= "• Comparison tables: Karşılaştırma tabloları\n";
                    $patternPrompt .= "• Side-by-side layout: Yan yana gösterim\n";
                    $patternPrompt .= "• Charts/graphs: Görsel karşılaştırma\n";
                    $patternPrompt .= "• Pros/cons: Avantaj/dezavantaj\n";
                    break;

                case 'COLLECTION':
                    $patternPrompt .= "🗂️ COLLECTION PATTERN RULES:\n";
                    $patternPrompt .= "• Grid layout: Kart tabanlı düzen\n";
                    $patternPrompt .= "• Filter options: Filtreleme seçenekleri\n";
                    $patternPrompt .= "• Category grouping: Kategori grupları\n";
                    $patternPrompt .= "• Gallery view: Galeri görünümü\n";
                    break;

                case 'PROCESS':
                    $patternPrompt .= "🔄 PROCESS PATTERN RULES:\n";
                    $patternPrompt .= "• Timeline layout: Zaman çizelgesi\n";
                    $patternPrompt .= "• Step progression: Adım ilerlemesi\n";
                    $patternPrompt .= "• Process flow: Süreç akışı\n";
                    $patternPrompt .= "• Progress indicators: İlerleme göstergeleri\n";
                    break;
            }
        }

        // Complete content requirement ekle
        $patternPrompt .= "\n\n🚫 KESİN YASAKLAR!\n";
        $patternPrompt .= "• text-7xl, text-8xl, text-6xl YASAK! Max text-5xl\n";
        $patternPrompt .= "• min-h-screen YASAK! Sadece pt-20 kullan\n";
        $patternPrompt .= "• py-32, py-40 YASAK! Max py-20\n";
        $patternPrompt .= "• Placeholder YASAK! '<!-- ... -->' yok\n";
        $patternPrompt .= "• Duplicate x-data YASAK! Tek x-data per element\n\n";

        $patternPrompt .= "📋 ZORUNLU İÇERİK MİNİMUMLARI:\n";
        $patternPrompt .= "• Feature cards: tam 6 adet (daha az değil!)\n";
        $patternPrompt .= "• Teknik tablo: tam 8 satır (daha az değil!)\n";
        $patternPrompt .= "• Applications section: kullanım alanları\n";
        $patternPrompt .= "• Safety section: güvenlik özellikleri\n";
        $patternPrompt .= "• Battery section: Li-ion detayları\n";
        $patternPrompt .= "• Contact section: iletişim formu\n\n";

        $patternPrompt .= "🎯 BOYUT STANDARTLARI (ZORUNLU!):\n";
        $patternPrompt .= "• Hero h1: SADECE text-4xl lg:text-5xl\n";
        $patternPrompt .= "• Section h2: SADECE text-3xl lg:text-4xl\n";
        $patternPrompt .= "• Card h3: SADECE text-xl lg:text-2xl\n";
        $patternPrompt .= "• Body text: SADECE text-base lg:text-lg\n";
        $patternPrompt .= "• text-6xl ASLA KULLANMA!\n\n";

        $patternPrompt .= "⚡ İÇERİK SAYAÇLARI (KONTROL ET!):\n";
        $patternPrompt .= "• Feature cards: tam 6 tane yaz (saymayı unutma!)\n";
        $patternPrompt .= "• Table rows: tam 8 tane yaz (saymayı unutma!)\n";
        $patternPrompt .= "• Her feature card için: ikon + başlık + açıklama\n";
        $patternPrompt .= "• Her table row için: özellik + değer\n";
        $patternPrompt .= "• Applications section: 4 kullanım alanı\n";
        $patternPrompt .= "• Safety features: 3 güvenlik özelliği\n\n";

        $patternPrompt .= "📏 REFERANS BOYUTLAR (transpalet.com):\n";
        $patternPrompt .= "• Normal başlık: text-2xl (24px)\n";
        $patternPrompt .= "• Büyük başlık: text-4xl (36px)\n";
        $patternPrompt .= "• Hero başlık: text-5xl MAX (48px)\n";
        $patternPrompt .= "• Normal text: text-base (16px)\n";
        $patternPrompt .= "• Büyük text: text-lg (18px)\n\n";

        // Pattern page kullanımında özel uyarılar
        if ($isPatternPage) {
            $patternPrompt .= "\n🎨 PATTERN KLONLAMA UYARILARI:\n";
            $patternPrompt .= "PATTERN sayfa yapısını TAMAMEN koru!\n";
            $patternPrompt .= "CSS sınıflarını DEĞİŞTİRME!\n";
            $patternPrompt .= "Section sıralamasını KORU!\n";
            $patternPrompt .= "Sadece içeriği ADAPTE ET!";
        } else {
            $patternPrompt .= "\n🔥 BÜYÜK BOYUT = SİSTEM HATASI! 🔥\n";
            $patternPrompt .= "❌ text-6xl, lg:text-6xl kullanma = HATA!\n";
            $patternPrompt .= "❌ İkonlarda lg:text-5xl, lg:text-6xl = HATA!\n";
            $patternPrompt .= "✅ İkonlar MAX lg:text-4xl\n";
            $patternPrompt .= "✅ Hero MAX text-5xl, Normal MAX text-4xl\n";
            $patternPrompt .= "❌ Template'leri AYNEN uygula!\n";
            $patternPrompt .= "❌ MIN 8 tablo + MIN 6 kart ZORUNLU!";
        }

        return $basePrompt . $patternPrompt;
    }

    /**
     * 📊 Pattern page için özel prompt generator
     */
    public static function generatePatternPagePrompt(string $userInput, string $patternContent): string
    {
        $prompt = "🎨 PATTERN PAGE KLONLAMA - MASTER TEMPLATE\n\n";

        $prompt .= "🎯 GÖREV: Verilen pattern sayfasının yapısını kullanarak yeni içerik oluştur\n\n";

        $prompt .= "📋 TEMEL PRENSİPLER:\n";
        $prompt .= "• HTML yapısını AYNEN takip et\n";
        $prompt .= "• CSS sınıflarını DEĞİŞTİRME\n";
        $prompt .= "• Section sıralamasını KORU\n";
        $prompt .= "• Component düzenini TEKRARLA\n";
        $prompt .= "• Sadece TEXT içeriğini ADAPTE ET\n\n";

        // Pattern page analizi
        $structure = self::detectPatternPageStructure($patternContent);

        $prompt .= "🔍 PATTERN ANALİZ SONUCU:\n";
        $prompt .= "Layout Tipi: {$structure['layout_type']}\n";
        $prompt .= "Component'ler: " . implode(', ', $structure['components']) . "\n";
        $prompt .= "Section Sayısı: " . count($structure['sections']) . "\n\n";

        $prompt .= "=== PATTERN SAYFA İÇERİĞİ ===\n";
        $prompt .= $patternContent . "\n";
        $prompt .= "=== PATTERN SONU ===\n\n";

        $prompt .= "🎯 KULLANICI TALEBİ: {$userInput}\n\n";

        $prompt .= "🚀 ÇIKTI KURALLARI:\n";
        $prompt .= "• Yukarıdaki pattern'in AYNI yapısını kullan\n";
        $prompt .= "• AYNI CSS sınıflarını uygula\n";
        $prompt .= "• AYNI component tiplerini tekrarla\n";
        $prompt .= "• İçeriği kullanıcı talep basina adapte et\n";
        $prompt .= "• Dark mode sınıflarını da KORU\n";
        $prompt .= "• Responsive breakpoint'leri DEĞİŞTİRME\n\n";

        $prompt .= "SONUÇ: Temiz HTML kodu, açıklama yok!";

        return $prompt;
    }

    /**
     * 🎨 BASIT SEKTÖR BAZLI PROMPT - Template'siz özgür AI
     *
     * @param string $pdfContent - PDF içeriği
     * @param array $options - Ek seçenekler
     * @return string - Sektörel renk + özgür format prompt
     */
    public function buildSectorBasedPrompt(string $pdfContent, array $options = []): string
    {
        try {
            // PDF içeriğinden sektör tespit et
            $detectedSector = $this->detectSector($pdfContent);
            $sectorColors = self::SECTOR_COLORS[$detectedSector] ?? self::SECTOR_COLORS['varsayılan'];

            Log::info('🎯 Sektör tespit edildi', [
                'detected_sector' => $detectedSector,
                'colors' => $sectorColors
            ]);

            // Özgür AI prompt oluştur
            $prompt = $this->buildFreeformPrompt($detectedSector, $sectorColors, $options);

            return $prompt;

        } catch (\Exception $e) {
            Log::warning('Sector detection failed', [
                'error' => $e->getMessage()
            ]);

            // Fallback: varsayılan renkler
            return $this->buildFreeformPrompt('varsayılan', self::SECTOR_COLORS['varsayılan'], $options);
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
            'corporate' => "🏢 KURUMSAL FORMAT:\n  • Hero section + Özellikler + Teknik specs tablo\n  • Profesyonel ton, temiz tasarım\n  • Specifications tablolar ağırlıklı\n  • Dark mode + modern gradients",

            'product' => "📦 ÜRÜN FORMAT:\n  • Ürün gösterimi odaklı\n  • Features grid + karşılaştırma\n  • Teknik özellikler prominent\n  • Hover efektleri + CTA butonlar",

            'creative' => "🎨 YARATICI FORMAT:\n  • Görsel ağırlıklı yaklaşım\n  • Interaktif unsurlar\n  • Asimetrik grid layout\n  • Bold typography + animasyonlar",

            'technical' => "⚙️ TEKNİK FORMAT:\n  • Detaylı dokümantasyon formatı\n  • Kod blokları + açıklamalar\n  • Adım adım yaklaşım\n  • Referans tabloları",

            'mixed' => "🔀 KARMA FORMAT:\n  • Farklı format türleri\n  • Bölümlere göre uyarla\n  • Dinamik geçişler\n  • Çeşitlilik sağla",

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
            'total_style_types' => count(self::STYLE_TYPES),
            'total_anti_monotony_rules' => count(self::ANTI_MONOTONY_RULES),
            'total_universal_patterns' => count(self::UNIVERSAL_PATTERNS),
            'supported_patterns' => array_keys(self::UNIVERSAL_PATTERNS),
            'supported_styles' => array_keys(self::STYLE_TYPES),
        ];
    }

    /**
     * 🎨 Pattern page listesi için utility method
     */
    public static function getAvailablePatternPages(): array
    {
        try {
            // Page modülünden aktif sayfaları al
            $pages = \Modules\Page\App\Models\Page::where('is_active', true)
                ->get(['page_id', 'title', 'slug'])
                ->map(function ($page) {
                    $currentLocale = app()->getLocale();
                    return [
                        'id' => $page->page_id,
                        'title' => $page->getTranslated('title', $currentLocale) ?? 'Untitled',
                        'slug' => $page->getTranslated('slug', $currentLocale) ?? '',
                    ];
                })
                ->toArray();

            return $pages;

        } catch (\Exception $e) {
            \Log::error('Pattern page listesi alınamadı', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * 🏢 SECTOR DETECTION - İçerikten sektör algılama
     */
    public static function detectSector(string $content): string
    {
        $content = strtolower($content);

        // Sektör bazlı skorlama
        $sectorScores = [];

        foreach (self::SECTOR_PATTERNS as $sector => $pattern) {
            $score = 0;

            // Keyword matching
            foreach ($pattern['keywords'] as $keyword) {
                if (strpos($content, strtolower($keyword)) !== false) {
                    $score += 1;
                }
            }

            // Bonus for multiple keyword matches
            if ($score > 1) {
                $score *= 1.5;
            }

            $sectorScores[$sector] = $score;
        }

        // En yüksek skorlu sektörü seç
        $detectedSector = array_keys($sectorScores, max($sectorScores))[0] ?? 'general';

        // Minimum threshold kontrolü
        if (max($sectorScores) < 1) {
            $detectedSector = 'general';
        }

        Log::info('🏢 Sector Detection', [
            'detected_sector' => $detectedSector,
            'scores' => $sectorScores,
            'content_length' => strlen($content)
        ]);

        return $detectedSector;
    }

    /**
     * 🎨 SECTOR THEME APPLICATION - Sektör temasını uygula
     */
    public static function applySectorTheme(string $sector): string
    {
        if (!isset(self::SECTOR_PATTERNS[$sector])) {
            return '';
        }

        $pattern = self::SECTOR_PATTERNS[$sector];

        $themePrompt = "🎨 SECTOR-SPECIFIC DESIGN THEME:\n";
        $themePrompt .= "• SECTOR: " . ucfirst($sector) . "\n";
        $themePrompt .= "• COLOR PALETTE: " . implode(', ', $pattern['colors']) . " (use these Tailwind colors)\n";
        $themePrompt .= "• STYLE TONE: " . $pattern['style'] . "\n";
        $themePrompt .= "• LAYOUT STRUCTURE: " . $pattern['layout'] . "\n";
        $themePrompt .= "• COMPONENT FOCUS: " . implode(', ', $pattern['components']) . "\n\n";

        // Sector-specific component instructions
        $themePrompt .= "🏗️ COMPONENT IMPLEMENTATION:\n";
        foreach ($pattern['components'] as $component) {
            if (isset(self::MODERN_CSS_PATTERNS[$component])) {
                $themePrompt .= "• {$component}: " . self::MODERN_CSS_PATTERNS[$component] . "\n";
            }
        }

        // Color-specific gradient instructions
        $primaryColor = $pattern['colors'][0];
        $secondaryColor = $pattern['colors'][1] ?? $pattern['colors'][0];

        $themePrompt .= "\n🌈 COLOR IMPLEMENTATION:\n";
        $themePrompt .= "• Primary gradients: from-{$primaryColor}-500 to-{$secondaryColor}-600\n";
        $themePrompt .= "• Button colors: bg-{$primaryColor}-600 hover:bg-{$primaryColor}-700\n";
        $themePrompt .= "• Accent colors: text-{$primaryColor}-600, border-{$primaryColor}-500\n";
        $themePrompt .= "• Dark mode: dark:from-{$primaryColor}-600 dark:to-{$secondaryColor}-700\n\n";

        // Style-specific instructions
        $styleInstructions = self::getStyleInstructions($pattern['style']);
        $themePrompt .= $styleInstructions;

        return $themePrompt;
    }

    /**
     * 📝 STYLE-SPECIFIC INSTRUCTIONS
     */
    private static function getStyleInstructions(string $style): string
    {
        $instructions = [
            'modern_premium' => "✨ MODERN PREMIUM STYLE:\n• Use glass morphism effects\n• Add subtle animations\n• Gradient text and backgrounds\n• Ultra-clean typography\n• Minimal spacing and padding\n",

            'professional' => "💼 PROFESSIONAL STYLE:\n• Clean, minimalist design\n• Consistent spacing\n• Conservative color palette\n• Clear hierarchy\n• Trust-building elements\n",

            'creative' => "🎨 CREATIVE STYLE:\n• Bold color combinations\n• Unique layouts\n• Artistic elements\n• Experimental typography\n• Visual storytelling\n",

            'friendly' => "😊 FRIENDLY STYLE:\n• Warm colors\n• Rounded corners\n• Playful elements\n• Conversational tone\n• Approachable design\n",

            'casual' => "👕 CASUAL STYLE:\n• Relaxed layout\n• Informal elements\n• Fun interactions\n• Comfortable spacing\n• Approachable aesthetic\n",

            'technical' => "⚙️ TECHNICAL STYLE:\n• Grid-based layout\n• Data visualization\n• Code-friendly fonts\n• Precise spacing\n• Function-focused design\n"
        ];

        return $instructions[$style] ?? "🔧 GENERAL STYLE: Apply balanced, versatile design principles\n";
    }

    /**
     * 📊 ENHANCED TEMPLATE STATS - Sektör ve component bilgileri dahil
     */
    public function getEnhancedTemplateStats(): array
    {
        return [
            'total_style_types' => count(self::STYLE_TYPES),
            'total_anti_monotony_rules' => count(self::ANTI_MONOTONY_RULES),
            'total_universal_patterns' => count(self::UNIVERSAL_PATTERNS),
            'total_sector_patterns' => count(self::SECTOR_PATTERNS),
            'total_css_components' => count(self::MODERN_CSS_PATTERNS),
            'supported_patterns' => array_keys(self::UNIVERSAL_PATTERNS),
            'supported_styles' => array_keys(self::STYLE_TYPES),
            'supported_sectors' => array_keys(self::SECTOR_PATTERNS),
            'component_categories' => [
                'layout' => ['responsive_cards', 'hero_sections', 'feature_cards', 'pricing_cards'],
                'buttons' => ['premium_buttons', 'outline_buttons', 'ghost_buttons', 'floating_action'],
                'typography' => ['responsive_headings', 'gradient_text', 'hero_text', 'subtitle_text'],
                'effects' => ['glass_morphism', 'neon_glow', 'gradient_borders', 'floating_elements'],
                'specialty' => ['timeline_item', 'progress_bar', 'badge_modern', 'stats_card']
            ]
        ];
    }
}