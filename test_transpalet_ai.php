<?php

require_once 'bootstrap/app.php';

$app = app();

use Modules\AI\App\Services\Content\AIContentGeneratorService;
use Modules\AI\App\Services\Content\FileAnalysisService;

echo "🚀 Transpalet PDF AI Test Başlatılıyor...\n";

try {
    // PDF dosyası
    $pdfPath = '/Users/nurullah/Desktop/cms/transpalet/F4-EN-Brochure-4.pdf';

    if (!file_exists($pdfPath)) {
        throw new Exception("PDF dosyası bulunamadı: {$pdfPath}");
    }

    echo "📄 PDF dosyası bulundu: " . basename($pdfPath) . "\n";
    echo "📦 Dosya boyutu: " . number_format(filesize($pdfPath) / 1024 / 1024, 2) . " MB\n";

    // PDF içeriğini string olarak oku (basit test için)
    $pdfContent = "F4 Electric Pallet Truck - Efficient and effortless tool for checking incoming goods with 1500kg capacity. Platform-based design with lithium-ion battery technology. Compact design suitable for narrow aisles. Technical specifications include 24V Li-ion battery, 1500kg capacity, easy operation controls.";

    echo "📝 PDF içerik alındı (" . strlen($pdfContent) . " karakter)\n";

    // AI Content Generator servisini başlat
    $aiService = new AIContentGeneratorService();

    // Test parametreleri
    $userInput = "Transpalet ürün tanıtım sayfası üret. Dolu dolu içerik, normal boyutlu başlıklar, çok sayıda özellik kartı ve detaylı tablo istiyorum.";
    $contentType = 'landing_page';
    $moduleContext = 'Page';
    $tenantId = 1;

    echo "🎯 AI içerik üretimi başlatılıyor...\n";
    echo "User Input: {$userInput}\n";
    echo "Content Type: {$contentType}\n";
    echo "Module: {$moduleContext}\n\n";

    // AI ile içerik üret
    $result = $aiService->generateContent(
        $userInput,
        $contentType,
        $moduleContext,
        $pdfContent, // PDF içeriği direkt string olarak
        $tenantId
    );

    if ($result['success']) {
        echo "✅ AI içerik üretimi BAŞARILI!\n";
        echo "📊 Token kullanımı: " . ($result['total_tokens'] ?? 'N/A') . "\n";
        echo "⏱️ Süre: " . ($result['generation_time_ms'] ?? 'N/A') . " ms\n";
        echo "🤖 Provider: " . ($result['provider'] ?? 'N/A') . "\n";
        echo "📝 İçerik uzunluğu: " . strlen($result['content']) . " karakter\n\n";

        // Sonucu dosyaya kaydet
        $outputPath = '/Users/nurullah/Desktop/cms/laravel/debug-output-html.txt';
        file_put_contents($outputPath, $result['content']);
        echo "💾 HTML içeriği kaydedildi: {$outputPath}\n";

        // İçerik analizi
        echo "\n🔍 İÇERİK ANALİZİ:\n";
        echo "=================================\n";

        $content = $result['content'];

        // Başlık boyutları kontrolü
        if (preg_match_all('/text-(6xl|7xl|8xl)/', $content, $matches)) {
            echo "❌ BÜYÜK BAŞLIK SORUNU: " . count($matches[0]) . " adet oversized text bulundu\n";
            foreach (array_unique($matches[1]) as $size) {
                echo "   - text-{$size} kullanılmış\n";
            }
        } else {
            echo "✅ Başlık boyutları normal (max text-5xl)\n";
        }

        // min-h-screen kontrolü
        if (strpos($content, 'min-h-screen') !== false) {
            echo "❌ min-h-screen kullanılmış (yasak!)\n";
        } else {
            echo "✅ min-h-screen kullanılmamış\n";
        }

        // Feature card sayısı
        $featureCount = preg_match_all('/<div[^>]*class="[^"]*bg-white[^"]*dark:bg-gray-[0-9]+[^"]*"[^>]*>/', $content);
        echo "📦 Feature Card Sayısı: {$featureCount} (minimum 6 gerekli)\n";

        // Tablo satır sayısı
        $tableRowCount = preg_match_all('/<tr[^>]*>/', $content) - 1; // Header hariç
        echo "📊 Tablo Satır Sayısı: {$tableRowCount} (minimum 8 gerekli)\n";

        // İkon kullanımı
        $iconCount = preg_match_all('/class="[^"]*fa[s|r|l]?[^"]*"/', $content);
        echo "🎯 İkon Sayısı: {$iconCount}\n";

        // İçerik zenginliği
        $wordCount = str_word_count(strip_tags($content));
        echo "📝 Kelime Sayısı: {$wordCount}\n";

        echo "\n=================================\n";

        if ($featureCount >= 6 && $tableRowCount >= 8 && $iconCount > 10) {
            echo "🎉 İÇERİK KALİTESİ: MÜKEMMEL!\n";
        } elseif ($featureCount >= 4 && $tableRowCount >= 5) {
            echo "⚠️ İÇERİK KALİTESİ: ORTA (iyileştirme gerekli)\n";
        } else {
            echo "❌ İÇERİK KALİTESİ: DÜŞÜK (yetersiz içerik)\n";
        }

    } else {
        echo "❌ AI içerik üretimi BAŞARISIZ!\n";
        echo "Hata: " . ($result['error'] ?? 'Bilinmeyen hata') . "\n";

        if (isset($result['details'])) {
            echo "Detaylar: " . print_r($result['details'], true) . "\n";
        }
    }

} catch (Exception $e) {
    echo "💥 HATA: " . $e->getMessage() . "\n";
    echo "Dosya: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🏁 Test tamamlandı.\n";