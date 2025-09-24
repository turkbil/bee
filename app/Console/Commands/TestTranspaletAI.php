<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\AI\App\Services\Content\AIContentGeneratorService;

class TestTranspaletAI extends Command
{
    protected $signature = 'transpalet:ai-test';
    protected $description = 'Transpalet PDF AI content generation test';

    public function handle()
    {
        $this->info('🚀 Transpalet PDF AI Test Başlatılıyor...');

        try {
            // PDF dosyası
            $pdfPath = '/Users/nurullah/Desktop/cms/transpalet/F4-EN-Brochure-4.pdf';

            if (!file_exists($pdfPath)) {
                throw new \Exception("PDF dosyası bulunamadı: {$pdfPath}");
            }

            $this->info('📄 PDF dosyası bulundu: ' . basename($pdfPath));
            $this->info('📦 Dosya boyutu: ' . number_format(filesize($pdfPath) / 1024 / 1024, 2) . ' MB');

            // PDF içeriğini GERÇEK EXTRACT ile oku
            $fileAnalysisService = app(\Modules\AI\app\Services\Content\FileAnalysisService::class);

            // Dosyayı UploadedFile nesnesine çevir
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $pdfPath,
                basename($pdfPath),
                'application/pdf',
                null,
                true
            );

            $analysisResult = $fileAnalysisService->analyzeFiles([$uploadedFile], 'content_extract');

            if ($analysisResult['success'] && !empty($analysisResult['individual_results'])) {
                $firstResult = $analysisResult['individual_results'][0];
                $pdfContent = $firstResult['extracted_content'];
                $this->info('✅ GERÇEK PDF içerik extract edildi');
                $this->info('📄 Raw text uzunluğu: ' . strlen($firstResult['raw_text'] ?? '') . ' karakter');
                $this->info('🔍 Enhanced content uzunluğu: ' . strlen($pdfContent) . ' karakter');
                $this->info('📝 İlk 300 karakter: ' . substr($pdfContent, 0, 300) . '...');
            } else {
                $this->error('❌ PDF extract başarısız: ' . ($analysisResult['error'] ?? 'Bilinmeyen hata'));
                $pdfContent = "PDF extract failed - using fallback content";
            }

            $this->info('📝 PDF içerik alındı (' . strlen($pdfContent) . ' karakter)');

            // AI Content Generator servisini başlat
            $aiService = new AIContentGeneratorService();

            // Test parametreleri - Evrensel ürün tanıtım sayfası
            $userInput = "Herhangi bir ürün için modern tanıtım sayfası. SADECE BODY, header/footer yok. Zengin içerik, çok kartlı yapı, detaylı tablo.";
            $contentType = 'landing_page';
            $moduleContext = ['module' => 'Page', 'type' => 'product_page'];
            $tenantId = 1;

            $this->info('🎯 AI içerik üretimi başlatılıyor...');
            $this->info("User Input: {$userInput}");
            $this->info("Content Type: {$contentType}");
            $this->info("Module: " . print_r($moduleContext, true));
            $this->newLine();

            // AI ile içerik üret - GERÇEK file_analysis ile
            $params = [
                'tenant_id' => $tenantId,
                'prompt' => $userInput,
                'content_type' => $contentType,
                'module_context' => $moduleContext,
                'length' => 'unlimited', // ZORUNLU: Manuel length override
                'file_analysis' => $analysisResult['success'] ? $analysisResult : null, // GERÇEK FILE ANALYSIS
                'conversion_type' => 'content_extract',
                'custom_instructions' => "ULTRA DETAYLI İÇERİK! 3000+ kelime, 15 feature card, 20 tablo satırı ZORUNLU!"
            ];

            $result = $aiService->generateContent($params);

            if ($result['success']) {
                $this->info('✅ AI içerik üretimi BAŞARILI!');
                $this->info('📊 Token kullanımı: ' . ($result['total_tokens'] ?? 'N/A'));
                $this->info('⏱️ Süre: ' . ($result['generation_time_ms'] ?? 'N/A') . ' ms');
                $this->info('🤖 Provider: ' . ($result['provider'] ?? 'N/A'));
                $this->info('📝 İçerik uzunluğu: ' . strlen($result['content']) . ' karakter');
                $this->newLine();

                // Sonucu dosyaya kaydet
                $outputPath = '/Users/nurullah/Desktop/cms/laravel/debug-output-html.txt';
                file_put_contents($outputPath, $result['content']);
                $this->info("💾 HTML içeriği kaydedildi: {$outputPath}");

                // İçerik analizi
                $this->newLine();
                $this->info('🔍 İÇERİK ANALİZİ:');
                $this->info('=================================');

                $content = $result['content'];

                // Başlık boyutları kontrolü
                if (preg_match_all('/text-(6xl|7xl|8xl)/', $content, $matches)) {
                    $this->error('❌ BÜYÜK BAŞLIK SORUNU: ' . count($matches[0]) . ' adet oversized text bulundu');
                    foreach (array_unique($matches[1]) as $size) {
                        $this->warn("   - text-{$size} kullanılmış");
                    }
                } else {
                    $this->info('✅ Başlık boyutları normal (max text-5xl)');
                }

                // min-h-screen kontrolü
                if (strpos($content, 'min-h-screen') !== false) {
                    $this->error('❌ min-h-screen kullanılmış (yasak!)');
                } else {
                    $this->info('✅ min-h-screen kullanılmamış');
                }

                // Feature card sayısı
                $featureCount = preg_match_all('/<div[^>]*class="[^"]*bg-white[^"]*dark:bg-gray-[0-9]+[^"]*"[^>]*>/', $content);
                $this->info("📦 Feature Card Sayısı: {$featureCount} (minimum 6 gerekli)");

                // Tablo satır sayısı
                $tableRowCount = preg_match_all('/<tr[^>]*>/', $content) - 1; // Header hariç
                $this->info("📊 Tablo Satır Sayısı: {$tableRowCount} (minimum 8 gerekli)");

                // İkon kullanımı
                $iconCount = preg_match_all('/class="[^"]*fa[s|r|l]?[^"]*"/', $content);
                $this->info("🎯 İkon Sayısı: {$iconCount}");

                // İçerik zenginliği
                $wordCount = str_word_count(strip_tags($content));
                $this->info("📝 Kelime Sayısı: {$wordCount}");

                $this->newLine();
                $this->info('=================================');

                if ($featureCount >= 6 && $tableRowCount >= 8 && $iconCount > 10) {
                    $this->info('🎉 İÇERİK KALİTESİ: MÜKEMMEL!');
                } elseif ($featureCount >= 4 && $tableRowCount >= 5) {
                    $this->warn('⚠️ İÇERİK KALİTESİ: ORTA (iyileştirme gerekli)');
                } else {
                    $this->error('❌ İÇERİK KALİTESİ: DÜŞÜK (yetersiz içerik)');
                }

            } else {
                $this->error('❌ AI içerik üretimi BAŞARISIZ!');
                $this->error('Hata: ' . ($result['error'] ?? 'Bilinmeyen hata'));

                if (isset($result['details'])) {
                    $this->info('Detaylar: ' . print_r($result['details'], true));
                }
            }

        } catch (\Exception $e) {
            $this->error('💥 HATA: ' . $e->getMessage());
            $this->error('Dosya: ' . $e->getFile() . ':' . $e->getLine());
        }

        $this->info('🏁 Test tamamlandı.');
    }
}