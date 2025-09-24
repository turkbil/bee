<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;
use DOMNode;

/**
 * 🧠 Smart HTML Translation Service
 * 
 * HTML içeriklerden sadece text kısımları çıkarılır, AI'a gönderilir,
 * çevrilen text'ler orijinal HTML yapısına geri yerleştirilir.
 * 
 * Features:
 * - %95 token tasarrufu
 * - HTML yapısını korur
 * - Attribute'leri bozmuyor
 * - CSS/JS kodlarına dokunmuyor
 */
class SmartHtmlTranslationService
{
    private AIService $aiService;
    private array $protectionMap = [];    // JavaScript framework attribute mapping
    private array $jsProtectionMap = [];  // JavaScript expression mapping
    
    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * HTML içeriği akıllıca çevirir
     */
    public function translateHtmlContent(
        string $htmlContent, 
        string $sourceLanguage, 
        string $targetLanguage
    ): string {
        // 🔄 Reset protection maps for each translation
        $this->protectionMap = [];
        $this->jsProtectionMap = [];
        
        Log::info('🧠 Smart HTML Translation başladı', [
            'source_lang' => $sourceLanguage,
            'target_lang' => $targetLanguage,
            'html_length' => strlen($htmlContent),
        ]);

        try {
            // 1. HTML'den text'leri çıkar
            $textExtractions = $this->extractTranslatableTexts($htmlContent);
            
            if (empty($textExtractions['texts'])) {
                Log::info('⚠️ Çevrilebilir text bulunamadı');
                return $htmlContent;
            }

            Log::info('📝 Text extraction tamamlandı', [
                'original_length' => strlen($htmlContent),
                'extractable_texts' => count($textExtractions['texts']),
                'total_text_length' => array_sum(array_map('strlen', $textExtractions['texts']))
            ]);

            // 2. Text'leri birleştirip AI'a gönder  
            $combinedText = implode("\n---TEXT-SEPARATOR---\n", $textExtractions['texts']);
            
            // 3. AI çevirisi
            $translatedCombined = $this->aiService->translateText(
                $combinedText, 
                $sourceLanguage, 
                $targetLanguage
            );

            if (empty($translatedCombined) || $translatedCombined === $combinedText) {
                Log::warning('⚠️ AI çeviri başarısız veya değişiklik yok');
                return $htmlContent;
            }

            // 4. Çevrilen text'leri ayır (ENHANCED AI response temizliği)
            $translatedTexts = $this->parseAITranslationResponse(
                $translatedCombined, 
                count($textExtractions['texts'])
            );
            
            if ($translatedTexts === null) {
                Log::error('❌ AI response parsing başarısız - fallback yapılıyor');
                return $htmlContent;
            }

            // 5. HTML'i yeniden oluştur
            $reconstructedHtml = $this->reconstructHtml(
                $textExtractions['template'], 
                $translatedTexts
            );

            Log::info('✅ Smart HTML Translation tamamlandı', [
                'token_saving' => round((1 - strlen($combinedText) / strlen($htmlContent)) * 100, 2) . '%'
            ]);

            return $reconstructedHtml;

        } catch (\Exception $e) {
            Log::error('❌ Smart HTML Translation hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: Orijinal içeriği döndür
            return $htmlContent;
        }
    }

    /**
     * 🧠 ULTRA SMART HTML Text Extraction v2.0
     * Alpine.js + JavaScript aware extraction
     */
    private function extractTranslatableTexts(string $htmlContent): array
    {
        $cleanHtml = $this->prepareHtmlForParsing($htmlContent);
        $texts = [];
        $template = $cleanHtml;
        $placeholderIndex = 0;

        // 1. 🛡️ ULTRA JAVASCRIPT FRAMEWORK PROTECTION SYSTEM
        // Alpine.js + Vue.js + React + jQuery + Livewire + Angular
        $jsFrameworkAttributes = [
            // Alpine.js
            'x-data', 'x-init', 'x-show', 'x-hide', 'x-transition', 'x-text', 'x-model', 'x-if', 'x-for',
            '@click', '@mouseenter', '@mouseleave', '@keyup', '@submit', '@change', '@input',
            // Vue.js
            'v-if', 'v-else', 'v-show', 'v-for', 'v-model', 'v-bind', 'v-on', '@click.prevent',
            // React (JSX attributes)
            'onClick', 'onChange', 'onSubmit', 'onMouseEnter', 'onMouseLeave',
            // jQuery data attributes
            'data-toggle', 'data-target', 'data-dismiss', 'data-backdrop', 'data-keyboard',
            // Livewire
            'wire:click', 'wire:model', 'wire:submit', 'wire:target', 'wire:loading', 'wire:poll',
            // Angular
            'ng-if', 'ng-show', 'ng-hide', 'ng-repeat', 'ng-model', 'ng-click',
            // General JavaScript events
            'onclick', 'onchange', 'onsubmit', 'onload', 'onmouseenter', 'onmouseleave'
        ];
        
        $protectionIndex = 0;
        foreach ($jsFrameworkAttributes as $attr) {
            // ENHANCED PROTECTION - Single and double quotes + complex expressions
            $patterns = [
                '/(' . preg_quote($attr) . '="[^"]*")/i',  // Double quotes
                "/(' . preg_quote($attr) . '=\'[^\']*\')/i", // Single quotes
                '/(' . preg_quote($attr) . '=`[^`]*`)/i',    // Backticks
            ];
            
            foreach ($patterns as $pattern) {
                $template = preg_replace_callback($pattern, function($matches) use (&$protectionIndex, $attr) {
                    $protectedToken = '{{JS_FRAMEWORK_PROTECTED_' . $protectionIndex . '}}';
                    $protectionIndex++;
                    
                    // Store mapping for restoration
                    $this->protectionMap[$protectedToken] = $matches[1];
                    
                    Log::debug('🛡️ JavaScript framework attribute protected', [
                        'attribute' => $attr,
                        'original' => substr($matches[1], 0, 100),
                        'token' => $protectedToken
                    ]);
                    
                    return $protectedToken;
                }, $template);
            }
        }
        
        // 2. 🔥 UNIVERSAL JAVASCRIPT EXPRESSION PROTECTION
        $jsPatterns = [
            // Timing functions
            '/setTimeout\s*\([^)]*\)/i',
            '/setInterval\s*\([^)]*\)/i',
            '/clearTimeout\s*\([^)]*\)/i',
            '/clearInterval\s*\([^)]*\)/i',
            // Function expressions
            '/\(\)\s*=>\s*[^,}]+/i',              // Arrow functions
            '/function\s*\([^)]*\)\s*\{[^}]*\}/i', // Function declarations
            // Object literals and assignments
            '/\{[^}]*[a-zA-Z_$][^}]*\}/i',       // Object literals with variables
            '/[a-zA-Z_$][a-zA-Z0-9_$]*\s*[=!]==?\s*(true|false|null|undefined)/i', // Boolean assignments
            '/[a-zA-Z_$][a-zA-Z0-9_$]*\s*[\+\-\*\/]=\s*\d+/i', // Math assignments
            // jQuery expressions
            '/\$\s*\([^)]*\)/i',                 // jQuery selectors
            '/\.\s*[a-zA-Z_$][a-zA-Z0-9_$]*\s*\(/i', // Method calls
            // DOM manipulation
            '/document\.[a-zA-Z_$][a-zA-Z0-9_$]*/i',
            '/window\.[a-zA-Z_$][a-zA-Z0-9_$]*/i',
            // Event handlers
            '/addEventListener\s*\([^)]*\)/i',
            '/removeEventListener\s*\([^)]*\)/i'
        ];
        
        foreach ($jsPatterns as $pattern) {
            $template = preg_replace_callback($pattern, function($matches) use (&$protectionIndex) {
                $jsCode = $matches[0];
                
                // 🚨 PREVENT CIRCULAR PROTECTION - Don't protect already protected tokens
                if (strpos($jsCode, '{{JS_FRAMEWORK_PROTECTED_') !== false || 
                    strpos($jsCode, '{{JS_PROTECTED_') !== false) {
                    return $jsCode; // Already protected, skip
                }
                
                $jsToken = '{{JS_PROTECTED_' . $protectionIndex . '}}';
                $protectionIndex++;
                
                // Store mapping for restoration  
                $this->jsProtectionMap[$jsToken] = $jsCode;
                
                Log::debug('⚡ JavaScript expression protected', [
                    'js_code' => $jsCode
                ]);
                
                return $jsToken;
            }, $template);
        }
        
        // 2. ENHANCED TEXT EXTRACTION - Multiple strategies
        $strategies = [
            // Strategy 1: Standard HTML text content
            '/>((?:[^<](?!<\s*\/?(?:script|style|code|pre)\b))*[^<]*)</i',
            // Strategy 2: Title attributes
            '/title="([^"]+)"/i',
            // Strategy 3: Alt attributes  
            '/alt="([^"]+)"/i',
            // Strategy 4: Placeholder attributes
            '/placeholder="([^"]+)"/i'
        ];
        
        foreach ($strategies as $strategyIndex => $pattern) {
            $template = preg_replace_callback($pattern, function($matches) use (&$texts, &$placeholderIndex, $strategyIndex) {
                $text = $strategyIndex === 0 ? trim($matches[1]) : trim($matches[1]);
                
                if ($this->isTranslatableText($text)) {
                    $placeholder = "{{TEXT_PLACEHOLDER_{$placeholderIndex}}}";
                    $texts[] = $text;
                    $placeholderIndex++;
                    
                    if ($strategyIndex === 0) {
                        return '>' . $placeholder . '<';
                    } else {
                        $attrName = explode('=', $matches[0])[0];
                        return $attrName . '="' . $placeholder . '"';
                    }
                }
                
                return $matches[0];
            }, $template);
        }

        Log::info('🧠 Text extraction tamamlandı', [
            'total_texts' => count($texts),
            'sample_texts' => array_slice($texts, 0, 3),
            'template_length' => strlen($template)
        ]);

        return [
            'texts' => $texts,
            'template' => $template,
            'alpine_protected' => true
        ];
    }

    /**
     * 🔥 ULTRA ENHANCED HTML Reconstruction v3.0
     * Alpine.js protection + BULLETPROOF placeholder replacement
     */
    private function reconstructHtml(string $template, array $translatedTexts): string
    {
        $html = $template;
        
        Log::info('🔄 ENHANCED HTML Reconstruction başlatıldı', [
            'template_length' => strlen($template),
            'translated_segments' => count($translatedTexts),
            'template_preview' => substr($template, 0, 200)
        ]);
        
        // 1. PRE-VALIDATION - Check all placeholders exist in template
        $missingPlaceholders = [];
        $existingPlaceholders = [];
        
        foreach ($translatedTexts as $index => $translatedText) {
            $placeholder = "{{TEXT_PLACEHOLDER_{$index}}}";
            if (strpos($html, $placeholder) === false) {
                $missingPlaceholders[] = $placeholder;
            } else {
                $existingPlaceholders[] = $placeholder;
            }
        }
        
        Log::info('📊 Placeholder validation completed', [
            'total_expected' => count($translatedTexts),
            'existing_count' => count($existingPlaceholders),
            'missing_count' => count($missingPlaceholders),
            'missing_placeholders' => array_slice($missingPlaceholders, 0, 10)
        ]);
        
        // 2. 🔧 ULTRA ROBUST PLACEHOLDER REPLACEMENT SYSTEM
        $totalReplacements = 0;
        $successfulReplacements = 0;
        $failedReplacements = [];
        
        foreach ($translatedTexts as $index => $translatedText) {
            $placeholder = "{{TEXT_PLACEHOLDER_{$index}}}";
            $cleanText = trim($translatedText);
            
            // Multi-strategy replacement approach
            $strategies = [
                'exact_match' => function() use ($placeholder, $cleanText, &$html) {
                    $count = 0;
                    $html = str_replace($placeholder, $cleanText, $html, $count);
                    return $count;
                },
                'case_insensitive' => function() use ($placeholder, $cleanText, &$html) {
                    $count = 0;
                    $html = str_ireplace($placeholder, $cleanText, $html, $count);
                    return $count;
                },
                'regex_based' => function() use ($index, $cleanText, &$html) {
                    $pattern = '/\\{\\{TEXT_PLACEHOLDER_' . $index . '\\}\\}/';
                    $count = preg_match_all($pattern, $html);
                    $html = preg_replace($pattern, $cleanText, $html);
                    return $count;
                },
                'flexible_spacing' => function() use ($index, $cleanText, &$html) {
                    $pattern = '/\\{\\{\\s*TEXT_PLACEHOLDER_' . $index . '\\s*\\}\\}/';
                    $count = preg_match_all($pattern, $html);
                    $html = preg_replace($pattern, $cleanText, $html);
                    return $count;
                }
            ];
            
            $replacementSuccess = false;
            foreach ($strategies as $strategyName => $strategy) {
                $replacementCount = $strategy();
                
                if ($replacementCount > 0) {
                    $totalReplacements += $replacementCount;
                    $successfulReplacements++;
                    $replacementSuccess = true;
                    
                    Log::debug("✅ Placeholder replaced successfully", [
                        'placeholder' => $placeholder,
                        'strategy' => $strategyName,
                        'replacements' => $replacementCount,
                        'text_preview' => substr($cleanText, 0, 50)
                    ]);
                    break;
                }
            }
            
            if (!$replacementSuccess) {
                $failedReplacements[] = [
                    'placeholder' => $placeholder,
                    'text' => substr($cleanText, 0, 100),
                    'template_contains' => strpos($html, $placeholder) !== false
                ];
                
                Log::error("❌ ALL replacement strategies failed", [
                    'placeholder' => $placeholder,
                    'text_preview' => substr($cleanText, 0, 50),
                    'template_search' => strpos($html, $placeholder) !== false ? 'FOUND' : 'NOT_FOUND'
                ]);
            }
        }
        
        Log::info('📊 Total placeholder replacements', [
            'expected' => count($translatedTexts),
            'actual' => $totalReplacements
        ]);
        
        // 2. 🔄 ULTRA RESTORATION SYSTEM - Alpine.js + JavaScript
        // Restore ULTRA protected JavaScript framework attributes
        $html = preg_replace_callback(
            '/{{JS_FRAMEWORK_PROTECTED_([0-9]+)}}/',
            function($matches) {
                $token = $matches[0];
                if (isset($this->protectionMap[$token])) {
                    Log::debug('🔄 JavaScript framework attribute restored', ['token' => $token]);
                    return $this->protectionMap[$token];
                }
                Log::warning('⚠️ JS Framework protection token not found', ['token' => $token]);
                return $token;
            },
            $html
        );
        
        // Restore JavaScript expressions 
        $html = preg_replace_callback(
            '/{{JS_PROTECTED_([0-9]+)}}/',
            function($matches) {
                $token = $matches[0];
                if (isset($this->jsProtectionMap[$token])) {
                    Log::debug('⚡ JavaScript restored', ['token' => $token]);
                    return $this->jsProtectionMap[$token];
                }
                return $token;
            },
            $html
        );
        
        // Legacy protection restore (for backward compatibility)
        $html = preg_replace_callback(
            '/{{ALPINE_PROTECTED_([^}]+)}}/',
            function($matches) {
                return base64_decode($matches[1]);
            },
            $html
        );
        
        // 3. 🔍 QUALITY CHECK & FINAL CLEANUP
        $remainingPlaceholders = preg_match_all('/{{[^}]+}}/', $html, $matches);
        if ($remainingPlaceholders > 0) {
            Log::error('❌ Reconstruction incomplete - attempting final cleanup', [
                'remaining_count' => $remainingPlaceholders,
                'placeholders' => $matches[0] ?? []
            ]);
            
            // Final cleanup - remove remaining placeholders
            $html = preg_replace('/{{TEXT_PLACEHOLDER_[0-9]+}}/', '', $html);
            $html = preg_replace('/{{[^}]+}}/', '', $html);
            
            Log::info('🧹 Final cleanup completed - orphaned placeholders removed');
        }
        
        Log::info('🔄 HTML reconstruction tamamlandı', [
            'final_length' => strlen($html),
            'replacements_made' => count($translatedTexts),
            'alpine_restored' => true
        ]);

        return $html;
    }

    /**
     * 🎯 ULTRA SMART Translatable Text Detection v2.0
     * Alpine.js + JavaScript + CSS aware protection
     */
    private function isTranslatableText(string $text): bool
    {
        $trimmedText = trim($text);
        
        // Boş text
        if ($trimmedText === '') {
            return false;
        }

        // 🚨 UNIVERSAL JAVASCRIPT EXPRESSIONS - CRITICAL PROTECTION
        $jsExpressionPatterns = [
            // Object literals and assignments
            '/^\{[^}]*\}$/',                    // { loaded: false, count: 0 }
            '/[a-zA-Z_$][a-zA-Z0-9_$]*\s*[=!]==?\s*(true|false|null|undefined)/', // loaded = true
            // Function calls
            '/setTimeout\s*\(/',               // setTimeout(() =>
            '/setInterval\s*\(/',             // setInterval(() =>
            '/clearTimeout\s*\(/',            // clearTimeout(id)
            '/\$\s*\(/',                      // $(selector)
            // Math operations
            '/[a-zA-Z_$][a-zA-Z0-9_$]*\s*[\+\-\*\/]=/', // count += 3, total -= 5
            // Framework-specific patterns
            '/x-[a-zA-Z-]+/',                  // x-show, x-data (Alpine.js)
            '/v-[a-zA-Z-]+/',                  // v-if, v-model (Vue.js)
            '/ng-[a-zA-Z-]+/',                 // ng-if, ng-repeat (Angular)
            // State variables
            '/[a-zA-Z_$][a-zA-Z0-9_$]*\s*[=!]/', // hovered = true, visible != false
            // CSS class patterns (framework utilities)
            '/opacity-[0-9]+/',               // opacity-90
            '/duration-[0-9]+/',              // duration-300
            '/transition-[a-zA-Z-]+/',        // transition-all
            // DOM events and methods
            '/\.[a-zA-Z_$][a-zA-Z0-9_$]*\s*\(/', // .show(), .hide(), .toggle()
        ];
        
        foreach ($jsExpressionPatterns as $pattern) {
            if (preg_match($pattern, $trimmedText)) {
                Log::debug('🛡️ JavaScript expression protected', ['text' => $trimmedText]);
                return false;
            }
        }

        // 🚨 CSS CLASS NAMES - Don't translate Tailwind classes
        if (preg_match('/^([a-zA-Z-]+:[a-zA-Z0-9-]+\s*)+$/', $trimmedText) || 
            preg_match('/^(bg-|text-|from-|to-|w-|h-|py-|px-|rounded-|shadow-|hover:|dark:|sm:|md:|lg:)/', $trimmedText)) {
            return false;
        }

        // HTML etiketleri içi (<div>, <span> vb.)
        if (preg_match('/<[^>]+>/', $trimmedText)) {
            return false;
        }

        // HTML entity'leri (&amp;, &lt; vb.)
        if (preg_match('/&[a-zA-Z0-9]+;/', $trimmedText)) {
            return false;
        }

        // 🚨 ENHANCED JavaScript/CSS kod pattern'leri
        $codePatterns = [
            '/(function\s*\(|var\s+|let\s+|const\s+)/',
            '/\{[^}]*\}/',                     // Object literals
            '/[a-zA-Z\-]+\s*:\s*[^;]+;/',      // CSS properties
            '/[a-zA-Z_$][a-zA-Z0-9_$]*\s*\(/',  // Function calls
            '/\d+px|\d+em|\d+rem|\d+%/',       // CSS units
            '/^#[0-9a-fA-F]{3,8}$/',          // Hex colors
            '/rgb\(|rgba\(|hsl\(|hsla\(/',    // CSS color functions
        ];
        
        foreach ($codePatterns as $pattern) {
            if (preg_match($pattern, $trimmedText)) {
                return false;
            }
        }

        // URL/Email
        if (preg_match('/^(https?:\/\/|www\.|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $trimmedText)) {
            return false;
        }

        // HTML teknik terimleri
        $technicalTerms = [
            'class', 'id', 'style', 'src', 'href', 'alt', 'title', 'type', 'name', 'value', 'placeholder',
            'div', 'span', 'section', 'article', 'header', 'footer', 'nav', 'main', 'form', 'input', 'button',
            // JavaScript framework terms
            'loaded', 'hovered', 'count', 'visible', 'active', 'disabled', 'selected',
            'true', 'false', 'null', 'undefined', 'this', 'self', 'window', 'document',
            // jQuery terms
            'show', 'hide', 'toggle', 'fadeIn', 'fadeOut', 'slideUp', 'slideDown',
            // Vue.js terms  
            'data', 'methods', 'computed', 'watch', 'props', 'emit',
            // React terms
            'useState', 'useEffect', 'props', 'state', 'setState'
        ];
        
        if (in_array(strtolower($trimmedText), $technicalTerms)) {
            return false;
        }

        // Sadece sayı+sembol (kod indicator'u) - but allow normal numbers in text
        if (preg_match('/^[\d\s\W]*$/', $trimmedText) && !preg_match('/[a-zA-ZüçöşıĞÜÇÖŞIİ]/', $trimmedText) && strlen($trimmedText) < 10) {
            return false;
        }

        // Çok kısa (1 karakter)
        if (strlen($trimmedText) < 2) {
            return false;
        }

        // ✅ REAL CONTENT = TRANSLATABLE!
        // Text content like headings, paragraphs, button labels
        if (preg_match('/[a-zA-ZüçöşıĞÜÇÖŞIİ]{2,}/', $trimmedText)) {
            Log::debug('✅ Translatable content detected', ['text' => substr($trimmedText, 0, 50)]);
            return true;
        }

        return false;
    }

    /**
     * HTML'i parsing için hazırlar
     */
    private function prepareHtmlForParsing(string $html): string
    {
        // Self-closing tag'leri düzelt
        $html = preg_replace('/<(area|base|br|col|embed|hr|img|input|link|meta|param|source|track|wbr)([^>]*?)>/i', '<$1$2 />', $html);
        
        // Script ve style içeriklerini koru (çevrilmesin)
        $html = preg_replace('/(<script[^>]*>)(.*?)(<\/script>)/is', '$1<!-- PROTECTED_CONTENT -->$3', $html);
        $html = preg_replace('/(<style[^>]*>)(.*?)(<\/style>)/is', '$1<!-- PROTECTED_CONTENT -->$3', $html);
        
        return $html;
    }

    /**
     * 🔥 ULTRA ENHANCED AI Response Parser v3.0
     * TÜM DİLLER + SEPARATOR INTELLIGENCE - evrensel çözüm
     */
    private function parseAITranslationResponse(string $response, int $expectedCount): ?array
    {
        Log::info('🔍 ENHANCED AI Response parsing başlatıldı', [
            'expected_count' => $expectedCount,
            'response_length' => strlen($response),
            'response_preview' => substr($response, 0, 200)
        ]);

        // 1. ULTRA AGGRESSIVE CLEANUP - AI response wrapper patterns
        $cleanupPatterns = [
            // AI explanation prefixes
            '/^(Here.*translation.*:|Translation.*:|Translated.*text.*:)/im',
            '/^(The.*translation.*is:|Below.*translation:|Following.*translation:)/im',
            '/^(I.*translated.*text.*:|Translating.*to.*:)/im',
            // Code block markers
            '/^```[a-z]*\\s*\\n?/',
            '/\\n?\\s*```\\s*$/',
            '/^```plaintext\\s*\\n?|```markdown\\s*\\n?|```html\\s*\\n?/',
            // Markdown formatting
            '/^\\*\\*.*\\*\\*\\s*\\n?/',
            '/^#{1,6}\\s.*\\n?/m',
            // Empty lines at start/end
            '/^\\s*\\n+/',
            '/\\n+\\s*$/'
        ];
        
        $cleanResponse = $response;
        foreach ($cleanupPatterns as $pattern) {
            $cleanResponse = preg_replace($pattern, '', $cleanResponse);
        }
        $cleanResponse = trim($cleanResponse);
        
        Log::info('🧹 Response cleanup tamamlandı', [
            'original_length' => strlen($response),
            'cleaned_length' => strlen($cleanResponse),
            'cleanup_ratio' => round((1 - strlen($cleanResponse) / strlen($response)) * 100, 2) . '%'
        ]);
        
        // 2. PRIMARY STRATEGY - Exact separator matching
        $primarySeparators = [
            "\n---TEXT-SEPARATOR---\n",
            "---TEXT-SEPARATOR---",
            "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
        ];
        
        foreach ($primarySeparators as $separator) {
            $segments = explode($separator, $cleanResponse);
            if (count($segments) === $expectedCount) {
                Log::info('✅ PRIMARY parsing successful', [
                    'separator' => $separator,
                    'segments_found' => count($segments)
                ]);
                return array_map('trim', $segments);
            }
        }
        
        // 3. SECONDARY STRATEGY - Flexible separator patterns
        $flexibleSeparators = [
            '/\\s*---TEXT-SEPARATOR---\\s*/',
            '/\\s*━{20,}\\s*/',
            '/\\n\\s*---\\s*\\n/',
            '/\\n\\s*={20,}\\s*\\n/',
            '/\\n\\s*\\*{20,}\\s*\\n/',
            '/\\n\\s*~{20,}\\s*\\n/'
        ];
        
        foreach ($flexibleSeparators as $pattern) {
            $segments = preg_split($pattern, $cleanResponse);
            if (count($segments) === $expectedCount) {
                Log::info('✅ SECONDARY parsing successful', [
                    'pattern' => $pattern,
                    'segments_found' => count($segments)
                ]);
                return array_map('trim', $segments);
            }
        }
        
        // 4. 🚨 ENHANCED FALLBACK - Force split if single segment received
        if (count($segments) === 1 && $expectedCount > 1) {
            Log::warning('⚠️ Single segment received, force splitting attempt');
            
            // Try to split by common AI response patterns
            $forceSplitPatterns = [
                '/\s{2,}---TEXT-SEPARATOR---\s{2,}/',  // Spaced separators
                '/\n\s*\n/',                           // Double newlines
                '/\s*\|\s*/',                        // Pipe separators
                '/\s*━{10,}\s*/',                    // Unicode lines
            ];
            
            foreach ($forceSplitPatterns as $pattern) {
                $forcedSegments = preg_split($pattern, $cleanResponse);
                if (count($forcedSegments) === $expectedCount) {
                    Log::info('✅ Force split successful', ['pattern' => $pattern]);
                    return array_map('trim', $forcedSegments);
                }
            }
        }
        
        // 5. Last resort for single response
        if ($expectedCount === 1 || count($segments) === 1) {
            Log::info('✅ Single segment fallback');
            return [$cleanResponse];
        }
        
        // 6. 🆘 ULTIMATE FALLBACK - Split response manually by expected count
        if ($expectedCount > 1) {
            Log::warning('🆘 Ultimate fallback - manual text splitting');
            
            // Calculate approximate segment length
            $avgLength = strlen($cleanResponse) / $expectedCount;
            $manualSegments = [];
            
            // Split by sentence boundaries near expected positions
            $sentences = preg_split('/[.!?]\s+/', $cleanResponse);
            $currentSegment = '';
            $segmentCount = 0;
            
            foreach ($sentences as $sentence) {
                $currentSegment .= $sentence . '. ';
                
                if (strlen($currentSegment) >= $avgLength && $segmentCount < $expectedCount - 1) {
                    $manualSegments[] = trim($currentSegment);
                    $currentSegment = '';
                    $segmentCount++;
                }
            }
            
            // Add remaining content as last segment
            if (!empty($currentSegment) || count($manualSegments) < $expectedCount) {
                $manualSegments[] = trim($currentSegment);
            }
            
            if (count($manualSegments) === $expectedCount) {
                Log::info('🆘 Manual splitting successful');
                return $manualSegments;
            }
        }
        
        // 7. Final detailed debugging
        Log::error('❌ AI response parsing tamamen başarısız', [
            'expected_segments' => $expectedCount,
            'received_segments' => count($segments),
            'response_length' => strlen($response),
            'clean_response_preview' => substr($cleanResponse, 0, 300),
            'segments_preview' => array_map(fn($s) => substr(trim($s), 0, 50), array_slice($segments, 0, 5))
        ]);
        
        return null;
    }

    /**
     * Translation stats döndürür
     */
    public function getTranslationStats(string $originalHtml, string $translatedHtml): array
    {
        return [
            'original_length' => strlen($originalHtml),
            'translated_length' => strlen($translatedHtml),
            'html_tags_original' => substr_count($originalHtml, '<'),
            'html_tags_translated' => substr_count($translatedHtml, '<'),
            'structure_preserved' => substr_count($originalHtml, '<') === substr_count($translatedHtml, '<'),
        ];
    }
}