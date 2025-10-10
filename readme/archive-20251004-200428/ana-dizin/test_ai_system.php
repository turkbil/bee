<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

// Laravel uygulamasını başlat
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test verilerini hazırla
echo "🔥 AI İÇERİK ÜRETİM SİSTEMİ TEST BAŞLADI\n\n";

// 1. SmartResponseFormatter test
echo "1️⃣ SmartResponseFormatter Testi\n";
echo "================================\n";

try {
    $formatter = new \Modules\AI\App\Services\SmartResponseFormatter();

    // Test feature'ı oluştur
    $testFeature = new \Modules\AI\App\Models\AIFeature();
    $testFeature->slug = 'pdf-content-generation';
    $testFeature->name = 'PDF İçerik Üretimi';
    $testFeature->title = 'Premium Landing Üretimi';

    // Test input/output
    $testInput = "Endüstriyel forklift ekipmanları için premium landing sayfası oluştur";
    $testOutput = "1. Modern Forklift Teknolojisi\nSon teknoloji elektrikli forkliftler ile verimliliği artırın.\n\n2. Güvenlik Önceliği\nErgonomik tasarım ve gelişmiş güvenlik özellikleri.\n\n3. Geniş Ürün Yelpazesi\n1-10 ton kapasiteli çeşitli forklift modelleri.";

    // Smart formatter'ı test et
    $formattedResult = $formatter->format($testInput, $testOutput, $testFeature);

    echo "✅ BAŞARILI: SmartResponseFormatter çalıştı\n";
    echo "📊 Girdi uzunluğu: " . strlen($testInput) . " karakter\n";
    echo "📊 Çıktı uzunluğu: " . strlen($testOutput) . " karakter\n";
    echo "📊 Formatted uzunluk: " . strlen($formattedResult) . " karakter\n";
    echo "🎨 Premium landing format uygulandı: " . (strpos($formattedResult, 'premium-landing-wrapper') !== false ? 'EVET' : 'HAYIR') . "\n";
    echo "🔍 Sektör tespiti: " . (strpos($formattedResult, 'industrial') !== false ? 'ENDÜSTRİYEL' : 'GENEL') . "\n";

} catch (\Exception $e) {
    echo "❌ HATA: SmartResponseFormatter test başarısız - " . $e->getMessage() . "\n";
}

echo "\n";

// 2. AIResponseFormatters test
echo "2️⃣ AIResponseFormatters Testi\n";
echo "==============================\n";

try {
    $responseFormatter = new \Modules\AI\App\Services\Response\AIResponseFormatters();

    // PDF content test
    $pdfResponse = $responseFormatter->formatContentGenerationResponse(
        $testOutput,
        $testFeature,
        'PDF İçerik Analizi'
    );

    echo "✅ BAŞARILI: AIResponseFormatters çalıştı\n";
    echo "📊 Response tipi: " . ($pdfResponse['type'] ?? 'N/A') . "\n";
    echo "🚀 Premium mod: " . ($pdfResponse['premium'] ?? false ? 'AKTIF' : 'PASIF') . "\n";
    echo "🎯 Enhanced: " . ($pdfResponse['enhanced'] ?? false ? 'EVET' : 'HAYIR') . "\n";
    echo "📋 PDF metadata: " . (isset($pdfResponse['pdf_meta']) ? 'MEVCUT' : 'YOK') . "\n";

    if (isset($pdfResponse['pdf_meta'])) {
        echo "   └─ Sektör: " . ($pdfResponse['pdf_meta']['sector'] ?? 'N/A') . "\n";
        echo "   └─ İçerik Tipi: " . ($pdfResponse['pdf_meta']['content_type'] ?? 'N/A') . "\n";
    }

} catch (\Exception $e) {
    echo "❌ HATA: AIResponseFormatters test başarısız - " . $e->getMessage() . "\n";
}

echo "\n";

// 3. TemplateEngine test
echo "3️⃣ TemplateEngine Testi\n";
echo "========================\n";

