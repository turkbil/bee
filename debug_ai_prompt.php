<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Initialize tenant 2
$tenant = App\Models\Tenant::find(2);
if (!$tenant) {
    die("Tenant 2 not found!\n");
}
tenancy()->initialize($tenant);

echo "=== TENANT 2 (İXTİF) AI PROMPT DEBUG ===\n\n";

// Build context like the controller does
$contextOrchestrator = $app->make(App\Services\AI\Context\ModuleContextOrchestrator::class);

$contextOptions = [
    'product_id' => null,
    'category_id' => null,
    'page_slug' => null,
    'session_id' => 'debug-session',
];

$aiContext = $contextOrchestrator->buildUserContext(
    'transpalet ariyorum',
    $contextOptions
);

echo "📊 CONTEXT SUMMARY:\n";
echo "- Modules: " . implode(', ', array_keys($aiContext['context']['modules'] ?? [])) . "\n";
echo "- Has Shop Module: " . (isset($aiContext['context']['modules']['shop']) ? 'YES' : 'NO') . "\n";

if (isset($aiContext['context']['modules']['shop'])) {
    $shopContext = $aiContext['context']['modules']['shop'];
    echo "- Shop Total Products: " . ($shopContext['total_products'] ?? 0) . "\n";
    echo "- Shop All Products Count: " . (count($shopContext['all_products'] ?? [])) . "\n";
    echo "- Shop Featured Products: " . (count($shopContext['featured_products'] ?? [])) . "\n";
    echo "- Shop Categories: " . (count($shopContext['categories'] ?? [])) . "\n";
}

echo "\n";
echo "🤖 BUILDING ENHANCED SYSTEM PROMPT...\n\n";

// Now call buildEnhancedSystemPrompt method (we need to use reflection since it's private)
$controller = $app->make(Modules\AI\App\Http\Controllers\Api\PublicAIController::class);
$reflectionClass = new ReflectionClass($controller);
$method = $reflectionClass->getMethod('buildEnhancedSystemPrompt');
$method->setAccessible(true);

$enhancedPrompt = $method->invoke($controller, $aiContext);

echo "📏 PROMPT LENGTH: " . strlen($enhancedPrompt) . " characters (~" . intval(strlen($enhancedPrompt) / 4) . " tokens)\n\n";
echo "🔍 CHECKING FOR KEY PHRASES:\n";
echo "- Contains 'Mevcut Ürünler': " . (str_contains($enhancedPrompt, 'Mevcut Ürünler') ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'litef': " . (stripos($enhancedPrompt, 'litef') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'transpalet': " . (stripos($enhancedPrompt, 'transpalet') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'EN ÖNEMLİ KURAL': " . (str_contains($enhancedPrompt, 'EN ÖNEMLİ KURAL') ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains 'İXTİF' (tenant-specific): " . (stripos($enhancedPrompt, 'ixtif') !== false ? '✅ YES' : '❌ NO') . "\n";
echo "- Contains HTTP URLs: " . (preg_match('/http[s]?:\/\//', $enhancedPrompt) ? '✅ YES' : '❌ NO') . "\n";

echo "\n";
echo "📜 FULL PROMPT (first 3000 chars):\n";
echo "================================================================================\n";
echo mb_substr($enhancedPrompt, 0, 3000);
echo "\n...\n";
echo "================================================================================\n\n";

// Extract product URLs if any
preg_match_all('/http[s]?:\/\/[^\s\)]+/', $enhancedPrompt, $urlMatches);
if (!empty($urlMatches[0])) {
    echo "🔗 FOUND " . count($urlMatches[0]) . " URLs IN PROMPT:\n";
    foreach (array_slice($urlMatches[0], 0, 10) as $url) {
        echo "  - {$url}\n";
    }
    if (count($urlMatches[0]) > 10) {
        echo "  ... and " . (count($urlMatches[0]) - 10) . " more\n";
    }
} else {
    echo "❌ NO URLS FOUND IN PROMPT!\n";
}

echo "\n✅ DEBUG COMPLETE\n";
