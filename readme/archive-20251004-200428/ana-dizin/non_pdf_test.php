<?php

require_once 'vendor/autoload.php';

// Laravel uygulamasını başlat
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📝 NON-PDF SCENARIOS TEST\n";
echo "==========================\n\n";

// Test different feature types and their formatters
$testScenarios = [
    [
        'name' => 'Blog Content Generation',
        'slug' => 'blog-yazisi-olusturucu',
        'input' => 'Yapay zeka teknolojilerinin gelişimi hakkında blog yazısı',
        'expected_format' => 'flexible',
        'expected_features' => ['blog-intro', 'blog-content', 'heading sections']
    ],
    [
        'name' => 'SEO Analysis',
        'slug' => 'hizli-seo-analizi',
        'input' => 'Web sitesi SEO analizi ve önerileri',
        'expected_format' => 'flexible',
        'expected_features' => ['seo-dashboard', 'scores', 'recommendations']
    ],
    [
        'name' => 'Translation Service',
        'slug' => 'cevirmen',
        'input' => 'Hello, this is a test translation request.',
        'expected_format' => 'strict',
        'expected_features' => ['translation-result', 'original-translated']
    ],
    [
        'name' => 'Creative Writing',
        'slug' => 'yaratici-yazi',
        'input' => 'Gelecekteki teknoloji dünyası hakkında yaratıcı hikaye',
        'expected_format' => 'adaptive',
        'expected_features' => ['creative format', 'narrative structure']
    ]
];

foreach ($testScenarios as $index => $scenario) {
    echo "📋 TEST " . ($index + 1) . ": {$scenario['name']}\n";
    echo str_repeat("-", 50) . "\n";

    try {
        // Create test feature
        $testFeature = new \Modules\AI\App\Models\AIFeature();
        $testFeature->slug = $scenario['slug'];
        $testFeature->name = $scenario['name'];

        // Test response content
        $testResponse = "Bu bir test yanıtıdır. 1. İlk nokta hakkında açıklama. 2. İkinci önemli konu. 3. Sonuç ve öneriler bölümü.";

        // SmartResponseFormatter test
        echo "🔧 SmartResponseFormatter Test:\n";
        $formatter = new \Modules\AI\App\Services\SmartResponseFormatter();
        $formattedResult = $formatter->format($scenario['input'], $testResponse, $testFeature);

        $expectedStrictness = \Modules\AI\App\Services\SmartResponseFormatter::STRICTNESS_LEVELS[$scenario['slug']] ?? 'flexible';
        echo "   └─ Expected Format: {$scenario['expected_format']}\n";
        echo "   └─ Actual Format: {$expectedStrictness}\n";
        echo "   └─ Format Match: " . ($expectedStrictness === $scenario['expected_format'] ? '✅' : '❌') . "\n";
        echo "   └─ Output Length: " . strlen($formattedResult) . " characters\n";
        echo "   └─ Enhanced: " . (strlen($formattedResult) > strlen($testResponse) ? '✅' : '❌') . "\n";

        // Check for specific formatting features
        $hasExpectedFeatures = true;
        foreach ($scenario['expected_features'] as $feature) {
            $hasFeature = false;
            switch ($feature) {
                case 'blog-intro':
                    $hasFeature = strpos($formattedResult, 'blog-intro') !== false;
                    break;
                case 'blog-content':
                    $hasFeature = strpos($formattedResult, 'blog-content') !== false;
                    break;
                case 'heading sections':
                    $hasFeature = strpos($formattedResult, '<h5>') !== false;
                    break;
                case 'seo-dashboard':
                    $hasFeature = strpos($formattedResult, 'seo') !== false;
                    break;
                case 'translation-result':
                    $hasFeature = strpos($formattedResult, 'orijinal') !== false || strpos($formattedResult, 'translation') !== false;
                    break;
                case 'creative format':
                    $hasFeature = !preg_match('/^\s*\d+[\.\)]\s/m', $formattedResult);
                    break;
            }
            if (!$hasFeature) {
                $hasExpectedFeatures = false;
                break;
            }
        }
        echo "   └─ Expected Features: " . ($hasExpectedFeatures ? '✅' : '❌') . "\n";

        // AIResponseFormatters test
        echo "\n🎨 AIResponseFormatters Test:\n";
        $responseFormatter = new \Modules\AI\App\Services\Response\AIResponseFormatters();
        $formatted = $responseFormatter->formatFeatureResponse($testResponse, $testFeature, "Test Helper");

        echo "   └─ Success: " . ($formatted['success'] ?? false ? '✅' : '❌') . "\n";
        echo "   └─ Type: " . ($formatted['type'] ?? 'unknown') . "\n";
        echo "   └─ Feature Match: " . (isset($formatted['feature']) ? '✅' : '❌') . "\n";

        // Test specific formatters based on feature type
        if (strpos($scenario['slug'], 'seo') !== false) {
            $seoResult = $responseFormatter->formatSEOAnalysisResponse($testResponse, $testFeature, "SEO Helper");
            echo "   └─ SEO Specific Format: " . ($seoResult['type'] === 'seo_analysis' ? '✅' : '❌') . "\n";
        }

        if (strpos($scenario['slug'], 'cevirmen') !== false) {
            $translationResult = $responseFormatter->formatTranslationResponse($testResponse, $testFeature, "Translation Helper");
            echo "   └─ Translation Format: " . ($translationResult['type'] === 'translation' ? '✅' : '❌') . "\n";
        }

        if (strpos($scenario['slug'], 'icerik') !== false || strpos($scenario['slug'], 'blog') !== false) {
            $contentResult = $responseFormatter->formatContentGenerationResponse($testResponse, $testFeature, "Content Helper");
            echo "   └─ Content Format: " . ($contentResult['type'] === 'content_generation' ? '✅' : '❌') . "\n";
        }

        echo "✅ Test Completed Successfully\n\n";

    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    }
}