try {
    $templateEngine = new \Modules\AI\App\Services\Template\TemplateEngine();

    // Test feature'ı veritabanından al
    $feature = \Modules\AI\App\Models\AIFeature::where('slug', 'pdf-content-generation')->first();

    if (!$feature) {
        // Test feature'ı oluştur
        $feature = new \Modules\AI\App\Models\AIFeature();
        $feature->slug = 'pdf-content-generation';
        $feature->name = 'PDF İçerik Üretimi';
        $feature->title = 'Premium Landing Üretimi';
        $feature->type = 'content_creator';
        $feature->quick_prompt = 'PDF dosyasındaki bilgileri analiz ederek premium landing sayfası oluştur.';
        $feature->response_template = json_encode([
            'format' => 'premium_landing',
            'sections' => ['hero', 'features', 'stats'],
            'premium' => true
        ]);
        $feature->is_active = true;
        $feature->save();
    }

    // Template build et
    $builtTemplate = $templateEngine->buildTemplate($feature, [
        'tenant_name' => 'Test Tenant',
        'sector' => 'industrial',
        'user_name' => 'Test User'
    ]);

    echo "✅ BAŞARILI: TemplateEngine çalıştı\n";
    echo "📊 Template uzunluğu: " . strlen($builtTemplate) . " karakter\n";
    echo "🎯 Base template: " . (strpos($builtTemplate, 'CONTENT CREATOR MODE') !== false ? 'MEVCUT' : 'YOK') . "\n";
    echo "📋 Response instructions: " . (strpos($builtTemplate, 'RESPONSE FORMAT') !== false ? 'MEVCUT' : 'YOK') . "\n";
    echo "🔧 Context variables: " . (strpos($builtTemplate, 'Test Tenant') !== false ? 'İŞLENDİ' : 'İŞLENMEDİ') . "\n";

} catch (\Exception $e) {
    echo "❌ HATA: TemplateEngine test başarısız - " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Veri kalitesi ve sahte veri kontrolü
echo "4️⃣ Veri Kalitesi Kontrolü\n";
echo "==========================\n";

try {
    // SmartResponseFormatter'da sahte veri kontrolü
    $sampleResponse = "Bu şirket 15+ yıl deneyimine sahiptir ve 1000+ başarılı proje tamamlamıştır.";
    $feature = new \Modules\AI\App\Models\AIFeature();
    $feature->slug = 'premium-landing-builder';

    $formatter = new \Modules\AI\App\Services\SmartResponseFormatter();
    $result = $formatter->format('Test input', $sampleResponse, $feature);

    // Sahte veri tespiti
    $fakeDataPatterns = [
        '/\d+\+?\s*(yıl|year)\s*(deneyim|experience)/i',
        '/\d+\+?\s*(proje|project)/i',
        '/\d+\+?\s*(müşteri|customer)/i',
        '/quality.*score.*\d+/i',
        '/\d+%.*success/i'
    ];

    $fakeDataFound = false;
    foreach ($fakeDataPatterns as $pattern) {
        if (preg_match($pattern, $result)) {
            $fakeDataFound = true;
            break;
        }
    }

    echo "🔍 Sahte veri kontrolü: " . ($fakeDataFound ? '⚠️ BULUNDU' : '✅ TEMİZ') . "\n";
    echo "📊 Gerçek veriden üretim: " . (!$fakeDataFound ? '✅ BAŞARILI' : '❌ BAŞARISIZ') . "\n";
    echo "🎯 Strictness seviyesi: " . (\Modules\AI\App\Services\SmartResponseFormatter::STRICTNESS_LEVELS['premium-landing-builder'] ?? 'Bilinmiyor') . "\n";

} catch (\Exception $e) {
    echo "❌ HATA: Veri kalitesi kontrolü başarısız - " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Sektör tespiti ve renk paleti
echo "5️⃣ Sektör Tespiti & Renk Paleti\n";
echo "=================================\n";

try {
    $testOutputs = [
        'endüstriyel' => "Forklift ve transpalet ekipmanları için endüstriyel çözümler",
        'teknoloji' => "Yazılım geliştirme ve AI teknolojileri hizmeti",
        'sağlık' => "Hastane yönetimi ve doktor randevu sistemi",
        'finans' => "Bankacılık ve yatırım danışmanlığı hizmetleri"
    ];

    foreach ($testOutputs as $expectedSector => $content) {
        $feature = new \Modules\AI\App\Models\AIFeature();
        $feature->slug = 'pdf-content-generation';

        $formatter = new \Modules\AI\App\Services\SmartResponseFormatter();
        $result = $formatter->format('Test', $content, $feature);

        // Sektör tespiti
        $detectedSector = 'general';
        if (strpos($result, 'data-sector') !== false) {
            preg_match("/data-sector='([^']*)/", $result, $matches);
            $detectedSector = $matches[1] ?? 'general';
        }

        echo "🎯 Test: {$expectedSector} -> Tespit: {$detectedSector} " .
             ($expectedSector === $detectedSector ? '✅' : '⚠️') . "\n";
    }

} catch (\Exception $e) {
    echo "❌ HATA: Sektör tespiti test başarısız - " . $e->getMessage() . "\n";
}

echo "\n";

echo "🎉 AI İÇERİK ÜRETİM SİSTEMİ TESTİ TAMAMLANDI!\n";
echo "==============================================\n";

// Log temizle
echo "🧹 Log dosyası temizleniyor...\n";
file_put_contents('storage/logs/laravel.log', '');
echo "✅ Log dosyası temizlendi.\n";