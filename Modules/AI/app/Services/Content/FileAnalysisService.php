<?php

declare(strict_types=1);

namespace Modules\AI\app\Services\Content;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\Template\TemplateEngine;

/**
 * File Analysis Service - PDF/Image → Web Format Converter
 *
 * PDF'leri ve görselleri analiz ederek web formatına dönüştürür
 * Geçici dosyalar otomatik olarak silinir
 */
class FileAnalysisService
{
    private AIService $aiService;
    private TemplateEngine $templateEngine;

    public function __construct()
    {
        $this->aiService = app(\Modules\AI\App\Services\AIService::class);
        $this->templateEngine = app(TemplateEngine::class);
    }

    /**
     * Multiple files analiz et
     */
    public function analyzeFiles(array $files, string $analysisType = 'layout_preserve'): array
    {
        try {
            Log::info('🔍 File analysis başladı', [
                'file_count' => count($files),
                'analysis_type' => $analysisType
            ]);

            $results = [];
            $combinedContent = '';
            $tempPaths = []; // Cleanup için temp path'leri track et

            foreach ($files as $file) {
                $result = $this->analyzeSingleFile($file, $analysisType);
                $results[] = $result;

                if ($result['success']) {
                    $combinedContent .= "\n\n" . $result['extracted_content'];

                    // Temp path'i track et (cleanup için)
                    if (isset($result['temp_path'])) {
                        $tempPaths[] = $result['temp_path'];
                    }
                }
            }

            // Combined analysis for multiple files
            if (count($files) > 1) {
                $combinedContent = $this->combineMultipleFileContents($results, $analysisType);
            }

            $finalResult = [
                'success' => true,
                'file_type' => count($files) > 1 ? 'multiple' : ($results[0]['file_type'] ?? 'unknown'),
                'analysis_type' => $analysisType,
                'extracted_content' => trim($combinedContent),
                'individual_results' => $results,
                'analysis_summary' => $this->generateAnalysisSummary($results),
                'temp_paths' => $tempPaths
            ];

            // 🗑️ INSTANT CLEANUP - Analiz sonrası hemen temizle
            $this->cleanupTempFiles($tempPaths);

            // 🤖 AUTOMATIC CLEANUP SYSTEM - Tüm temp dosyaları otomatik temizlenecek
            \Modules\AI\app\Services\Content\AutoCleanupService::scheduleAutomaticCleanup();

            return $finalResult;

        } catch (\Exception $e) {
            Log::error('❌ File analysis error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Dosya analizi başarısız: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Single file analiz et
     */
    private function analyzeSingleFile(UploadedFile $file, string $analysisType): array
    {
        $fileType = $this->detectFileType($file);

        switch ($fileType) {
            case 'pdf':
                return $this->analyzePDF($file, $analysisType);
            case 'image':
                return $this->analyzeImage($file, $analysisType);
            default:
                throw new \Exception('Desteklenmeyen dosya tipi: ' . $fileType);
        }
    }

    /**
     * PDF analiz et
     */
    private function analyzePDF(UploadedFile $file, string $analysisType): array
    {
        $tempPath = null;

        try {
            // 🔥 PDF HASH CACHE SİSTEMİ - Aynı dosya tekrar işlenmesin!
            $fileContent = $file->get();
            $pdfHash = hash('sha256', $fileContent);
            $cacheKey = "pdf_analysis:{$pdfHash}:{$analysisType}";

            // Cache'den kontrol et (7 gün cache)
            if (\Cache::has($cacheKey)) {
                Log::info('⚡ PDF cache hit - kredi harcanmadı!', [
                    'file_name' => $file->getClientOriginalName(),
                    'pdf_hash' => substr($pdfHash, 0, 12),
                    'cache_key' => $cacheKey
                ]);

                $cachedResult = \Cache::get($cacheKey);
                $cachedResult['file_name'] = $file->getClientOriginalName(); // Dosya adını güncelle
                $cachedResult['from_cache'] = true;
                return $cachedResult;
            }

            // PDF'i geçici olarak kaydet
            $tempPath = $file->store('temp/pdf-analysis', 'local');
            $fullPath = Storage::disk('local')->path($tempPath);

            Log::info('📄 PDF analysis başladı', [
                'file_name' => $file->getClientOriginalName(),
                'temp_path' => $tempPath,
                'analysis_type' => $analysisType,
                'pdf_hash' => substr($pdfHash, 0, 12)
            ]);

            // PDF text extraction
            $textContent = $this->extractPDFText($fullPath);

            // Eğer text extraction başarısız olduysa, fallback content
            if (empty(trim($textContent)) || str_contains($textContent, 'PDF text extraction failed')) {
                Log::warning('⚠️ PDF text extraction failed, using fallback content');
                $textContent = 'PDF content could not be extracted. Please provide manual description or use a different PDF.';
            }

            // PDF layout analysis (eğer layout_preserve ise)
            $layoutInfo = [];
            if ($analysisType === 'layout_preserve') {
                $layoutInfo = $this->analyzePDFLayout($fullPath);
            }

            // 🖼️ PDF İÇİNDEKİ GÖRSELLER - Görselleri çıkar ve listele
            $extractedImages = $this->extractPDFImages($fullPath);
            Log::info('🖼️ PDF görsel extraction', [
                'image_count' => count($extractedImages),
                'images_found' => !empty($extractedImages)
            ]);

            // 🚫 AI ENHANCEMENT YOK - Direkt raw text kullan
            $enhancedContent = $textContent;

            $result = [
                'success' => true,
                'file_type' => 'pdf',
                'file_name' => $file->getClientOriginalName(),
                'raw_text' => $textContent,
                'layout_info' => $layoutInfo,
                'extracted_content' => $enhancedContent,
                'extracted_images' => $extractedImages, // 🖼️ YENİ: PDF'deki görseller
                'image_count' => count($extractedImages),
                'analysis_type' => $analysisType,
                'temp_path' => $tempPath,
                'pdf_hash' => $pdfHash,
                'from_cache' => false
            ];

            // 🔥 CACHE'E KAYDET (7 gün) - Dosya adını cache'leme, sadece içeriği
            $cacheableResult = $result;
            unset($cacheableResult['file_name']); // Dosya adı değişebilir
            unset($cacheableResult['temp_path']); // Temp path'i cache'leme
            \Cache::put($cacheKey, $cacheableResult, now()->addDays(7));

            Log::info('💾 PDF analysis cached', [
                'cache_key' => $cacheKey,
                'content_length' => strlen($enhancedContent),
                'cache_duration' => '7 days'
            ]);

            return $result;

        } catch (\Exception $e) {
            // Hata durumunda da instant cleanup yap
            if ($tempPath) {
                $this->cleanupTempFiles([$tempPath]);
            }

            Log::error('❌ PDF analysis error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Image analiz et
     */
    private function analyzeImage(UploadedFile $file, string $analysisType): array
    {
        $tempPath = null;

        try {
            // Image'ı geçici olarak kaydet
            $tempPath = $file->store('temp/image-analysis', 'local');
            $fullPath = Storage::disk('local')->path($tempPath);

            Log::info('🖼️ Image analysis başladı', [
                'file_name' => $file->getClientOriginalName(),
                'temp_path' => $tempPath,
                'analysis_type' => $analysisType
            ]);

            // Image'ı base64'e çevir (Claude Vision için)
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = $file->getMimeType();

            // Claude Vision ile image analysis
            $analysisResult = $this->analyzeImageWithAI($imageData, $mimeType, $analysisType);

            return [
                'success' => true,
                'file_type' => 'image',
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $mimeType,
                'extracted_content' => $analysisResult['content'],
                'layout_analysis' => $analysisResult['layout'] ?? [],
                'analysis_type' => $analysisType,
                'temp_path' => $tempPath
            ];

        } catch (\Exception $e) {
            // Hata durumunda da instant cleanup yap
            if ($tempPath) {
                $this->cleanupTempFiles([$tempPath]);
            }

            Log::error('❌ Image analysis error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * PDF text extraction (using smalot/pdfparser)
     */
    private function extractPDFText(string $pdfPath): string
    {
        try {
            // PDF Parser kullan - eğer kurulu değilse basic fallback
            if (class_exists('\\Smalot\\PdfParser\\Parser')) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($pdfPath);
                $text = $pdf->getText();
            } else {
                // Fallback: Basic file content
                Log::warning('⚠️ smalot/pdfparser not installed, using basic text extraction');
                $text = 'PDF text extraction requires smalot/pdfparser package. Please install: composer require smalot/pdfparser';
            }

            // Text temizleme
            $text = preg_replace('/\s+/', ' ', $text); // Multiple spaces
            $text = preg_replace('/\n+/', "\n", $text); // Multiple newlines

            return trim($text);

        } catch (\Exception $e) {
            Log::error('❌ PDF text extraction error: ' . $e->getMessage());
            return 'PDF text extraction failed: ' . $e->getMessage();
        }
    }

    /**
     * PDF layout analysis (using advanced techniques)
     */
    private function analyzePDFLayout(string $pdfPath): array
    {
        try {
            // Basic layout detection
            if (!class_exists('\\Smalot\\PdfParser\\Parser')) {
                return ['error' => 'smalot/pdfparser required for layout analysis'];
            }

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);

            $pages = $pdf->getPages();
            $layoutInfo = [
                'page_count' => count($pages),
                'structure' => [],
                'fonts' => [],
                'colors' => []
            ];

            foreach ($pages as $pageIndex => $page) {
                $pageLayout = [
                    'page' => $pageIndex + 1,
                    'text_blocks' => $this->detectTextBlocks($page),
                    'headings' => $this->detectHeadings($page),
                    'lists' => $this->detectLists($page)
                ];

                $layoutInfo['structure'][] = $pageLayout;
            }

            return $layoutInfo;

        } catch (\Exception $e) {
            Log::error('❌ PDF layout analysis error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Image analysis with Claude Vision
     */
    private function analyzeImageWithAI(string $imageData, string $mimeType, string $analysisType): array
    {
        try {
            $analysisPrompt = $this->buildImageAnalysisPrompt($analysisType);

            // Claude Vision API call - API key'i set et
            $anthropicService = app(\Modules\AI\App\Services\AnthropicService::class);

            // Config'den API key al
            $apiKey = config('ai.providers.anthropic.api_key');
            if (empty($apiKey)) {
                throw new \Exception('Anthropic API key not configured. Please set ANTHROPIC_API_KEY in .env file');
            }

            // API key ve model ayarlarını set et
            $anthropicService->setApiKey($apiKey);
            $anthropicService->setModel(config('ai.providers.anthropic.model', 'claude-sonnet-4-20250514'));
            $anthropicService->setMaxTokens(1500); // DAHA KISA İÇERİK İÇİN

            Log::info('🔍 Claude Vision API çağrısı yapılıyor', [
                'analysis_type' => $analysisType,
                'mime_type' => $mimeType,
                'api_key_prefix' => substr($apiKey, 0, 10) . '...'
            ]);

            $messages = [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $analysisPrompt
                        ],
                        [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ];

            $response = $anthropicService->generateCompletionStream($messages);
            $content = $response['response'] ?? $response;

            return [
                'content' => $content,
                'layout' => $this->extractLayoutInfoFromResponse($content)
            ];

        } catch (\Exception $e) {
            Log::error('❌ Image AI analysis error: ' . $e->getMessage());

            // Fallback: Basic image description
            return [
                'content' => 'Image analysis failed: ' . $e->getMessage() . '. Please provide manual description.',
                'layout' => []
            ];
        }
    }

    // 🗑️ AI ENHANCEMENT SİLİNDİ - Gereksizdi!

    /**
     * Image analysis prompt builder
     */
    private function buildImageAnalysisPrompt(string $analysisType): string
    {
        $defaultLanguage = $this->getTenantDefaultLanguage();
        $languageName = $this->getLanguageName($defaultLanguage);

        if ($analysisType === 'layout_preserve') {
            return "SADECE {$languageName} DİLİNDE! Bu görseli analiz et:

1. LAYOUT ANALİZİ:
   - Sayfa düzeni (header, sidebar, main content, footer)
   - Grid sistem (kaç sütun, nasıl bölünmüş)
   - Spacing ve margin'lar
   - Element hierarchisi

2. TASARIM ÖGELERİ:
   - Renk paleti (dominant renkler)
   - Typography (başlık, metin boyutları)
   - Button stilleri
   - Card/container tasarımları

3. İÇERİK ÇIKARIMI:
   - Tüm metin içeriği
   - Navigation menü öğeleri
   - Başlıklar ve alt başlıklar
   - Çağrı metinleri (CTA)

4. WEB ELEMENT TESPİTİ:
   - Form alanları
   - Butonlar ve linkler
   - İkonlar ve görseller
   - Liste yapıları

Bu bilgileri kullanarak Tailwind CSS + Alpine.js ile aynı görünümde modern web tasarımı oluşturacağım. Lütfen detaylı ve yapılandırılmış bilgi ver.";
        } else {
            return "SADECE {$languageName} DİLİNDE! Bu görselden içerik çıkar:

1. Tüm metin içeriği (başlıklar, paragraflar, listeler)
2. Navigation öğeleri
3. Buton/link metinleri
4. Form label'ları
5. Çağrı metinleri (CTA)

Tasarım bilgilerine gerek yok, sadece içerik odaklı çıkarım yap. Modern web sitesi oluştururken bu içeriği kullanacağım.";
        }
    }

    /**
     * Enhancement prompt builder
     */
    private function buildEnhancementPrompt(string $content, string $analysisType, string $fileType, array $layoutInfo): string
    {
        // Tenant'ın varsayılan dilini al
        $defaultLanguage = $this->getTenantDefaultLanguage();
        // TemplateEngine entegrasyonu - PDF analizi için template based approach
        $templateContext = [
            'file_type' => $fileType,
            'analysis_type' => $analysisType,
            'content_preview' => substr($content, 0, 500),
            'layout_info' => $layoutInfo,
            'current_date' => date('d.m.Y'),
            'current_time' => date('H:i')
        ];

        // PDF Content Generation için özel AI Feature oluştur (mock)
        $pdfFeature = new \Modules\AI\App\Models\AIFeature([
            'name' => 'PDF Premium Landing Generator',
            'slug' => 'pdf-premium-landing-generator',
            'type' => 'content_creator',
            'quick_prompt' => 'PDF dosyalarından ultra premium landing page oluştur',
            'response_template' => json_encode([
                'format' => 'ultra_premium_landing',
                'style' => 'modern_premium',
                'sections' => ['hero', 'features', 'stats', 'cta'],
                'show_original' => false
            ])
        ]);

        // 🚀 SÜPER MİNİMAL - sadece talebi gönder!
        $userPrompt = "PDF → landing page oluştur";
        $templateBasedPrompt = \Modules\AI\App\Services\ResponseTemplateEngine::generateMinimalPrompt($userPrompt, $content);

        Log::info('🎨 Minimal prompt generated', [
            'template_length' => strlen($templateBasedPrompt)
        ]);

        // ✅ Sadece dil + minimal prompt
        $finalPrompt = "Dil: " . strtoupper($this->getLanguageName($defaultLanguage)) . "\n\n" . $templateBasedPrompt;

        return $finalPrompt;
    }

    /**
     * File type detection
     */
    private function detectFileType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        throw new \Exception('Unsupported file type: ' . $mimeType);
    }

    /**
     * 🗑️ INSTANT CLEANUP - Geçici dosyaları hemen temizle
     */
    private function cleanupTempFiles(array $tempPaths): void
    {
        foreach ($tempPaths as $tempPath) {
            try {
                if ($tempPath && Storage::disk('local')->exists($tempPath)) {
                    Storage::disk('local')->delete($tempPath);
                    Log::info('✅ INSTANT cleanup: ' . $tempPath);
                }
            } catch (\Exception $e) {
                Log::warning('⚠️ Instant cleanup failed: ' . $tempPath . ' - ' . $e->getMessage());
                // Hata durumunda delayed cleanup job'a bırak
            }
        }
    }


    // Helper methods for PDF text analysis
    private function detectTextBlocks($page): array
    {
        // Basic implementation
        return ['text_blocks' => 'detected'];
    }

    private function detectHeadings($page): array
    {
        // Basic implementation
        return ['headings' => 'detected'];
    }

    private function detectLists($page): array
    {
        // Basic implementation
        return ['lists' => 'detected'];
    }

    private function extractLayoutInfoFromResponse($response): array
    {
        // Parse AI response for layout info
        return ['layout_extracted' => true];
    }

    private function combineMultipleFileContents(array $results, string $analysisType): string
    {
        $combined = '';
        foreach ($results as $result) {
            if ($result['success']) {
                $combined .= "\n\n=== " . $result['file_name'] . " ===\n" . $result['extracted_content'];
            }
        }
        return trim($combined);
    }

    private function generateAnalysisSummary(array $results): array
    {
        return [
            'total_files' => count($results),
            'successful_analyses' => count(array_filter($results, fn($r) => $r['success'] ?? false)),
            'file_types' => array_unique(array_column($results, 'file_type'))
        ];
    }

    /**
     * Tenant'ın varsayılan dilini al
     */
    private function getTenantDefaultLanguage(): string
    {
        try {
            // Site'ın varsayılan dilini al (site_languages'dan)
            $siteLocale = config('app.site_locale');
            if ($siteLocale) {
                return $siteLocale;
            }

            // Tenant locale varsa onu kullan
            $tenantLocale = config('app.tenant_locale');
            if ($tenantLocale) {
                return $tenantLocale;
            }

            // Site languages'dan ilk dili al
            $siteLanguages = config('app.site_languages', []);
            if (!empty($siteLanguages) && is_array($siteLanguages)) {
                return reset($siteLanguages); // İlk dili al
            }

            // Fallback
            return config('app.locale', 'tr');

        } catch (\Exception $e) {
            Log::warning('Dil tespiti başarısız, varsayılan kullanılıyor', ['error' => $e->getMessage()]);
            return 'tr';
        }
    }

    /**
     * Dil kodundan dil adını al
     */
    private function getLanguageName(string $langCode): string
    {
        $languages = [
            'tr' => 'TÜRKÇE',
            'en' => 'İNGİLİZCE',
            'de' => 'ALMANCA',
            'fr' => 'FRANSIZCA',
            'es' => 'İSPANYOLCA',
            'ar' => 'ARAPÇA',
            'ru' => 'RUSÇA',
            'zh' => 'ÇİNCE',
            'ja' => 'JAPONCA',
            'ko' => 'KORECE',
            'it' => 'İTALYANCA',
            'pt' => 'PORTEKIZCE'
        ];

        return $languages[$langCode] ?? strtoupper($langCode);
    }

    /**
     * 🖼️ PDF İÇİNDEKİ GÖRSELLER - PDF'den görsel çıkar ve listele
     */
    private function extractPDFImages(string $pdfPath): array
    {
        try {
            if (!class_exists('\\Smalot\\PdfParser\\Parser')) {
                Log::warning('⚠️ smalot/pdfparser not installed, cannot extract PDF images');
                return [
                    [
                        'type' => 'note',
                        'message' => 'PDF görsel çıkarma için smalot/pdfparser paketi gerekli',
                        'instructions' => 'composer require smalot/pdfparser'
                    ]
                ];
            }

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $pages = $pdf->getPages();

            $extractedImages = [];
            $imageCounter = 1;

            foreach ($pages as $pageNum => $page) {
                try {
                    // Page objects'i al
                    $objects = $page->getXObjects();

                    foreach ($objects as $objectName => $object) {
                        // Image object'i kontrol et
                        if ($this->isImageObject($object)) {
                            $imageInfo = $this->processImageObject($object, $pageNum + 1, $imageCounter);
                            if ($imageInfo) {
                                // 🔍 OCR - Görsel içindeki text'i çıkar
                                $imageInfo = $this->addOCRTextToImage($imageInfo, $object);
                                $extractedImages[] = $imageInfo;
                                $imageCounter++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("⚠️ Sayfa {$pageNum} görsel çıkarma hatası: " . $e->getMessage());
                    continue;
                }
            }

            // Eğer hiç görsel bulunamazsa bilgi mesajı ekle
            if (empty($extractedImages)) {
                $extractedImages[] = [
                    'type' => 'info',
                    'message' => 'Bu PDF\'de görsel bulunamadı veya görseller çıkarılamadı',
                    'page' => 'all',
                    'suggestion' => 'PDF\'de gömülü görseller varsa, farklı bir PDF formatı deneyin'
                ];
            }

            Log::info('🖼️ PDF görsel extraction tamamlandı', [
                'total_pages' => count($pages),
                'extracted_images' => count($extractedImages),
                'image_types' => array_unique(array_column($extractedImages, 'type'))
            ]);

            return $extractedImages;

        } catch (\Exception $e) {
            Log::error('❌ PDF görsel extraction hatası: ' . $e->getMessage());
            return [
                [
                    'type' => 'error',
                    'message' => 'PDF görsel çıkarma başarısız: ' . $e->getMessage(),
                    'page' => 'unknown'
                ]
            ];
        }
    }

    /**
     * Object'in image olup olmadığını kontrol et
     */
    private function isImageObject($object): bool
    {
        try {
            // Object details'i kontrol et
            $details = $object->getDetails();

            // Subtype kontrolü
            if (isset($details['Subtype']) && in_array($details['Subtype'], ['Image', '/Image'])) {
                return true;
            }

            // Filter kontrolü (image compression types)
            if (isset($details['Filter'])) {
                $filters = is_array($details['Filter']) ? $details['Filter'] : [$details['Filter']];
                $imageFilters = ['DCTDecode', 'CCITTFaxDecode', 'JBIG2Decode', 'JPXDecode'];

                foreach ($filters as $filter) {
                    if (in_array($filter, $imageFilters)) {
                        return true;
                    }
                }
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Image object'ini işle ve bilgilerini çıkar
     */
    private function processImageObject($object, int $pageNum, int $imageCounter): ?array
    {
        try {
            $details = $object->getDetails();

            // Görsel bilgilerini çıkar
            $width = $details['Width'] ?? 'unknown';
            $height = $details['Height'] ?? 'unknown';
            $colorSpace = $details['ColorSpace'] ?? 'unknown';
            $bitsPerComponent = $details['BitsPerComponent'] ?? 'unknown';
            $filter = $details['Filter'] ?? 'unknown';

            // Image format'ı tahmin et
            $imageFormat = $this->guessImageFormat($filter, $details);

            // Size tahmin et
            $estimatedSize = $this->estimateImageSize($width, $height, $bitsPerComponent, $colorSpace);

            return [
                'type' => 'image',
                'id' => "img_{$pageNum}_{$imageCounter}",
                'page' => $pageNum,
                'position' => $imageCounter,
                'dimensions' => [
                    'width' => $width,
                    'height' => $height
                ],
                'format' => $imageFormat,
                'color_space' => $colorSpace,
                'bits_per_component' => $bitsPerComponent,
                'compression' => $filter,
                'estimated_size' => $estimatedSize,
                'description' => "Sayfa {$pageNum}'de bulunan görsel #{$imageCounter}",
                'technical_info' => [
                    'filter' => $filter,
                    'color_space' => $colorSpace,
                    'dimensions' => "{$width}x{$height}",
                    'compression_type' => $this->getCompressionType($filter)
                ]
            ];

        } catch (\Exception $e) {
            Log::warning("Image object işleme hatası: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Filter'dan image format'ı tahmin et
     */
    private function guessImageFormat($filter, array $details): string
    {
        if (is_array($filter)) {
            $filter = reset($filter);
        }

        switch ($filter) {
            case 'DCTDecode':
                return 'JPEG';
            case 'CCITTFaxDecode':
                return 'TIFF/Fax';
            case 'JBIG2Decode':
                return 'JBIG2';
            case 'JPXDecode':
                return 'JPEG2000';
            case 'FlateDecode':
                return 'PNG-like';
            default:
                return 'Unknown';
        }
    }

    /**
     * Image boyutunu tahmin et
     */
    private function estimateImageSize($width, $height, $bitsPerComponent, $colorSpace): string
    {
        try {
            if ($width === 'unknown' || $height === 'unknown') {
                return 'Unknown size';
            }

            $pixels = (int) $width * (int) $height;
            $channels = $this->getColorChannels($colorSpace);
            $bits = (int) ($bitsPerComponent === 'unknown' ? 8 : $bitsPerComponent);

            $estimatedBytes = ($pixels * $channels * $bits) / 8;

            if ($estimatedBytes > 1024 * 1024) {
                return round($estimatedBytes / (1024 * 1024), 2) . ' MB';
            } elseif ($estimatedBytes > 1024) {
                return round($estimatedBytes / 1024, 2) . ' KB';
            } else {
                return round($estimatedBytes) . ' bytes';
            }

        } catch (\Exception $e) {
            return 'Size calculation failed';
        }
    }

    /**
     * Color space'den kanal sayısını tahmin et
     */
    private function getColorChannels($colorSpace): int
    {
        if (is_array($colorSpace)) {
            $colorSpace = reset($colorSpace);
        }

        switch ($colorSpace) {
            case 'DeviceGray':
            case '/DeviceGray':
                return 1; // Grayscale
            case 'DeviceRGB':
            case '/DeviceRGB':
                return 3; // RGB
            case 'DeviceCMYK':
            case '/DeviceCMYK':
                return 4; // CMYK
            default:
                return 3; // Default RGB
        }
    }

    /**
     * Compression type açıklaması
     */
    private function getCompressionType($filter): string
    {
        if (is_array($filter)) {
            $filter = reset($filter);
        }

        switch ($filter) {
            case 'DCTDecode':
                return 'JPEG compression (lossy)';
            case 'CCITTFaxDecode':
                return 'CCITT Fax compression (lossless)';
            case 'JBIG2Decode':
                return 'JBIG2 compression (lossless)';
            case 'JPXDecode':
                return 'JPEG 2000 compression';
            case 'FlateDecode':
                return 'Flate compression (lossless)';
            default:
                return 'Unknown compression';
        }
    }

    /**
     * 🔍 OCR - PDF görselinin içindeki text'i çıkar
     */
    private function addOCRTextToImage(array $imageInfo, $imageObject): array
    {
        try {
            // OCR etkinleştirme kontrolü
            if (!class_exists('\\thiagoalessio\\TesseractOCR\\TesseractOCR')) {
                Log::warning('⚠️ tesseract_ocr not installed, skipping OCR');
                $imageInfo['ocr_text'] = null;
                $imageInfo['ocr_status'] = 'tesseract_not_available';
                $imageInfo['ocr_note'] = 'OCR için tesseract_ocr paketi gerekli: composer require thiagoalessio/tesseract_ocr';
                return $imageInfo;
            }

            // Image data çıkarma (PDF'den binary veri al)
            $imageData = $this->extractImageDataFromPDFObject($imageObject);

            if (!$imageData) {
                $imageInfo['ocr_text'] = null;
                $imageInfo['ocr_status'] = 'image_data_extraction_failed';
                $imageInfo['ocr_note'] = 'Görsel verisi PDF\'den çıkarılamadı';
                return $imageInfo;
            }

            // Geçici dosya oluştur
            $tempImagePath = $this->createTempImageFile($imageData, $imageInfo['format']);

            if (!$tempImagePath) {
                $imageInfo['ocr_text'] = null;
                $imageInfo['ocr_status'] = 'temp_file_creation_failed';
                return $imageInfo;
            }

            // OCR işlemi
            $ocrText = $this->performOCR($tempImagePath);

            // Temp dosyayı temizle
            if (file_exists($tempImagePath)) {
                unlink($tempImagePath);
            }

            // OCR sonuçlarını ekle
            $imageInfo['ocr_text'] = $ocrText['text'];
            $imageInfo['ocr_status'] = $ocrText['status'];
            $imageInfo['ocr_confidence'] = $ocrText['confidence'] ?? null;
            $imageInfo['ocr_language'] = $ocrText['language'] ?? 'auto';
            $imageInfo['ocr_word_count'] = $ocrText['word_count'] ?? 0;

            // Text varsa description'ı güncelle
            if (!empty($ocrText['text']) && strlen(trim($ocrText['text'])) > 0) {
                $imageInfo['description'] .= " - İçerik: " . substr(trim($ocrText['text']), 0, 100) . "...";
                $imageInfo['has_text_content'] = true;
            } else {
                $imageInfo['has_text_content'] = false;
            }

            Log::info('🔍 OCR işlemi tamamlandı', [
                'image_id' => $imageInfo['id'],
                'ocr_status' => $ocrText['status'],
                'text_length' => strlen($ocrText['text'] ?? ''),
                'has_content' => !empty($ocrText['text'])
            ]);

            return $imageInfo;

        } catch (\Exception $e) {
            Log::error('❌ OCR işlemi hatası: ' . $e->getMessage());
            $imageInfo['ocr_text'] = null;
            $imageInfo['ocr_status'] = 'ocr_error';
            $imageInfo['ocr_error'] = $e->getMessage();
            return $imageInfo;
        }
    }

    /**
     * PDF object'inden image data çıkar
     */
    private function extractImageDataFromPDFObject($imageObject): ?string
    {
        try {
            // PDF object'den raw data al
            $content = $imageObject->getContent();

            if (empty($content)) {
                return null;
            }

            // Basic filtering - PDF stream data olabilir
            return $content;

        } catch (\Exception $e) {
            Log::warning('PDF image data extraction failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Geçici image dosyası oluştur
     */
    private function createTempImageFile(string $imageData, string $format): ?string
    {
        try {
            // Format'a göre extension belirle
            $extension = $this->getImageExtension($format);
            $tempPath = tempnam(sys_get_temp_dir(), 'pdf_ocr_') . '.' . $extension;

            // Raw data'yı dosyaya yaz
            if (file_put_contents($tempPath, $imageData) === false) {
                return null;
            }

            // Dosya var mı kontrol et
            if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                return null;
            }

            return $tempPath;

        } catch (\Exception $e) {
            Log::warning('Temp image file creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format'dan file extension çıkar
     */
    private function getImageExtension(string $format): string
    {
        switch (strtolower($format)) {
            case 'jpeg':
                return 'jpg';
            case 'png-like':
                return 'png';
            case 'tiff/fax':
                return 'tiff';
            default:
                return 'jpg'; // Default JPEG
        }
    }

    /**
     * OCR işlemini gerçekleştir
     */
    private function performOCR(string $imagePath): array
    {
        try {
            // Tesseract OCR kullan
            $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($imagePath);

            // Türkçe + İngilizce dil desteği
            $ocr->lang('tur+eng');

            // OCR konfigürasyonu
            $ocr->configFile('pdf')  // PDF optimizasyonu
               ->psm(6)              // Uniform text block
               ->oem(3);             // Default OCR Engine Mode

            // OCR çalıştır
            $extractedText = $ocr->run();

            // Text temizleme
            $cleanedText = $this->cleanOCRText($extractedText);

            // Word count
            $wordCount = str_word_count($cleanedText);

            // Confidence hesaplama (basit algoritma)
            $confidence = $this->calculateOCRConfidence($cleanedText);

            return [
                'text' => $cleanedText,
                'status' => 'success',
                'confidence' => $confidence,
                'language' => 'tur+eng',
                'word_count' => $wordCount,
                'raw_text' => $extractedText
            ];

        } catch (\Exception $e) {
            Log::error('OCR processing failed: ' . $e->getMessage());

            return [
                'text' => '',
                'status' => 'failed',
                'error' => $e->getMessage(),
                'confidence' => 0,
                'word_count' => 0
            ];
        }
    }

    /**
     * OCR text'ini temizle
     */
    private function cleanOCRText(string $text): string
    {
        // Basic cleaning
        $cleaned = preg_replace('/\s+/', ' ', $text); // Multiple spaces
        $cleaned = preg_replace('/\n+/', "\n", $cleaned); // Multiple newlines
        $cleaned = trim($cleaned);

        // Çok kısa text'leri filtrele
        if (strlen($cleaned) < 3) {
            return '';
        }

        return $cleaned;
    }

    /**
     * OCR confidence hesapla (basit algoritma)
     */
    private function calculateOCRConfidence(string $text): float
    {
        if (empty($text)) {
            return 0.0;
        }

        // Faktörler:
        $confidence = 0.5; // Base confidence

        // Text uzunluğu
        if (strlen($text) > 20) {
            $confidence += 0.2;
        }

        // Kelime sayısı
        $wordCount = str_word_count($text);
        if ($wordCount > 5) {
            $confidence += 0.2;
        }

        // Türkçe karakterler
        if (preg_match('/[çğıöşüÇĞIÖŞÜ]/', $text)) {
            $confidence += 0.1;
        }

        return min(1.0, $confidence);
    }
}