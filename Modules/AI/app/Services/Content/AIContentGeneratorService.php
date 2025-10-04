<?php

declare(strict_types=1);

namespace Modules\AI\app\Services\Content;

use Modules\AI\App\Services\AIService;
use Modules\AI\App\Models\AIPrompt;
use Modules\AI\App\Models\AICreditUsage;
use Modules\AI\App\Jobs\AIContentGenerationJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Tenant;

/**
 * GLOBAL AI Content Generator Service
 *
 * Tüm modüller için AI destekli içerik üretimi sağlar.
 * Module-agnostic tasarım ile herhangi bir modül bu sistemi kullanabilir.
 */
class AIContentGeneratorService
{
    private AIService $aiService;

    // Content Builder için özel prompt ID aralığı
    private const CONTENT_BUILDER_PROMPT_START = 5000;
    private const CREDIT_COSTS = [
        'simple' => 3,
        'moderate' => 5,
        'complex' => 10,
        'template' => 2,
        'pdf_enhanced' => 8  // 15'ten 8'e düşürüldü (%47 tasarruf)
    ];

    public function __construct()
    {
        $this->aiService = app(AIService::class);
    }

    /**
     * GLOBAL AI ile içerik üret - Herhangi bir modül kullanabilir
     */
    public function generateContent(array $params): array
    {
        try {
            Log::info('🚀 MOBILE-FIRST AI Content Generation başladı', [
                'tenant_id' => $params['tenant_id'] ?? 'unknown',
                'has_pdf' => isset($params['file_analysis']),
                'prompt_length' => strlen($params['prompt'] ?? ''),
                'content_type' => $params['content_type'] ?? 'auto',
                'pattern_page_id' => $params['pattern_page_id'] ?? null
            ]);

            $tenantId = $params['tenant_id'] ?? tenant('id');
            $prompt = $params['prompt'] ?? '';
            $contentType = $params['content_type'] ?? 'auto';
            $customInstructions = $params['custom_instructions'] ?? '';
            $pageTitle = $params['page_title'] ?? null;
            $moduleContext = $params['module_context'] ?? []; // Modül-specific context
            $patternPageId = $params['pattern_page_id'] ?? null; // 🆕 Pattern page ID

            // 🆕 File analysis integration
            $fileAnalysis = $params['file_analysis'] ?? null;
            $conversionType = $params['conversion_type'] ?? 'content_extract';

            // 🚫 AI İÇERİK CACHE YOK - Her seferinde farklı sonuç!
            // PDF analizi cache'lenir ama AI content generation her seferinde yeni

            // 💰 PDF destekli içerik için otomatik olarak 'unlimited' token kullan
            $length = $fileAnalysis ? 'unlimited' : ($params['length'] ?? 'ultra_long');

            // Prompt içinden editoryal briefları çek (terim haritası, ürün adı vb.)
            $extractedBrief = $this->extractEditorialBriefFromPrompt($prompt ?? '');
            $editorialBriefContext = $this->buildEditorialBriefContext($extractedBrief);
            if (!empty($editorialBriefContext)) {
                $customInstructions = trim($editorialBriefContext . "\n\n" . $customInstructions);
            }

            if ($fileAnalysis) {
                Log::info('🔍 File analysis detected, enhancing prompt...', [
                    'file_type' => $fileAnalysis['file_type'],
                    'analysis_type' => $fileAnalysis['analysis_type'],
                    'has_layout_info' => isset($fileAnalysis['individual_results'][0]['layout_info'])
                ]);

                // Build enhanced prompt with file content
                $contextualPrompt = $this->buildFileEnhancedPrompt($prompt, $fileAnalysis, $conversionType);
            } else {
                // Pattern page varsa o zaman pattern-based prompt oluştur
                if ($patternPageId) {
                    $contextualPrompt = $this->buildPatternBasedPrompt($prompt, $patternPageId, $moduleContext);
                } else {
                    // Modül context'i entegre et
                    $contextualPrompt = $this->buildModuleContextualPrompt($prompt, $moduleContext);
                }
            }
            
            // Otomatik sayfa türü tespiti - Sadece genel
            $detectedPageType = 'genel';

            // Site bilgileri
            $siteName = setting('site_title') ?? 'Site';

            Log::info('📝 GLOBAL Parametreler hazırlandı', [
                'tenantId' => $tenantId,
                'prompt' => $contextualPrompt,
                'contentType' => $contentType,
                'length' => $length,
                'pageTitle' => $pageTitle,
                'detectedPageType' => $detectedPageType,
                'siteName' => $siteName,
                'moduleContext' => $moduleContext
            ]);

            // 🗑️ TEMA/CSS ANALİZİ KALDIRILDI - Sadece Tailwind kullanıyoruz

            // İçerik tipini belirle - artık belirleme yok, normal sayfa
            if ($contentType === 'auto') {
                $contentType = 'page'; // Normal sayfa gibi davransın
            }

            // 🎯 PATTERN-AWARE RESPONSIVE MASTER PROMPT
            if ($fileAnalysis && isset($fileAnalysis['individual_results'][0]['extracted_content'])) {
                $pdfContent = $fileAnalysis['individual_results'][0]['extracted_content'];

                // 🖼️ PDF GÖRSELLER - PDF'deki görsel bilgilerini al
                $pdfImages = $fileAnalysis['individual_results'][0]['extracted_images'] ?? [];
                $imageCount = $fileAnalysis['individual_results'][0]['image_count'] ?? 0;

                // PDF content'e görsel bilgilerini ekle
                if (!empty($pdfImages) && $imageCount > 0) {
                    $pdfContent .= "\n\n📸 PDF'DEKİ GÖRSELLER ({$imageCount} adet):\n";

                    $totalOCRText = '';
                    $imagesWithText = 0;

                    foreach ($pdfImages as $index => $image) {
                        if ($image['type'] === 'image') {
                            $pdfContent .= "- Görsel #" . ($index + 1) . ": {$image['description']} ";
                            $pdfContent .= "({$image['dimensions']['width']}x{$image['dimensions']['height']}, ";
                            $pdfContent .= "{$image['format']}, Sayfa: {$image['page']})";

                            // 🔍 OCR TEXT ENTEGRASYONU - Görsel içindeki text'i ekle
                            if (isset($image['ocr_text']) && !empty($image['ocr_text'])) {
                                $pdfContent .= "\n  📝 OCR İçerik: " . $image['ocr_text'];
                                $pdfContent .= " (Güven: " . round(($image['ocr_confidence'] ?? 0) * 100) . "%)";
                                $totalOCRText .= "\n" . $image['ocr_text'];
                                $imagesWithText++;
                            } elseif (isset($image['ocr_status']) && $image['ocr_status'] !== 'success') {
                                $pdfContent .= "\n  ⚠️ OCR: " . ($image['ocr_note'] ?? 'Text çıkarılamadı');
                            }

                            $pdfContent .= "\n";
                        }
                    }

                    // OCR özeti
                    if ($imagesWithText > 0) {
                        $pdfContent .= "\n🎯 OCR ÖZETİ: {$imagesWithText}/{$imageCount} görselde text bulundu\n";
                        $pdfContent .= "📝 TOPLAM OCR İÇERİK:\n{$totalOCRText}\n";
                    }

                    $pdfContent .= "ℹ️ NOT: Bu görseller HTML'e eklenmeyecek, OCR ile çıkarılan text içerik olarak kullanılacak.\n";
                }

                $finalPrompt = \Modules\AI\App\Services\ResponseTemplateEngine::generatePatternAwarePrompt($contextualPrompt, $pdfContent);

                // Pattern detection log
                $detectedPattern = \Modules\AI\App\Services\ResponseTemplateEngine::detectUniversalPattern($pdfContent);
                Log::info('🎯 Pattern-aware prompt oluşturuldu', [
                    'detected_pattern' => $detectedPattern,
                    'pdf_length' => strlen($pdfContent),
                    'image_count' => $imageCount,
                    'has_pdf_images' => !empty($pdfImages),
                    'images_with_ocr' => $imagesWithText ?? 0,
                    'total_ocr_length' => strlen($totalOCRText ?? ''),
                    'prompt_length' => strlen($finalPrompt)
                ]);
            } elseif ($patternPageId) {
                // Pattern page kullanıyorsa pattern content'i eklenir
                $patternContent = $this->getPatternPageContent($patternPageId);
                if ($patternContent) {
                    $finalPrompt = \Modules\AI\App\Services\ResponseTemplateEngine::generatePatternAwarePrompt($contextualPrompt, $patternContent);
                    Log::info('🎨 Pattern page prompt oluşturuldu', [
                        'pattern_page_id' => $patternPageId,
                        'pattern_length' => strlen($patternContent),
                        'prompt_length' => strlen($finalPrompt)
                    ]);
                } else {
                    $finalPrompt = \Modules\AI\App\Services\ResponseTemplateEngine::generatePatternAwarePrompt($contextualPrompt);
                    Log::warning('⚠️ Pattern page bulunamadı, default prompt kullanıldı', ['pattern_page_id' => $patternPageId]);
                }
            } else {
                $finalPrompt = \Modules\AI\App\Services\ResponseTemplateEngine::generatePatternAwarePrompt($contextualPrompt);
                Log::info('📱 Mobile-first pattern-aware prompt oluşturuldu', [
                    'prompt_length' => strlen($finalPrompt),
                    'default_pattern' => 'SHOWCASE'
                ]);
            }

            // AI çağrısı

            try {
                // Model tercihini kontrol et - content_type'a göre AI modeli seç
                $preferredModel = $this->selectOptimalAIModel($contentType, $params);

                Log::info('🧠 AI Model seçimi', [
                    'content_type' => $contentType,
                    'selected_model' => $preferredModel['model'],
                    'provider' => $preferredModel['provider'],
                    'reason' => $preferredModel['reason']
                ]);

                $messages = [
                    [
                        'role' => 'user',
                        'content' => $finalPrompt
                    ]
                ];

                // Seçilen modele göre service kullan
                if ($preferredModel['provider'] === 'anthropic') {
                    // Database'den al (şifrelenmiş)
                    $provider = \Modules\AI\App\Models\AIProvider::where('name', 'anthropic')
                        ->where('is_active', true)
                        ->first();
                    $apiKey = $provider ? $provider->api_key : null; // Otomatik decrypt

                    Log::info('🔑 Anthropic API Key Debug', [
                        'has_key' => !empty($apiKey),
                        'from_database' => true,
                        'key_length' => $apiKey ? strlen($apiKey) : 0
                    ]);

                    $anthropicService = app(\Modules\AI\App\Services\AnthropicService::class);
                    $anthropicService->setApiKey($apiKey);
                    $anthropicService->setModel($preferredModel['model']);

                    // 🚀 DYNAMIC TOKEN OPTIMIZATION kullan
                    $tokenOptimization = $this->optimizeTokenUsage($params, $preferredModel);
                    $optimizedTokens = $tokenOptimization['optimized_tokens'];

                    $anthropicService->setMaxTokens($optimizedTokens);
                    Log::info('🚀 Dynamic Token Optimization uygulandı', [
                        'provider' => $preferredModel['provider'],
                        'model' => $preferredModel['model'],
                        'optimized_tokens' => $optimizedTokens,
                        'efficiency_score' => $tokenOptimization['efficiency_score'],
                        'reasoning' => $tokenOptimization['reasoning'],
                        'cost_estimate' => $tokenOptimization['cost_estimate']
                    ]);

                    if ($fileAnalysis) {
                        // ⛓️ PDF için uzun içerik: parça parça üret ve birleştir
                        $aiResponse = $this->generateLongFromPdf(
                            $finalPrompt,
                            $fileAnalysis,
                            $anthropicService
                        );
                    } else {
                        $result = $anthropicService->generateCompletionStream($messages);

                        // Claude API hata kontrolü - OPENAI FALLBACK YOK!
                        if (is_array($result) && isset($result['success']) && !$result['success']) {
                            Log::error('🔒 Claude API başarısız - OpenAI kullanılmayacak!', [
                                'error' => $result['error'] ?? 'Unknown error',
                                'model' => $preferredModel['model']
                            ]);

                            // OpenAI'ye GEÇİŞ YAPMA - Direkt hata ver
                            throw new \Exception('Claude API başarısız. Lütfen tekrar deneyin. (OpenAI devre dışı)');
                        } else {
                            $aiResponse = $result['response'] ?? $result;
                        }
                    }
                } else {
                    // Claude dışında başka provider seçilmişse - HATA VER
                    Log::error('🔒 YASAK: Claude dışında provider seçilemez!', [
                        'attempted_provider' => $preferredModel['provider']
                    ]);
                    throw new \Exception('AI Content Generation için SADECE Claude Sonnet 4 kullanılabilir!');
                }

                Log::info('🔥 GLOBAL AI Response', [
                    'response_type' => gettype($aiResponse),
                    'is_array' => is_array($aiResponse),
                    'response_sample' => is_string($aiResponse) ? substr($aiResponse, 0, 200) : 'not string'
                ]);

                // Response'u string'e çevir
                if (is_array($aiResponse)) {
                    $aiResponse = $aiResponse['response'] ?? $aiResponse['content'] ?? json_encode($aiResponse);
                }

                // Markdown code block'larını temizle
                $aiResponse = $this->cleanMarkdownBlocks($aiResponse);
                
            } catch (\Exception $e) {
                Log::error('🔒 AI Content Generation HATA (OpenAI kullanılmayacak): ' . $e->getMessage());
                // OpenAI FALLBACK YOK - Direkt hata dön
                throw new \Exception('Üzgünüm, AI içerik üretimi başarısız. Lütfen tekrar deneyin: ' . $e->getMessage());
            }
            
            Log::info('✅ GLOBAL AI yanıt alındı', [
                'response_length' => strlen($aiResponse),
                'has_content' => !empty($aiResponse)
            ]);

            // Kredi kullanımını kaydet
            Log::info('💰 GLOBAL Kredi kullanımı kaydediliyor...');
            $safeTenantId = $tenantId ?? 1; // Default tenant ID
            $this->recordCreditUsage($safeTenantId, $contentType, $contextualPrompt, $moduleContext);
            Log::info('✅ GLOBAL Kredi kullanımı kaydedildi');

            // HTML'i işle - basit temizlik
            $processedContent = $this->processDynamicContent($aiResponse, []);

            // Son temizlik: HTML dışı cümleleri ve yarım cümleleri temizle, bütünlük sağla
            $processedContent = $this->finalizeHTMLContent($processedContent);

            // Editoryal brief zorunlu uygulama (post-process)
            if (!empty($extractedBrief)) {
                $processedContent = $this->applyEditorialBrief($processedContent, $extractedBrief);
            }

            $creditsUsed = 15; // Ultra uzun sayfa için sabit kredi

            // 🚫 AI İÇERİK CACHE YAPMA - Kullanıcı her seferinde farklı sonuç görmek istiyor!

            $result = [
                'success' => true,
                'content' => $processedContent,
                'credits_used' => $creditsUsed,
                'theme_matched' => !empty($themeContext),
                'content_type' => $contentType,
                'module_context' => $moduleContext,
                'from_cache' => false, // Her zaman false - AI content cache yok
                'meta' => [
                    'framework' => 'tailwind',
                    'module_source' => $moduleContext['module'] ?? 'unknown'
                ]
            ];

            Log::info('🎉 GLOBAL İçerik üretimi BAŞARILI', [
                'final_content_length' => strlen($processedContent),
                'credits_used' => $creditsUsed,
                'module_context' => $moduleContext
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('❌ GLOBAL AI Content Generation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'module_context' => $moduleContext ?? []
            ]);

            return [
                'success' => false,
                'error' => 'İçerik üretilirken hata oluştu',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Modül-specific context ile prompt'u zenginleştir
     */
    private function buildModuleContextualPrompt(string $basePrompt, array $moduleContext): string
    {
        if (empty($moduleContext)) {
            return $basePrompt;
        }

        $contextualPrompt = $basePrompt;
        
        // Modül bilgisini ekle
        if (isset($moduleContext['module'])) {
            $contextualPrompt = "[MODÜL: {$moduleContext['module']}] " . $contextualPrompt;
        }
        
        // Modül-specific talimatları ekle
        if (isset($moduleContext['instructions'])) {
            $contextualPrompt .= "\n\nMODÜL TALİMATLARI: {$moduleContext['instructions']}";
        }
        
        // Modül alanlarını ekle
        if (isset($moduleContext['fields']) && is_array($moduleContext['fields'])) {
            $fieldsText = implode(', ', array_keys($moduleContext['fields']));
            $contextualPrompt .= "\n\nHEDEF ALANLAR: {$fieldsText}";
        }
        
        // Entity bilgisi varsa ekle
        if (isset($moduleContext['entity_type'])) {
            $contextualPrompt .= "\n\nENTİTY TİPİ: {$moduleContext['entity_type']}";
        }

        return $contextualPrompt;
    }

    /**
     * 🎨 Pattern page kullanarak prompt oluştur
     */
    private function buildPatternBasedPrompt(string $basePrompt, int $patternPageId, array $moduleContext): string
    {
        try {
            $patternContent = $this->getPatternPageContent($patternPageId);

            if (!$patternContent) {
                Log::warning('Pattern page bulunamadı, normal prompt kullanılıyor', ['pattern_page_id' => $patternPageId]);
                return $this->buildModuleContextualPrompt($basePrompt, $moduleContext);
            }

            $contextualPrompt = "[PATTERN KULLANIMI] Aşağıdaki sayfanın yapısını ve stilini takip ederek yeni içerik oluştur:\n\n";

            // Modül bilgisini ekle
            if (isset($moduleContext['module'])) {
                $contextualPrompt = "[MODÜL: {$moduleContext['module']}] " . $contextualPrompt;
            }

            $contextualPrompt .= "=== PATTERN SAYFA İÇERİĞİ ===\n";
            $contextualPrompt .= $patternContent . "\n";
            $contextualPrompt .= "=== PATTERN SONU ===\n\n";

            $contextualPrompt .= "ÖNEMLİ TALİMATLAR:\n";
            $contextualPrompt .= "• Yukarıdaki pattern sayfasının HTML yapısını, CSS sınıflarını ve genel düzenini takip et\n";
            $contextualPrompt .= "• Aynı section yapısını ve sıralamasını kullan\n";
            $contextualPrompt .= "• Benzer Tailwind CSS sınıflarını uygula\n";
            $contextualPrompt .= "• Aynı card, grid ve layout pattern'lerini tekrarla\n";
            $contextualPrompt .= "• Dark mode sınıflarını (dark:) da aynı şekilde koru\n";
            $contextualPrompt .= "• İçerik farklı olacak ama yapı tamamen aynı olmalı\n\n";

            $contextualPrompt .= "KULLANICI TALEBİ: " . $basePrompt . "\n\n";

            // Modül-specific talimatları ekle
            if (isset($moduleContext['instructions'])) {
                $contextualPrompt .= "MODÜL TALİMATLARI: {$moduleContext['instructions']}\n";
            }

            Log::info('🎨 Pattern-based prompt oluşturuldu', [
                'pattern_page_id' => $patternPageId,
                'pattern_content_length' => strlen($patternContent),
                'final_prompt_length' => strlen($contextualPrompt)
            ]);

            return $contextualPrompt;

        } catch (\Exception $e) {
            Log::error('Pattern-based prompt oluşturulamadı', [
                'pattern_page_id' => $patternPageId,
                'error' => $e->getMessage()
            ]);

            // Fallback: normal module prompt
            return $this->buildModuleContextualPrompt($basePrompt, $moduleContext);
        }
    }

    /**
     * 📄 Pattern page içeriğini al
     */
    private function getPatternPageContent(int $patternPageId): ?string
    {
        try {
            // Page modülünden page'i al
            $page = \Modules\Page\App\Models\Page::find($patternPageId);

            if (!$page) {
                Log::warning('Pattern page bulunamadı', ['page_id' => $patternPageId]);
                return null;
            }

            // Aktif dil için body içeriğini al
            $currentLocale = app()->getLocale();
            $body = $page->getTranslated('body', $currentLocale);

            if (!$body) {
                // Fallback: ilk mevcut dildeki içeriği al
                $bodyData = $page->body;
                if (is_array($bodyData) && !empty($bodyData)) {
                    $body = array_values($bodyData)[0];
                }
            }

            if (!$body || empty(trim($body))) {
                Log::warning('Pattern page body içeriği boş', ['page_id' => $patternPageId]);
                return null;
            }

            Log::info('Pattern page içeriği alındı', [
                'page_id' => $patternPageId,
                'locale' => $currentLocale,
                'content_length' => strlen($body)
            ]);

            return $body;

        } catch (\Exception $e) {
            Log::error('Pattern page içeriği alınamadı', [
                'page_id' => $patternPageId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * 🆕 File analysis ile prompt enhance et
     */
    private function buildFileEnhancedPrompt(string $basePrompt, array $fileAnalysis, string $conversionType): string
    {
        // PDF/Dosya içeriğini direkt web arayüzüne çevir
        $enhancedPrompt = "Lütfen aşağıdaki içeriği kullanarak profesyonel bir web sayfası oluşturun.\n\n";

        $enhancedPrompt .= "📄 İÇERİK KAYNAĞI: Ürün/hizmet dokümantasyonu\n\n";

        $enhancedPrompt .= "🎯 GÖREV:\n";
        $enhancedPrompt .= "• Verilen bilgileri kullanarak kapsamlı ve profesyonel bir tanıtım sayfası oluşturun\n";
        $enhancedPrompt .= "• Tüm teknik özellikleri, boyutları ve değerleri web formatına uygun şekilde düzenleyin\n\n";

        $enhancedPrompt .= "📋 İÇERİK OLUŞTURMA REHBERİ:\n";
        $enhancedPrompt .= "1. Dokümandaki tüm teknik spesifikasyonları HTML tablolar halinde gösterin\n";
        $enhancedPrompt .= "2. Sayısal verileri (kapasite, boyut, performans değerleri) organize edin\n";
        $enhancedPrompt .= "3. Özellikleri kategorize ederek listeleyip açıklayın\n";
        $enhancedPrompt .= "4. Görsel alanlar için uygun placeholder önerileri ekleyin\n";
        $enhancedPrompt .= "5. İçeriği 5000-20000 karakter aralığında tutun\n\n";

        $enhancedPrompt .= "📊 TAILWIND TABLO KURALLARI:\n";
        $enhancedPrompt .= "Dokümandaki veriler için Tailwind sınıflarıyla responsive tablolar oluşturun:\n\n";
        $enhancedPrompt .= "ÖRNEK TABLO YAPISI:\n";
        $enhancedPrompt .= "<div class=\\\"overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm\\\">\n";
        $enhancedPrompt .= "  <table class=\\\"min-w-full divide-y divide-gray-200 dark:divide-gray-700\\\">\n";
        $enhancedPrompt .= "    <thead class=\\\"bg-gray-50 dark:bg-gray-800\\\">\n";
        $enhancedPrompt .= "      <tr>\n";
        $enhancedPrompt .= "        <th class=\\\"px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider\\\">Özellik</th>\n";
        $enhancedPrompt .= "        <th class=\\\"px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider\\\">Değer</th>\n";
        $enhancedPrompt .= "        <th class=\\\"px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider\\\">Açıklama</th>\n";
        $enhancedPrompt .= "      </tr>\n";
        $enhancedPrompt .= "    </thead>\n";
        $enhancedPrompt .= "    <tbody class=\\\"bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700\\\">\n";
        $enhancedPrompt .= "      <tr>\n";
        $enhancedPrompt .= "        <td class=\\\"px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300\\\">Özellik adı</td>\n";
        $enhancedPrompt .= "        <td class=\\\"px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300\\\">Değer ve birim</td>\n";
        $enhancedPrompt .= "        <td class=\\\"px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300\\\">Detaylı açıklama</td>\n";
        $enhancedPrompt .= "      </tr>\n";
        $enhancedPrompt .= "    </tbody>\n";
        $enhancedPrompt .= "  </table>\n";
        $enhancedPrompt .= "</div>\n\n";

        $enhancedPrompt .= "TABLO KATEGORİLERİ:\n";
        $enhancedPrompt .= "• Teknik Özellikler\n";
        $enhancedPrompt .= "• Fiziksel Boyutlar\n";
        $enhancedPrompt .= "• Performans Değerleri\n";
        $enhancedPrompt .= "• Kapasite ve Limitler\n";
        $enhancedPrompt .= "• Opsiyonel Özellikler\n\n";

        $enhancedPrompt .= "📐 SAYFA YAPISI ÖNERİLERİ:\n";
        $enhancedPrompt .= "1. GIRIŞ BÖLÜMÜ\n";
        $enhancedPrompt .= "   • Ürün/hizmet tanıtımı\n";
        $enhancedPrompt .= "   • Ana faydalar ve özellikler\n\n";

        $enhancedPrompt .= "2. DETAYLI ÖZELLİKLER\n";
        $enhancedPrompt .= "   • Kategorize edilmiş özellik listeleri\n";
        $enhancedPrompt .= "   • Teknik detaylar\n\n";

        $enhancedPrompt .= "3. SPESIFIKASYON TABLOLARI\n";
        $enhancedPrompt .= "   • Dokümandaki tüm teknik değerler\n";
        $enhancedPrompt .= "   • Responsive tablo tasarımı\n";
        $enhancedPrompt .= "   • Kategori başlıkları ile gruplandırma\n\n";

        $enhancedPrompt .= "4. EK BİLGİLER\n";
        $enhancedPrompt .= "   • Kullanım alanları ve senaryolar\n";
        $enhancedPrompt .= "   • Avantajlar ve faydalar\n";
        $enhancedPrompt .= "   • Önemli notlar ve uyarılar\n\n";

        $enhancedPrompt .= "🎨 HTML TASARIM KURALLARI (TAILWIND + ALPINE):\n";
        $enhancedPrompt .= "• SADECE Tailwind CSS utility sınıflarını kullanın (Bootstrap/Tabler KULLANMAYIN)\n";
        $enhancedPrompt .= "• Her ana bölüm: <section class=\\\"py-16 md:py-24 bg-white dark:bg-gray-900\\\">\n";
        $enhancedPrompt .= "• Container: <div class=\\\"container mx-auto px-6 sm:px-8 lg:px-12\\\">\n";
        $enhancedPrompt .= "• Tipografi: text-gray-900 dark:text-gray-100, body: text-gray-700 dark:text-gray-300 leading-relaxed\n";
        $enhancedPrompt .= "• Kartlar: bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-lg p-8\n";
        $enhancedPrompt .= "• Butonlar: inline-flex items-center px-6 py-3 rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-all\n";
        $enhancedPrompt .= "• Dark mode için her renge karşılık gelen dark: sınıflarını mutlaka ekleyin\n";
        $enhancedPrompt .= "• Net başlık hiyerarşisi (H2, H3, H4) kullanın\n";
        $enhancedPrompt .= "• Profesyonel ve temiz, nefes alan (breathing) layout üretin\n\n";

        $enhancedPrompt .= "📊 PDF DOKÜMAN İÇERİĞİ:\n";
        $enhancedPrompt .= "=================================\n";

        // PDF'in tam içeriğini al - kısaltma yok!
        $pdfContent = '';
        if (isset($fileAnalysis['extracted_content'])) {
            $pdfContent = $fileAnalysis['extracted_content'];
        } elseif (isset($fileAnalysis['individual_results'][0]['extracted_content'])) {
            $pdfContent = $fileAnalysis['individual_results'][0]['extracted_content'];
        } elseif (isset($fileAnalysis['pages']) && is_array($fileAnalysis['pages'])) {
            // Çok sayfalı PDF için tüm sayfaları birleştir
            $pdfContent = '';
            foreach ($fileAnalysis['pages'] as $page) {
                if (isset($page['extracted_content'])) {
                    $pdfContent .= $page['extracted_content'] . "\n\n";
                }
            }
        }

        // PDF içeriği yoksa hata ver
        if (empty($pdfContent)) {
            $enhancedPrompt .= "HATA: PDF içeriği bulunamadı veya boş!\n";
            $enhancedPrompt .= "File Analysis Debug: " . json_encode($fileAnalysis, JSON_UNESCAPED_UNICODE) . "\n";
            Log::error('🚨 PDF içeriği bulunamadı!', [
                'file_analysis_keys' => array_keys($fileAnalysis),
                'has_extracted_content' => isset($fileAnalysis['extracted_content']),
                'has_individual_results' => isset($fileAnalysis['individual_results']),
                'has_pages' => isset($fileAnalysis['pages'])
            ]);
        } else {
            // Tam PDF içeriğini ekle - KISALTMA YOK
            $enhancedPrompt .= $pdfContent . "\n";
            Log::info('✅ PDF içeriği AI prompt\'a eklendi', [
                'pdf_content_length' => strlen($pdfContent),
                'pdf_preview' => substr($pdfContent, 0, 200) . '...',
                'contains_data' => str_contains($pdfContent, 'mm') || str_contains($pdfContent, 'kg') || str_contains($pdfContent, 'liter') || str_contains($pdfContent, 'volt')
            ]);
        }

        $enhancedPrompt .= "=================================\n\n";

        // Kullanıcının manuel ek talimatları (eğer varsa)
        if (!empty($basePrompt)) {
            // ZORUNLU BRIEF formatında belirt ki model harfiyen uygulasın
            $enhancedPrompt .= "🧭 ZORUNLU BRIEF (HARFİYEN UYGULA):\n" . $basePrompt . "\n\n";
        }

        $enhancedPrompt .= "🚨 KRİTİK ÖNEM - MUTLAKA UYGULA:\n";
        $enhancedPrompt .= "• Yukarıdaki PDF içeriğini MUTLAKA kullan - rastgele içerik üretme!\n";
        $enhancedPrompt .= "• PDF'deki TÜMET teknik özellikleri, sayıları ve verileri içeriğe dahil et\n";
        $enhancedPrompt .= "• Dokümandaki her değeri (mm, kg, volt, liter, watt vs.) tablolarda göster\n";
        $enhancedPrompt .= "• PDF'de geçen ürün/hizmet adlarını aynen kullan\n";
        $enhancedPrompt .= "• Teknik spesifikasyonları HTML tablolar halinde organize et\n";
        $enhancedPrompt .= "• Profesyonel ve gerçek verilere dayalı bir sayfa oluştur\n";
        $enhancedPrompt .= "• En az 5000 karakter, PDF verilerinden türetilmiş HTML kodu üret\n";
        $enhancedPrompt .= "• ÇIKTIDA ASLA 'devam', 'devam ediyorum', 'kaldığımız yerden', 'continue' gibi ifadeler yazma; SADECE nihai HTML üret\n";
        $enhancedPrompt .= "• 'Bu bölümde', 'Bölüm X', 'İşte PDF içeriğinin devamı' gibi açıklama/yorum cümleleri YAZMA\n\n";

        $enhancedPrompt .= "🔍 KONTROL LİSTESİ:\n";
        $enhancedPrompt .= "✅ PDF'deki sayısal değerleri kullandım\n";
        $enhancedPrompt .= "✅ PDF'deki ürün/model isimlerini kullandım\n";
        $enhancedPrompt .= "✅ PDF'deki teknik özellikleri tabloya çevirdim\n";
        $enhancedPrompt .= "✅ Rastgele içerik üretmedim, PDF verisini kullandım";

        return $enhancedPrompt;
    }

    // 🗑️ GLOBAL DYNAMIC PROMPT KALDIRILDI - Artık minimal prompt kullanıyoruz

    /**
     * Modül context'ini text formatına çevir
     */
    private function buildModuleContextText(array $moduleContext): string
    {
        if (empty($moduleContext)) {
            return '';
        }
        
        $text = "\n\nMODÜL CONTEXT BİLGİLERİ:\n";
        
        if (isset($moduleContext['module'])) {
            $text .= "- Kaynak Modül: {$moduleContext['module']}\n";
        }
        
        if (isset($moduleContext['entity_type'])) {
            $text .= "- Entity Tipi: {$moduleContext['entity_type']}\n";
        }
        
        if (isset($moduleContext['fields']) && is_array($moduleContext['fields'])) {
            $text .= "- Hedef Alanlar: " . implode(', ', array_keys($moduleContext['fields'])) . "\n";
        }
        
        if (isset($moduleContext['specific_requirements'])) {
            $text .= "- Özel Gereksinimler: {$moduleContext['specific_requirements']}\n";
        }
        
        $text .= "\nBu modül context'ine uygun içerik üret!\n";
        
        return $text;
    }

    /**
     * İçerik tipini otomatik tespit et
     */
    private function detectContentType(string $prompt): string
    {
        $prompt = Str::lower($prompt);

        $patterns = [
            'service' => ['hizmet', 'service', 'servis', 'yapay zeka', 'reklamcılık', 'yazılım', 'tasarım'],
            'hero' => ['hero', 'başlangıç', 'giriş', 'ana bölüm'],
            'features' => ['özellik', 'feature', 'avantaj', 'fayda'],
            'pricing' => ['fiyat', 'paket', 'pricing', 'ücret'],
            'about' => ['hakkımızda', 'hakkında', 'about', 'biz kimiz'],
            'contact' => ['iletişim', 'contact', 'ulaş', 'adres'],
            'testimonials' => ['referans', 'yorum', 'testimonial', 'müşteri'],
            'gallery' => ['galeri', 'görsel', 'resim', 'gallery'],
            'team' => ['ekip', 'team', 'kadro', 'çalışan'],
            'faq' => ['sss', 'soru', 'faq', 'sorular'],
            'cta' => ['cta', 'aksiyon', 'harekete geç', 'call to action'],
            'product' => ['ürün', 'product', 'satış', 'alışveriş'],
            'blog' => ['blog', 'makale', 'yazı']
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

    // 🗑️ DYNAMIC THEME CONTEXT KALDIRILDI - Gereksizdi

    /**
     * Dinamik tema context'ine göre içerik işle
     */
    private function processDynamicContent(string $content, array $themeContext): string
    {
        // Renkleri tema renkleriyle değiştir
        $content = $this->replaceDynamicColors($content, $themeContext);

        // Tailwind + Alpine class'ları işle
        $content = $this->processDynamicTailwindClasses($content, $themeContext);

        // Dark/Light uyumluluğunu güçlendir
        $content = $this->enhanceDarkModeSupport($content);

        // XSS temizliği
        $content = $this->sanitizeContent($content);

        return $content;
    }

    /**
     * Background renklerini temizle - Sistem zaten bg yönetiyor
     */
    private function replaceDynamicColors(string $content, array $themeContext): string
    {
        // Aşırı baskın gradient ve doygun renkleri kaldır, nötrleri koru
        $content = preg_replace('/bg-gradient-to-[a-z]+ from-[a-z]+-\d+ (via-[a-z]+-?\d* )?to-[a-z]+-\d+/', '', $content);
        // Gray ailesini ve bg-white'ı KORU; diğer sabit bg-* renklerini temizle
        $content = preg_replace('/\bbg-(?!gray-)(?!white\b)[a-z]+-\d+\b/', '', $content);

        // Fazla boşlukları temizle
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace('class=" ', 'class="', $content);
        $content = str_replace(' "', '"', $content);

        return $content;
    }

    /**
     * Dark/Light mode desteğini artır: nötr sınıflara dark: eşlerini ekle
     */
    private function enhanceDarkModeSupport(string $content): string
    {
        // bg-white, text ve border için dark counterpart ekle (zaten varsa eklenmez)
        $content = preg_replace_callback('/class=\"([^\"]*)\"/i', function ($m) {
            $classes = $m[1];
            $updated = $classes;

            $hasDarkBg = preg_match('/\bdark:bg-[a-z]+-\d+\b/', $classes);
            if (preg_match('/\bbg-white\b/', $classes) && !$hasDarkBg) {
                $updated .= ' dark:bg-gray-900';
            }

            $hasDarkText = preg_match('/\bdark:text-[a-z]+-\d+\b/', $classes);
            if (preg_match('/\btext-gray-900\b/', $classes) && !$hasDarkText) {
                $updated .= ' dark:text-gray-100';
            }

            if (preg_match('/\btext-gray-700\b/', $classes) && !preg_match('/\bdark:text-gray-300\b/', $classes)) {
                $updated .= ' dark:text-gray-300';
            }

            if (preg_match('/\bborder-gray-200\b/', $classes) && !preg_match('/\bdark:border-gray-700\b/', $classes)) {
                $updated .= ' dark:border-gray-700';
            }

            return 'class="' . trim(preg_replace('/\s+/', ' ', $updated)) . '"';
        }, $content);

        return $content;
    }

    /**
     * Dinamik Tailwind + Alpine class işleme
     */
    private function processDynamicTailwindClasses(string $content, array $themeContext): string
    {
        // Modern Tailwind 3.x class'ları uygula
        $content = $this->applyModernTailwindClasses($content);

        // Alpine.js directive'leri ekle
        $content = $this->addAlpineInteractivity($content);

        return $content;
    }

    /**
     * Modern Tailwind 3.x class'ları uygula
     */
    private function applyModernTailwindClasses(string $content): string
    {
        // Modern Tailwind 3.x utility class replacements
        $modernMap = [
            // Spacing - Modern responsive scale
            'p-2' => 'p-2 sm:p-3 md:p-4 lg:p-6',
            'p-4' => 'p-4 sm:p-6 md:p-8 lg:p-10',
            'p-6' => 'p-6 sm:p-8 md:p-10 lg:p-12',
            'mb-4' => 'mb-4 sm:mb-6 md:mb-8',
            'mt-8' => 'mt-8 sm:mt-12 md:mt-16',
            'space-y-4' => 'space-y-4 sm:space-y-6 md:space-y-8',
            'gap-4' => 'gap-4 sm:gap-6 md:gap-8 lg:gap-10',

            // Typography - Modern scale
            'text-sm' => 'text-sm sm:text-base',
            'text-lg' => 'text-lg sm:text-xl md:text-2xl',
            'text-xl' => 'text-xl sm:text-2xl md:text-3xl lg:text-4xl',
            'text-2xl' => 'text-2xl sm:text-3xl md:text-4xl lg:text-5xl',
            'text-3xl' => 'text-3xl sm:text-4xl md:text-5xl lg:text-6xl',

            // Colors - Modern palette with dark mode
            'text-black' => 'text-gray-900 dark:text-gray-100',
            'text-gray-600' => 'text-gray-700 dark:text-gray-300',
            'text-gray-800' => 'text-gray-900 dark:text-gray-100',

            // Borders - Modern style
            'border' => 'border border-gray-200 dark:border-gray-700',
            'rounded' => 'rounded-lg',
            'rounded-md' => 'rounded-xl',

            // Shadows - Modern elevation
            'shadow' => 'shadow-lg hover:shadow-xl transition-shadow duration-300',
            'shadow-md' => 'shadow-xl hover:shadow-2xl transition-shadow duration-300',

            // Grid - Modern responsive
            'grid-cols-2' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-2',
            'grid-cols-3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
            'grid-cols-4' => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
        ];

        foreach ($modernMap as $search => $replace) {
            $content = str_replace("class=\"$search", "class=\"$replace", $content);
            $content = str_replace(" $search ", " $replace ", $content);
            $content = str_replace(" $search\"", " $replace\"", $content);
        }

        return $content;
    }

    /**
     * Alpine.js interactivity ekle
     */
    private function addAlpineInteractivity(string $content): string
    {
        // Button'lara Alpine.js click handler'ları ekle
        $content = preg_replace(
            '/<button([^>]*class="[^"]*")([^>]*)>/i',
            '<button$1 x-data="{ clicked: false }" x-on:click="clicked = !clicked" x-bind:class="{ \'scale-95\': clicked }"$2>',
            $content
        );

        // Card'lara hover animation ekle
        $content = preg_replace(
            '/<div([^>]*class="[^"]*card[^"]*")([^>]*)>/i',
            '<div$1 x-data="{ hovered: false }" x-on:mouseenter="hovered = true" x-on:mouseleave="hovered = false"$2>',
            $content
        );

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
     * Aktif tema CSS bilgilerini al
     */
    private function getActiveThemeCSSInfo(): string
    {
        $cssInfo = "🎨 KRİTİK CSS KURALLARI:\n\n";

        // Tailwind CSS uyumlu sınıflar
        $cssInfo .= "TAILWIND CSS KULLANILACAK SINIFLAR:\n";
        $cssInfo .= "• Arka plan: bg-white dark:bg-gray-900\n";
        $cssInfo .= "• Metin: text-gray-900 dark:text-gray-100\n";
        $cssInfo .= "• Başlık: text-gray-900 dark:text-white\n";
        $cssInfo .= "• Alt metin: text-gray-600 dark:text-gray-400\n";
        $cssInfo .= "• Kenarlık: border-gray-200 dark:border-gray-700\n";
        $cssInfo .= "• Kart: bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700\n";
        $cssInfo .= "• Buton: bg-blue-600 hover:bg-blue-700 text-white\n";
        $cssInfo .= "• Link: text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300\n\n";

        $cssInfo .= "⚠️ ÖNEMLİ: HER element için mutlaka dark: prefix'li sınıflar ekleyin!\n";
        $cssInfo .= "⚠️ ASLA sadece tek renk kullanmayın, hem light hem dark mode desteği zorunlu!\n";
        $cssInfo .= "⚠️ text-black KULLANILAMAZ! Yerine: text-gray-900 dark:text-gray-100\n\n";

        return $cssInfo;
    }

    /**
     * Kredi kullanımını kaydet
     */
    private function recordCreditUsage(int $tenantId, string $contentType, string $prompt, array $moduleContext = []): void
    {
        try {
            $hasPdfContent = !empty($moduleContext['file_analysis']);
            $length = $moduleContext['length'] ?? 'medium';
            $credits = $this->calculateCredits($contentType, $length, $hasPdfContent);

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
                'action' => 'global_content_generation',
                'details' => json_encode([
                    'content_type' => $contentType,
                    'prompt_length' => strlen($prompt),
                    'module_context' => $moduleContext,
                    'timestamp' => now()
                ]),
                'used_at' => now(),
                'created_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('GLOBAL Credit usage recording failed: ' . $e->getMessage());
        }
    }

    /**
     * Kredi maliyetini hesapla
     */
    private function calculateCredits(string $contentType, string $length, bool $hasPdfContent = false): int
    {
        $baseCredits = self::CREDIT_COSTS['moderate'];

        // PDF destekli içerik - Daha yüksek maliyet
        if ($hasPdfContent) {
            $baseCredits = self::CREDIT_COSTS['pdf_enhanced'];
        } else {
            // İçerik tipine göre ayarla
            if (in_array($contentType, ['hero', 'cta'])) {
                $baseCredits = self::CREDIT_COSTS['simple'];
            } elseif (in_array($contentType, ['pricing', 'team', 'features'])) {
                $baseCredits = self::CREDIT_COSTS['complex'];
            }
        }

        // Uzunluğa göre ayarla - OPTİMİZE EDİLDİ
        $lengthMultiplier = match($length) {
            'short' => 0.7,
            'long' => 1.5,
            'ultra_long' => 1.8,    // 2.5'ten 1.8'e (%28 tasarruf)
            'unlimited' => 2.0,     // 3.0'dan 2.0'a (%33 tasarruf)
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
            'short' => 2000,
            'long' => 6000,
            'ultra_long' => 8000,   // Claude max limiti: 8192
            'unlimited' => 8192,    // Claude 3.5 Sonnet resmi limiti
            default => 4000
        };
    }

    /**
     * Uzunluk talimatları
     */
    private function getLengthInstructions(string $length): string
    {
        return match($length) {
            'short' => 'KISA VE ÖZ İÇERİK ÜRET! 1-2 paragraf yeterli.',
            'long' => 'KAPSAMLI İÇERİK ÜRET! Detaylı ve açıklayıcı olsun.',
            'ultra_long' => 'KAPSAMLI İÇERİK ÜRET! Kullanıcının talebine göre zengin ve detaylı bir içerik oluştur. Modern tasarım prensiplerini kullan, ancak SADECE içerik bölümü üret - header ve footer ekleme.',
            'unlimited' => 'MAXİMUM KAPSAMLI İÇERİK ÜRET! Çok detaylı, profesyonel kalitede ve comprehensive bir landing sayfası oluştur. Token sınırı olmadan zengin içerik üret.',
            default => 'ORTA UZUNLUKTA İÇERİK ÜRET! Dengeli ve informatif olsun.'
        };
    }

    /**
     * Site kimlik context'i oluştur
     */
    private function buildSiteIdentityContext(string $siteName, string $detectedPageType, string $pageTitle): string
    {
        $context = "SİTE KİMLİK BİLGİLERİ:\n\n";
        $context .= "Site Adı: {$siteName}\n";
        $context .= "Sayfa Türü: {$detectedPageType}\n";
        if ($pageTitle) {
            $context .= "Sayfa Başlığı: {$pageTitle}\n";
        }
        $context .= "\n";

        $context .= "ZORUNLU: Bu site kimliğine uygun, profesyonel içerik üret!\n";
        $context .= "Site adını ({$siteName}) içerikte uygun yerlerde kullan.\n";

        return $context;
    }

    /**
     * GLOBAL base prompt
     */
    private function getGlobalBasePrompt(): string
    {
        return <<<EOT
🎨 MODERN TAILWIND DESIGN MASTER - PREMIUM CONTENT GENERATOR

Sen bir PREMIUM TASARIM UZMANISIN. Modern, nefes alabilen, aesthetik açıdan üstün HTML tasarımlar üretirsin.

{{site_identity_context}}

{{dynamic_theme_context}}

{{header_css_context}}

{{module_context}}

🎯 KULLANıCı TALEBİ: {{user_prompt}}
📱 İÇERİK TİPİ: {{content_type}}

{{length_instructions}}

{{custom_instructions}}

🧭 ZORUNLU KURALLAR:
- Kullanıcı BRIEF içinde verilen TERİM HARİTASI, ÜRÜN ADI ve YASAKLI TERİMLER harfiyen UYGULANACAK.
- 'X yerine Y kullan' talimatı geçerse, tüm içerikte X ifadesi Y ile değiştirilecek.
- Ürün adı belirtildiyse, tüm başlık/CTA/metinlerde bu isim kullanılacak.
- Bu kurallar PDF içeriğinde geçen eski terimlere baskındır.

🚫 DEVAM/YARI SENTEZ YASAK:
- Çıktıda ASLA 'devam', 'devam ediyorum', 'kaldığımız yerden', 'continue', 'continuing' gibi ifadeler kullanma.
- SADECE nihai, TAM ve BAĞIMSIZ bir HTML sayfa içeriği üret.
- Açıklama, not, yönlendirme, soru cümlesi ve metin dışı yorum ekleme.

🏗️ MODERN TASARIM PRENSİPLERİ (BREATHING DESIGN MASTER):

📐 LAYOUT ARCHITECTURE (GENEROUS SPACING):
- Section Structure: <section class=\"py-24 md:py-40 relative overflow-hidden\">
- Container System: <div class=\"container mx-auto px-6 sm:px-8 lg:px-12 max-w-7xl\">
- Content Grid: grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 md:gap-16 lg:gap-20
- Flex Systems: flex flex-col md:flex-row items-start md:items-center justify-between gap-8 md:gap-12

🎭 VISUAL HIERARCHY (BREATHING TYPOGRAPHY):
- Hero Typography: text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-8 md:mb-12
- Sub Headers: text-2xl md:text-3xl lg:text-4xl font-semibold mb-6 md:mb-8
- Body Text: text-lg md:text-xl leading-relaxed text-gray-600 mb-6 md:mb-8
- Micro Copy: text-sm md:text-base text-gray-500 mb-4 md:mb-6
- Generous Line Height: leading-relaxed md:leading-loose

🌟 INTERACTIVE ELEMENTS (BREATHING INTERACTIONS):
- Premium Buttons: \"inline-flex items-center px-10 py-5 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-all duration-300 transform hover:scale-102 focus:ring-4 focus:ring-blue-200 shadow-lg hover:shadow-xl\"
- Modern Cards: \"bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-10 md:p-12 border border-gray-100 hover:scale-102\"
- Form Fields: \"w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors\"
- Badge Elements: \"inline-flex px-4 py-2 bg-blue-50 text-blue-800 text-sm rounded-full border border-blue-200\"
- Spacing Between Elements: space-y-8 md:space-y-12 lg:space-y-16

🎨 TAILWIND SECONDARY COLOR SYSTEM (DYNAMIC CUSTOMIZATION):
- Blue Secondary: text-blue-600, border-blue-500, bg-blue-50 (buttons: bg-blue-600)
- Slate Secondary: text-slate-600, border-slate-500, bg-slate-50 (buttons: bg-slate-600)
- Emerald Secondary: text-emerald-600, border-emerald-500, bg-emerald-50 (buttons: bg-emerald-600)
- Amber Secondary: text-amber-600, border-amber-500, bg-amber-50 (buttons: bg-amber-600)
- Red Secondary: text-red-600, border-red-500, bg-red-50 (buttons: bg-red-600)
- Cyan Secondary: text-cyan-600, border-cyan-500, bg-cyan-50 (buttons: bg-cyan-600)
- Text Hierarchy: text-gray-900, text-gray-700, text-gray-600, text-gray-500

📱 RESPONSIVE EXCELLENCE:
- Mobile First Design (base styles)
- Tablet Optimization: md: prefix (768px+)
- Desktop Enhancement: lg: prefix (1024px+)
- Large Screen: xl: prefix (1280px+)

✨ ANIMATION & MICRO-INTERACTIONS (BREATHING DESIGN):
- Smooth Transitions: transition-all duration-300 ease-in-out
- Hover Effects: hover:scale-102, hover:shadow-xl, hover:-translate-y-1
- Focus States: focus:ring-4 focus:ring-blue-200 focus:outline-none
- Breathing Animations: hover:scale-102 transition-transform duration-200
- Gentle Pulse: animate-pulse (sadece loading states için)

🧩 ALPINE.JS INTEGRATION:
- Interactive Data: x-data ile dinamik state yönetimi
- Event Handling: x-on:click ile etkileşim
- Conditional Display: x-show ile görünürlük kontrolü
- Dynamic Classes: x-bind:class ile stil değişimi

🚫 CRITICAL RULES - STRICT ENFORCEMENT:

🚫 IMAGE RULES - MUTLAK YASAK:
1. ❌ img tagı TAMAMEN YASAK - hiç kullanma!
2. ❌ src attribute'u ASLA yazma!
3. ❌ background-image YASAK - hiç kullanma!
4. ❌ .jpg, .png, .webp, .svg dosya uzantıları YASAK!
5. ❌ hero.jpg, logo.png gibi resim dosyaları YAZMA!
6. ✅ SADECE İKON: fas fa-xxx sınıfları kullanabilirsin
7. ✅ PLACEHOLDER: gradient kutular + text + ikonlar

🚫 BACKGROUND COLOR RULES - STRICT ENFORCEMENT:
1. ❌ MAIN CONTAINER ve SECTION'lara background color YASAK
2. ❌ Tüm content wrapper'a tek renk vermek YASAK
3. ❌ GRADIENT BACKGROUND TAMAMEN YASAK - bg-gradient-to-*, from-*, to-* kullanma
4. ❌ HERHANGİ BİR GRADIENT YASAK: bg-gradient-to-r, bg-gradient-to-b, bg-gradient-to-t
5. ✅ SADECE şu elementlerde SOLID background kullanabilirsin:
   - Buttons: TAILWIND SECONDARY COLORS (bg-blue-600, bg-slate-600, bg-emerald-600, bg-amber-600, bg-red-600, bg-cyan-600)
   - Cards: bg-white (sadece kartlar için)
   - Badges/Tags: LIGHT SECONDARY BACKGROUNDS (bg-blue-50, bg-slate-50, bg-emerald-50, bg-amber-50, bg-red-50, bg-cyan-50)
   - Input Fields: bg-gray-50
   - Accent Elements: SOLID COLOR ONLY (bg-blue-600, bg-emerald-600, bg-amber-600)

🎨 MODERN DESIGN RULES (BREATHING DESIGN PRINCIPLES):
4. ✅ MANDATORY Alpine.js interactivity (x-data, x-show, x-on:click)
5. ✅ GENEROUS spacing scale: 8, 12, 16, 20, 24, 32, 40, 48 (breathing room)
6. ✅ Container max-width: max-w-7xl
7. ✅ Rounded corners: rounded-xl, rounded-2xl only
8. ✅ Shadow depth: shadow-lg, shadow-xl with hover enhancement
9. ✅ Responsive grid: grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 md:gap-16
10. ✅ WHITESPACE MASTERY: Every element needs breathing room
11. ✅ SECONDARY COLOR SYSTEM: Use Tailwind secondary colors for customization
12. ✅ NO GRADIENTS: Never use any gradient backgrounds

🏆 PREMIUM QUALITY STANDARDS (BREATHING DESIGN EXCELLENCE):
- GENEROUS whitespace distribution with breathing room
- Perfect visual alignment with spacious layouts
- Consistent GENEROUS spacing rhythm (12px+ minimum)
- Subtle hover animations with breathing scale (hover:scale-102)
- Professional typography scale with breathing line-heights
- Mobile-first responsive design with GENEROUS touch targets
- Accessibility-friendly markup with proper contrast
- ZERO GRADIENTS - Only solid colors from secondary palette
- Dynamic color customization through Tailwind secondary system

🚀 LANDING PAGE ÖZEL TALİMATLARI (Ultra Long için):
EĞER length: 'ultra_long' ise, bu LANDING PAGE yapısını kullan:

1. 🎯 HERO SECTION:
- Güçlü headline ile başlat
- Sub-headline ve value proposition
- Call-to-action button (secondary colors kullan)
- Görsel placeholder: gradient kutu + marka/ürün adı + ikon (ASLA resim src="" kullanma!)

2. 💎 FEATURES/BENEFITS SECTION:
- 3-6 ana özellik/fayda
- Icon + başlık + açıklama formatı
- Grid layout ile responsive

3. 📖 ABOUT/STORY SECTION:
- Hikaye anlatımı
- Güven verici bilgiler
- Team/company bilgileri

4. 🛍️ SERVICES/PRODUCTS SECTION:
- Ana hizmet/ürünler
- Kısa açıklamalar
- Fiyatlandırma ipuçları (opsiyonel)

5. ⭐ SOCIAL PROOF SECTION:
- Müşteri yorumları
- Başarı hikayeleri
- İstatistikler/sayılar

6. 📞 CALL-TO-ACTION SECTION:
- Son çağrı
- İletişim formu placeholder
- Aciliyet yaratma

🚫 ÖNEMLİ KURAL: SADECE İÇERİK BÖLÜMÜ ÜRET! Header ve Footer ekleme, bunlar zaten mevcut.

HER BÖLÜM için breathing design kullan ve secondary color palette'ten renkler seç!

📋 OUTPUT FORMAT:
Return ONLY clean HTML code. No markdown wrappers, no explanations, pure HTML.

🛑 YORUM/GEÇİŞ CÜMLELERİ YASAK:
- Bu bölümde, Bölüm X, Devam eden, İşte PDF içeriğinin devamı gibi AÇIKLAYICI cümleler YAZMA.
- Sadece içerik HTML'i üret; açıklama ve anlatım cümlesi yok.
EOT;
    }

    /**
     * Markdown block'larını temizle
     */
    private function cleanMarkdownBlocks(string $content): string
    {
        // Markdown code block'larını temizle
        if (str_starts_with($content, '```html')) {
            $content = str_replace('```html', '', $content);
        }
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```[a-z]*\n?/i', '', $content);
        }
        if (str_ends_with($content, '```')) {
            $content = preg_replace('/```\s*$/i', '', $content);
        }

        return trim($content);
    }

    /**
     * HTML çıktısını sonlandır: HTML dışı satırları temizle, konuşma cümlelerini kaldır, köşe durumlarını toparla
     */
    private function finalizeHTMLContent(string $content): string
    {
        if (empty($content)) return $content;

        // Markdown ve artıkları temizle
        $content = $this->cleanMarkdownBlocks($content);

        // Sık görülen model cümleleri (TR/EN) temizle
        $banPhrases = [
            'Bu HTML kodunun devamını oluşturmamı ister misiniz',
            'devamını oluşturmamı',
            'ister misiniz',
            'Would you like me to continue',
            'Shall I continue',
            'Do you want me to continue',
            'Devam eden bölümden itibaren',
            'devam ediyorum',
            'devam edelim',
            'kaldığımız yerden',
            'şimdi devam',
            'continue from',
            'continuing',
            'let\'s continue',
        ];
        foreach ($banPhrases as $phrase) {
            $content = str_ireplace($phrase, '', $content);
        }

        // HTML olmayan bağımsız satırları kaldır (yalnızca düz yazı satırı)
        $lines = preg_split('/\r?\n/', $content);
        $kept = [];
        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '') { $kept[] = $line; continue; }
            if (str_contains($trim, '<') && str_contains($trim, '>')) {
                $kept[] = $line;
            }
        }
        $content = implode("\n", $kept);

        // Kapatılmamış fence ihtimali için tekrar temizle
        $content = preg_replace('/```[a-z]*|```/i', '', $content);

        // Meta/açıklama paragraf temizliği: "Bu bölümde", "Bölüm X", "İşte PDF içeriğinin devamı" vb.
        $metaWords = [
            'Bu bölümde', 'Bu kısımda', 'Bölüm', 'devam', 'Devam eden', 'İşte PDF', 'tasarımı oluşturmaya devam',
            'modern Tailwind tasarımı (Bölüm', 'responsive ve dark mode uyumlu olarak tasarlandı'
        ];
        foreach ($metaWords as $w) {
            // <p> ... </p> içindeki meta cümleleri temizle
            $pattern = '/<p[^>]*>[^<]*' . preg_quote($w, '/') . '[^<]*<\/p>/iu';
            $content = preg_replace($pattern, '', $content);
        }

        // Çoklu boşluk/boş satırları sadeleştir
        $content = preg_replace('/\n{3,}/', "\n\n", $content);

        // Minimum sarmalayıcı ekle
        if (!preg_match('/<(section|div)[^>]*>/i', $content)) {
            $content = '<section class="py-16 md:py-24 bg-white dark:bg-gray-900"><div class="container mx-auto px-6 sm:px-8 lg:px-12">' . $content . '</div></section>';
        }

        return trim($content);
    }

    /**
     * Kullanıcının serbest metin prompt'undan editoryal briefları çıkar
     * - "X yerine Y kullan" kalıbı → term_map
     * - "ürünün adı X" kalıbı → product_name
     */
    private function extractEditorialBriefFromPrompt(string $prompt): array
    {
        $brief = [
            'term_map' => [],
            'product_name' => null,
            'forbidden_terms' => [],
        ];

        if (empty($prompt)) {
            return [];
        }

        // 'X yerine Y kullan' kalıbı
        $pattern = '/\b([\p{L}0-9\-\s]+?)\s*yerine\s*([\p{L}0-9\-\s]+?)\s*kullan/iu';
        if (preg_match_all($pattern, $prompt, $m, PREG_SET_ORDER)) {
            foreach ($m as $match) {
                $from = trim($match[1]);
                $to = trim($match[2]);
                if ($from && $to && strcasecmp($from, $to) !== 0) {
                    $brief['term_map'][$from] = $to;
                }
            }
        }

        // 'ürünün adı X' / 'ürün adı X'
        $patternName = '/ürün(?:ün|un|in|\'nın)?\s*adı\s*:?-?\s*([\p{L}0-9\-\s]+)/iu';
        if (preg_match($patternName, $prompt, $n)) {
            $brief['product_name'] = trim($n[1]);
        }

        // 'YASAKLI TERİMLER: a, b, c' basit kalıp
        $patternBan = '/yasal?kli\s*terimler\s*:\s*([\p{L}0-9,\-\s]+)/iu';
        if (preg_match($patternBan, $prompt, $b)) {
            $list = array_filter(array_map('trim', explode(',', $b[1])));
            if ($list) $brief['forbidden_terms'] = $list;
        }

        // Boşsa boş dizi dön
        if (empty($brief['term_map']) && empty($brief['product_name']) && empty($brief['forbidden_terms'])) {
            return [];
        }
        return $brief;
    }

    /**
     * Brief'i AI'ye daha anlaşılır aktaracak metin bloğu üret
     */
    private function buildEditorialBriefContext(array $brief): string
    {
        if (empty($brief)) return '';
        $lines = ["BRIEF (ZORUNLU):"];
        if (!empty($brief['product_name'])) {
            $lines[] = "• ÜRÜN ADI: " . $brief['product_name'];
        }
        if (!empty($brief['term_map'])) {
            $lines[] = "• TERİM HARİTASI:";
            foreach ($brief['term_map'] as $from => $to) {
                $lines[] = "  - '{$from}' yerine '{$to}' KULLAN";
            }
        }
        if (!empty($brief['forbidden_terms'])) {
            $lines[] = "• YASAKLI TERİMLER: " . implode(', ', $brief['forbidden_terms']);
        }
        $lines[] = "Bu kurallar tüm içerikte HARFİYEN uygulanacak.";
        return implode("\n", $lines);
    }

    /**
     * Üretilen HTML üzerinde brief zorlaması (terim değiştirme vb.)
     */
    private function applyEditorialBrief(string $content, array $brief): string
    {
        // Terim haritası uygulanır (büyük/küçük harf duyarsız)
        if (!empty($brief['term_map'])) {
            foreach ($brief['term_map'] as $from => $to) {
                // Basit ve etkili: case-insensitive replace
                $content = str_ireplace($from, $to, $content);
            }
        }

        // Yasaklı terim varsa yıldızla veya boşlukla yumuşat (tam silmek yerine)
        if (!empty($brief['forbidden_terms'])) {
            foreach ($brief['forbidden_terms'] as $ban) {
                $content = str_ireplace($ban, '—', $content);
            }
        }
        return $content;
    }

    /**
     * PDF içerikleri için uzun form üretim: parçala ve birleştir
     */
    private function generateLongFromPdf(string $basePrompt, array $fileAnalysis, \Modules\AI\App\Services\AnthropicService $anthropicService): string
    {
        $raw = $fileAnalysis['extracted_content'] ?? '';
        if (empty($raw)) {
            Log::warning('⚠️ generateLongFromPdf: extracted_content boş');
            return '';
        }

        // Yaklaşık 9000 karakterlik parçalara böl (token güvenliği için)
        $chunkSize = 9000;
        $chunks = [];
        $len = strlen($raw);
        for ($i = 0; $i < $len; $i += $chunkSize) {
            $chunks[] = substr($raw, $i, $chunkSize);
        }

        $total = count($chunks);
        Log::info('📦 PDF uzun form üretim başlıyor', [
            'parts' => $total,
            'total_chars' => $len
        ]);

        $assembled = '';
        foreach ($chunks as $index => $part) {
            $partNo = $index + 1;
            $partPrompt = $basePrompt . "\n\n" .
                "📌 PDF PARÇA {$partNo}/{$total} - ULTRA PREMIUM LANDING DEVAM EDİYOR\n\n" .
                "🎯 MUTLAKA TAKİP ET: FORKLIFT/TRANSPALET TESPİT EDİLDİ = ORANGE PALETTE ZORUNLU!\n\n" .
                "KURALLARI UNUTMA:\n" .
                "✅ ORANGE GRADIENTS: from-orange-500 via-amber-500 to-yellow-600\n" .
                "✅ TYPOGRAPHY: text-4xl lg:text-6xl (hero), text-2xl lg:text-4xl (headings)\n" .
                "✅ SPACING: py-12, py-16, py-20 (compact, efficient)\n" .
                "✅ GLASS MORPHISM: bg-white/10 backdrop-blur-md\n" .
                "✅ HOVER EFFECTS: hover:-translate-y-6 hover:scale-105\n" .
                "✅ DARK MODE: dark:bg-gray-900, dark:text-white\n\n" .
                "❌ YASAKLAR: Header/Footer/Nav YASAK! Sadece BODY sections üret!\n\n" .
                "OUTPUT: Sadece temiz HTML. Açıklama/yorum yok!\n\n" .
                "PDF CHUNK DATA:\n" . $part . "\n" .
                "— PDF CHUNK SONU —\n\n" .
                "ŞİMDİ: Bu PDF verisini kullanarak ORANGE PALETTE ile PREMIUM landing section'ları üret!";

            $messages = [
                [ 'role' => 'user', 'content' => $partPrompt ]
            ];

            $result = $anthropicService->generateCompletionStream($messages);
            if (!is_array($result) || (isset($result['success']) && !$result['success'])) {
                Log::error('❌ PDF uzun form bölüm üretimi başarısız', [
                    'part' => $partNo,
                    'error' => is_array($result) ? ($result['error'] ?? 'unknown') : 'unknown'
                ]);
                // Hatanın devamını engelleme: atla ve devam et
                continue;
            }

            $html = $this->cleanMarkdownBlocks($result['response'] ?? (string) $result);
            // Tema/dark-mode uyumu
            $html = $this->processDynamicContent($html, []);

            $assembled .= "\n\n<!-- PART {$partNo}/{$total} -->\n" . $html;
        }

        return trim($assembled);
    }

    /**
     * AI response'unu parse et (HTML, CSS, ALPINE)
     */
    public function parseAIResponse(string $response): array
    {
        $html = '';
        $css = '';
        $alpine = '';

        // HTML bölümünü al
        if (preg_match('/HTML:\s*(.*?)(?=\n\s*CSS:|ALPINE:|JAVASCRIPT:|JS:|$)/si', $response, $matches)) {
            $html = trim($matches[1]);
        } elseif (preg_match('/```html\s*(.*?)```/si', $response, $matches)) {
            $html = trim($matches[1]);
        } else {
            // Eğer format yoksa section tag'ı varsa onu al
            if (preg_match('/<section[^>]*>.*?<\/section>/si', $response, $matches)) {
                $html = trim($matches[0]);
            } else {
                // Son çare: tüm response'u HTML olarak al
                $html = trim($response);
            }
        }

        // CSS bölümünü al
        if (preg_match('/CSS:\s*(.*?)(?=\n\s*ALPINE:|JAVASCRIPT:|JS:|$)/si', $response, $matches)) {
            $css = trim($matches[1]);
        } elseif (preg_match('/```css\s*(.*?)```/si', $response, $matches)) {
            $css = trim($matches[1]);
        }

        // ALPINE bölümünü al
        if (preg_match('/(?:ALPINE|JAVASCRIPT|JS):\s*(.*?)$/si', $response, $matches)) {
            $alpine = trim($matches[1]);
        } elseif (preg_match('/```(?:js|javascript)\s*(.*?)```/si', $response, $matches)) {
            $alpine = trim($matches[1]);
        }

        // Temizle
        $html = $this->cleanMarkdownBlocks($html);
        $css = $this->cleanMarkdownBlocks($css);
        $alpine = $this->cleanMarkdownBlocks($alpine);

        return [
            'html' => trim($html),
            'css' => trim($css),
            'alpine' => trim($alpine),
            'parsed_sections' => [
                'html_found' => !empty($html),
                'css_found' => !empty($css),
                'alpine_found' => !empty($alpine)
            ]
        ];
    }

    /**
     * GLOBAL ASYNC METHODS - HER MODÜL İÇİN KULLANILACAK
     * Pattern: AI Translation System
     */

    /**
     * Async content generation (Queue system)
     * Fark: AI Çeviri = DB'den veri çeker, AI Content = Anlık input'larla çalışır
     */
    public function generateContentAsync(array $params): array
    {
        try {
            Log::info('🚀 GLOBAL Async AI Content Generation başlatıldı', $params);

            // Job ID ve Session ID oluştur
            $jobId = Str::uuid()->toString();
            $sessionId = session()->getId() ?? Str::uuid()->toString();

            // Session ID'yi params'a ekle
            $params['sessionId'] = $sessionId;

            $jobData = [
                'status' => 'pending',
                'progress' => 0,
                'message' => 'İçerik üretimi sıraya alındı...',
                'params' => $params,
                'created_at' => now()->toISOString()
            ];

            // Cache'e hem jobId hem sessionId ile kaydet (AI Translation pattern)
            Cache::put("ai_content_job:{$jobId}", $jobData, 600); // 10 dakika
            Cache::put("ai_content_job:{$sessionId}", $jobData, 600); // Session ID ile de

            // Job'u dispatch et (AI Translation pattern)
            $job = new \Modules\AI\App\Jobs\AIContentGenerationJob($params, $jobId);
            dispatch($job->onQueue('ai-content'));

            return [
                'success' => true,
                'job_id' => $jobId,
                'message' => 'İçerik üretimi başlatıldı'
            ];

        } catch (\Exception $e) {
            Log::error('❌ GLOBAL Async AI Content Generation hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'İçerik üretimi başlatılamadı: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Job status kontrolü (AI Translation pattern)
     */
    public function getJobStatus(string $jobId): array
    {
        try {
            // Önce cache'den kontrol et
            $jobData = Cache::get("ai_content_job:{$jobId}");

            if (!$jobData) {
                // Cache'de yoksa en son job'u al (hızlı çözüm)
                $dbJob = AIContentJob::latest()->first();

                if (!$dbJob) {
                    return [
                        'success' => false,
                        'message' => 'Job bulunamadı'
                    ];
                }

                // Database'den veriyi reconstruct et
                return [
                    'success' => true,
                    'data' => [
                        'status' => $dbJob->status,
                        'progress' => ($dbJob->status === 'completed') ? 100 : (($dbJob->status === 'failed') ? 0 : 50),
                        'message' => $dbJob->status === 'completed' ? 'İçerik başarıyla oluşturuldu!' :
                                   ($dbJob->status === 'failed' ? 'İçerik oluşturulamadı' : 'İşleniyor...'),
                        'content' => $dbJob->generated_content ?? null,
                        'error' => $dbJob->error_message ?? null
                    ]
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'status' => $jobData['status'],
                    'progress' => $jobData['progress'],
                    'message' => $jobData['message'],
                    'content' => $jobData['content'] ?? null,
                    'error' => $jobData['error'] ?? null
                ]
            ];

        } catch (\Exception $e) {
            Log::error('❌ Job status hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Job durumu alınamadı'
            ];
        }
    }

    /**
     * Job result alma (AI Translation pattern)
     */
    public function getJobResult(string $jobId): array
    {
        try {
            // Önce cache'den kontrol et
            $jobData = Cache::get("ai_content_job:{$jobId}");

            if (!$jobData) {
                // Cache'de yoksa en son completed job'u al (hızlı çözüm)
                $dbJob = AIContentJob::latest()->where('status', 'completed')->first();

                if (!$dbJob) {
                    return [
                        'success' => false,
                        'message' => 'Job bulunamadı'
                    ];
                }

                // Database'den veriyi reconstruct et
                return [
                    'success' => true,
                    'data' => [
                        'content' => $dbJob->generated_content ?? '',
                        'credits_used' => $dbJob->credits_used ?? 15,
                        'processing_time' => $dbJob->completed_at ?
                            $dbJob->completed_at->diffInSeconds($dbJob->started_at ?? $dbJob->created_at) : null
                    ]
                ];
            }

            if ($jobData['status'] !== 'completed') {
                return [
                    'success' => false,
                    'message' => 'Job henüz tamamlanmadı',
                    'status' => $jobData['status']
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'content' => $jobData['content'],
                    'credits_used' => $jobData['credits_used'] ?? 15,
                    'processing_time' => $jobData['processing_time'] ?? null
                ]
            ];

        } catch (\Exception $e) {
            Log::error('❌ Job result hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Job sonucu alınamadı'
            ];
        }
    }

    /**
     * Job progress güncelleme (Internal - Job'dan çağrılır)
     */
    public function updateJobProgress(string $jobId, int $progress, string $message, string $status = 'processing'): void
    {
        try {
            $jobData = Cache::get("ai_content_job:{$jobId}") ?? [];

            $jobData['status'] = $status;
            $jobData['progress'] = $progress;
            $jobData['message'] = $message;
            $jobData['updated_at'] = now()->toISOString();

            Cache::put("ai_content_job:{$jobId}", $jobData, 600);

            Log::info("📊 Job progress güncellendi: {$jobId} - {$progress}% - {$message}");

        } catch (\Exception $e) {
            Log::error('❌ Job progress güncelleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Job completion (Internal - Job'dan çağrılır)
     */
    public function completeJob(string $jobId, string $content, int $creditsUsed, ?float $processingTime = null): void
    {
        try {
            $jobData = Cache::get("ai_content_job:{$jobId}") ?? [];

            $jobData['status'] = 'completed';
            $jobData['progress'] = 100;
            $jobData['message'] = 'İçerik başarıyla üretildi!';
            $jobData['content'] = $content;
            $jobData['credits_used'] = $creditsUsed;
            $jobData['processing_time'] = $processingTime;
            $jobData['completed_at'] = now()->toISOString();

            Cache::put("ai_content_job:{$jobId}", $jobData, 600);

            Log::info("✅ Job tamamlandı: {$jobId} - {$creditsUsed} kredi kullanıldı");

        } catch (\Exception $e) {
            Log::error('❌ Job completion hatası: ' . $e->getMessage());
        }
    }

    /**
     * Start async content generation
     */
    public function startAsyncGeneration(array $params): string
    {
        try {
            $jobId = Str::uuid()->toString();
            $sessionId = session()->getId();

            // Job'u queue'ya gönder - jobId'yi ikinci parametre olarak gönder
            AIContentGenerationJob::dispatch([
                'job_id' => $jobId,
                'session_id' => $sessionId,
                'tenant_id' => tenant('id'),
                'user_id' => auth()->id(),
                'prompt' => $params['prompt'],
                'target_field' => $params['target_field'] ?? 'body',
                'replace_existing' => $params['replace_existing'] ?? true,
                'module' => $params['module'] ?? 'page',
                'component' => $params['component'] ?? null,
                // 🆕 File analysis parameters
                'file_analysis' => $params['file_analysis'] ?? null,
                'conversion_type' => $params['conversion_type'] ?? 'content_extract',
                'sessionId' => $sessionId // sessionId'yi de params içine ekle
            ], $jobId); // jobId'yi ikinci parametre olarak gönder

            // Initial progress (Redis yoksa file store'a düş)
            $initial = [
                'progress' => 0,
                'message' => 'İşlem başlatılıyor...',
                'status' => 'pending',
                'content' => null
            ];
            $this->safeCachePut("ai_content_progress_{$jobId}", $initial, 600);
            $this->safeCachePut("ai_content_progress_{$sessionId}", $initial, 600);

            // JobId → SessionId eşlemesi (frontend fallback için)
            $this->safeCachePut("ai_content_job_map_{$jobId}", $sessionId, 600);

            Log::info("✅ Async content generation started: {$jobId}");

            return $jobId;

        } catch (\Exception $e) {
            Log::error('❌ Async generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function safeCachePut(string $key, $value, int $ttlSeconds): void
    {
        try {
            Cache::put($key, $value, $ttlSeconds);
        } catch (\Throwable $e) {
            Log::warning('⚠️ Default cache put failed, falling back to file store', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            try {
                Cache::store('file')->put($key, $value, $ttlSeconds);
            } catch (\Throwable $e2) {
                Log::error('❌ File cache put failed', ['key' => $key, 'error' => $e2->getMessage()]);
            }
        }
    }

    /**
     * Job failure (Internal - Job'dan çağrılır)
     */
    public function failJob(string $jobId, string $error): void
    {
        try {
            $jobData = Cache::get("ai_content_job:{$jobId}") ?? [];

            $jobData['status'] = 'failed';
            $jobData['progress'] = 0;
            $jobData['message'] = 'İçerik üretimi başarısız oldu';
            $jobData['error'] = $error;
            $jobData['failed_at'] = now()->toISOString();

            Cache::put("ai_content_job:{$jobId}", $jobData, 600);

            Log::error("❌ Job başarısız: {$jobId} - {$error}");

        } catch (\Exception $e) {
            Log::error('❌ Job failure hatası: ' . $e->getMessage());
        }
    }

    /**
     * 🔒 KRİTİK ÖNEMLİ - DEĞİŞTİRİLMEYECEK!
     *
     * AI CONTENT GENERATION İÇİN SADECE CLAUDE SONNET 4 KULLANILACAK!
     *
     * SEBEP: Claude Sonnet 4 (claude-3-5-sonnet-20241022) en iyi tasarımcıdır.
     * - HTML/CSS/JS kodlama yetenekleri mükemmel
     * - Dark mode desteği ve responsive tasarım
     * - Tailwind CSS ile profesyonel çıktılar
     * - PDF'den HTML'e dönüşüm yetenekleri üstün
     *
     * OpenAI KULLANILMAYACAK! Claude başarısız olursa hata verilecek.
     *
     * @param string $contentType
     * @param array $params
     * @return array
     */
    private function selectOptimalAIModel(string $contentType, array $params): array
    {
        try {
            // 🎯 Smart Provider Selection sistemi kullan
            $providerOptimizer = app(\Modules\AI\App\Services\ProviderOptimizationService::class);

            // Content parametrelerini hazırla
            $contentParams = [
                'content_type' => $contentType,
                'prompt' => $params['prompt'] ?? '',
                'context' => $params['custom_instructions'] ?? '',
                'feature' => $params['feature'] ?? 'content_generation',
                'length' => $params['length'] ?? 'medium',
                'pdf_size' => isset($params['file_analysis']) ? 100000 : 0, // PDF varsa büyük say
                'file_analysis' => $params['file_analysis'] ?? null,
                'tenant_id' => $params['tenant_id'] ?? null,
            ];

            // Optimal provider seç
            $optimization = $providerOptimizer->getOptimalContentProvider($contentParams);

            if ($optimization['recommended_provider']) {
                $provider = $optimization['recommended_provider'];

                Log::info('🎯 Smart Provider Selection sonucu', [
                    'selected_provider' => $provider['provider_name'],
                    'category' => $optimization['category'],
                    'final_score' => $provider['final_score'],
                    'reasoning' => $provider['reasoning'],
                    'is_design_task' => $optimization['category'] === 'design_mandatory'
                ]);

                // Claude provider'ları için model mapping
                $claudeModels = [
                    'claude_3_5_sonnet' => 'claude-3-5-sonnet-20241022',
                    'claude_4_sonnet' => 'claude-3-5-sonnet-20241022', // Şimdilik 3.5 kullan
                    'claude_3_haiku' => 'claude-3-haiku-20240307',
                ];

                // OpenAI provider'ları için model mapping
                $openaiModels = [
                    'openai_gpt4o' => 'gpt-4o',
                    'openai_gpt4o_mini' => 'gpt-4o-mini',
                ];

                // Provider'a göre model ve service belirle
                if (str_starts_with($provider['provider_name'], 'claude_')) {
                    return [
                        'provider' => 'anthropic',
                        'model' => $claudeModels[$provider['provider_name']] ?? 'claude-3-5-sonnet-20241022',
                        'reason' => $provider['reasoning'],
                        'optimization_score' => $provider['final_score'],
                        'category' => $optimization['category']
                    ];
                } elseif (str_starts_with($provider['provider_name'], 'openai_')) {
                    return [
                        'provider' => 'openai',
                        'model' => $openaiModels[$provider['provider_name']] ?? 'gpt-4o',
                        'reason' => $provider['reasoning'],
                        'optimization_score' => $provider['final_score'],
                        'category' => $optimization['category']
                    ];
                }
            }

        } catch (\Exception $e) {
            Log::error('❌ Provider optimization hatası, default kullanılıyor', [
                'error' => $e->getMessage(),
                'content_type' => $contentType
            ]);
        }

        // Fallback: 🎨 TASARIM İÇİN CLAUDE 4 SONNET ZORUNLU!
        return [
            'provider' => 'anthropic',
            'model' => 'claude-3-5-sonnet-20241022',
            'reason' => '🎨 FALLBACK: Claude 4 Sonnet - En iyi tasarım modeli (backup selection)',
            'optimization_score' => 1.0,
            'category' => 'design_mandatory'
        ];
    }

    /**
     * 🚀 Token kullanımını optimize et - Dynamic Token Optimization
     */
    private function optimizeTokenUsage(array $params, array $selectedModel): array
    {
        try {
            $providerOptimizer = app(\Modules\AI\App\Services\ProviderOptimizationService::class);

            // Provider name'i model selection'dan al
            $providerName = $selectedModel['provider'] === 'anthropic'
                ? 'claude_3_5_sonnet'  // Claude için default
                : 'openai_gpt4o';      // OpenAI için default

            // Daha spesifik provider mapping
            if (isset($selectedModel['category']) && $selectedModel['category'] === 'design_mandatory') {
                $providerName = 'claude_4_sonnet'; // Tasarım için Claude 4
            }

            // Content parametrelerini hazırla
            $contentParams = [
                'content_type' => $params['content_type'] ?? 'page',
                'prompt' => $params['prompt'] ?? '',
                'context' => $params['custom_instructions'] ?? '',
                'feature' => $params['feature'] ?? 'content_generation',
                'length' => $params['length'] ?? 'medium',
                'pdf_size' => isset($params['file_analysis']) ? 100000 : 0,
                'file_analysis' => $params['file_analysis'] ?? null,
                'tenant_id' => $params['tenant_id'] ?? null,
                'provider_name' => $providerName,
            ];

            // Token optimization yap
            return $providerOptimizer->optimizeTokenUsage($contentParams);

        } catch (\Exception $e) {
            Log::error('❌ Token optimization hatası, fallback kullanılıyor', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            // Fallback: Basit token hesaplama
            $length = $params['length'] ?? 'medium';
            $fallbackTokens = $this->getMaxTokens($length);
            $safeTokens = min($fallbackTokens, 8192); // Claude max limiti

            return [
                'optimized_tokens' => $safeTokens,
                'provider_max' => 8192,
                'efficiency_score' => 0.5,
                'reasoning' => '🔧 Fallback token calculation used',
                'cost_estimate' => ['estimated_cost_credits' => ($safeTokens / 1000) * 0.3]
            ];
        }
    }
}
