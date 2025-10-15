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

// Create request
$request = Illuminate\Http\Request::create('/api/ai/v1/chat', 'POST', [
    'message' => 'transpalet ariyorum',
    'feature' => 'shop_assistant'
]);
$request->headers->set('X-Tenant', '2');
$request->headers->set('Accept', 'application/json');

// Call controller with dependency injection
$controller = $app->make(Modules\AI\App\Http\Controllers\Api\PublicAIController::class);

try {
    $response = $controller->shopAssistantChat($request);
    $content = $response->getContent();
    $data = json_decode($content, true);

    echo "\n=== AI RESPONSE ===\n";
    if (isset($data['reply'])) {
        echo $data['reply'];
        echo "\n\n=== ANALYSIS ===\n";

        // Check for links
        $hasHttp = strpos($data['reply'], 'http') !== false;
        $hasMarkdown = strpos($data['reply'], '[') !== false && strpos($data['reply'], ']') !== false;
        $hasProduct = stripos($data['reply'], 'litef') !== false || stripos($data['reply'], 'ürün') !== false;

        echo "✓ Contains HTTP links: " . ($hasHttp ? "YES" : "NO") . "\n";
        echo "✓ Contains Markdown links: " . ($hasMarkdown ? "YES" : "NO") . "\n";
        echo "✓ Mentions products: " . ($hasProduct ? "YES" : "NO") . "\n";

        if ($hasHttp && $hasMarkdown) {
            echo "\n✅ SUCCESS: AI is showing product links!\n";
        } else {
            echo "\n❌ FAIL: AI is NOT showing product links!\n";
        }
    } else {
        echo "Full response:\n";
        print_r($data);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