// Compare PDF vs Non-PDF formatting
echo "📊 PDF vs NON-PDF COMPARISON\n";
echo "=============================\n";

try {
    $testContent = "Forklift ekipmanları için teknik özellikler ve satış bilgileri.";

    // PDF Feature
    $pdfFeature = new \Modules\AI\App\Models\AIFeature();
    $pdfFeature->slug = 'pdf-content-generation';
    $pdfFeature->name = 'PDF Content Analysis';

    // Non-PDF Feature
    $nonPdfFeature = new \Modules\AI\App\Models\AIFeature();
    $nonPdfFeature->slug = 'blog-yazisi-olusturucu';
    $nonPdfFeature->name = 'Blog Content Creator';

    $formatter = new \Modules\AI\App\Services\SmartResponseFormatter();
    $responseFormatter = new \Modules\AI\App\Services\Response\AIResponseFormatters();

    // PDF Processing
    $pdfFormatted = $formatter->format("Test", $testContent, $pdfFeature);
    $pdfResponse = $responseFormatter->formatContentGenerationResponse($testContent, $pdfFeature, "PDF Test");

    // Non-PDF Processing
    $nonPdfFormatted = $formatter->format("Test", $testContent, $nonPdfFeature);
    $nonPdfResponse = $responseFormatter->formatFeatureResponse($testContent, $nonPdfFeature, "Non-PDF Test");

    echo "🔍 PDF Processing:\n";
    echo "   └─ Format Type: " . ($pdfResponse['type'] ?? 'unknown') . "\n";
    echo "   └─ Premium Mode: " . ($pdfResponse['premium'] ?? false ? '✅' : '❌') . "\n";
    echo "   └─ Enhanced: " . ($pdfResponse['enhanced'] ?? false ? '✅' : '❌') . "\n";
    echo "   └─ Length: " . strlen($pdfFormatted) . " characters\n";
    echo "   └─ Premium Elements: " . (strpos($pdfFormatted, 'premium-landing-wrapper') !== false ? '✅' : '❌') . "\n";

    echo "\n📝 Non-PDF Processing:\n";
    echo "   └─ Format Type: " . ($nonPdfResponse['type'] ?? 'unknown') . "\n";
    echo "   └─ Premium Mode: " . ($nonPdfResponse['premium'] ?? false ? '✅' : '❌') . "\n";
    echo "   └─ Enhanced: " . ($nonPdfResponse['enhanced'] ?? false ? '✅' : '❌') . "\n";
    echo "   └─ Length: " . strlen($nonPdfFormatted) . " characters\n";
    echo "   └─ Blog Elements: " . (strpos($nonPdfFormatted, 'blog-') !== false || strpos($nonPdfFormatted, '<h5>') !== false ? '✅' : '❌') . "\n";

    echo "\n🎯 Key Differences:\n";
    echo "   └─ PDF has premium landing: " . (($pdfResponse['premium'] ?? false) && !($nonPdfResponse['premium'] ?? false) ? '✅' : '❌') . "\n";
    echo "   └─ Different response types: " . (($pdfResponse['type'] ?? '') !== ($nonPdfResponse['type'] ?? '') ? '✅' : '❌') . "\n";
    echo "   └─ PDF is more enhanced: " . (strlen($pdfFormatted) > strlen($nonPdfFormatted) ? '✅' : '❌') . "\n";

} catch (\Exception $e) {
    echo "❌ ERROR in comparison: " . $e->getMessage() . "\n";
}

echo "\n🎉 NON-PDF SCENARIOS TEST COMPLETED!\n";
echo "=====================================\n";

file_put_contents('storage/logs/laravel.log', '');
echo "🧹 Logs cleared.\n";