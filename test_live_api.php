<?php

// Test live ixtif.com API
$url = 'https://ixtif.com/api/ai/v1/shop-assistant/chat';

$data = [
    'message' => 'transpalet ariyorum',
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Tenant: 2',
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local testing

echo "\n=== CANLI SİSTEM TEST (ixtif.com) ===\n";
echo "📤 Gönderilen Mesaj: 'transpalet ariyorum'\n";
echo "🌐 URL: $url\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL ERROR: $error\n";
    exit(1);
}

echo "📊 HTTP Status: $httpCode\n\n";

if ($httpCode !== 200) {
    echo "❌ HTTP ERROR: Status $httpCode\n";
    echo "Response: $response\n";
    exit(1);
}

$result = json_decode($response, true);

if (!$result) {
    echo "❌ JSON PARSE ERROR\n";
    echo "Raw Response: $response\n";
    exit(1);
}

if (!isset($result['success']) || !$result['success']) {
    echo "❌ API ERROR: " . ($result['error'] ?? 'Unknown error') . "\n";
    echo "Full Response: " . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(1);
}

// Success - analyze response
$message = $result['data']['message'] ?? '';
$conversationId = $result['data']['conversation_id'] ?? 'N/A';
$sessionId = $result['data']['session_id'] ?? 'N/A';

echo "✅ STATUS: SUCCESS\n";
echo "📊 Conversation ID: $conversationId\n";
echo "🆔 Session ID: " . substr($sessionId, 0, 16) . "...\n\n";

echo "🤖 AI YANITI:\n";
echo str_repeat('─', 80) . "\n";
echo $message . "\n";
echo str_repeat('─', 80) . "\n\n";

// Analyze response
$hasHttp = stripos($message, 'http') !== false;
$hasMarkdown = strpos($message, '[') !== false && strpos($message, '](') !== false;
$hasProduct = stripos($message, 'litef') !== false || stripos($message, 'ürün') !== false;
$hasNegative = stripos($message, 'elimde') !== false && stripos($message, 'yok') !== false;

echo "🔍 SONUÇ ANALİZİ:\n";
echo "  - HTTP linkler var mı? " . ($hasHttp ? "✅ EVET" : "❌ HAYIR") . "\n";
echo "  - Markdown linkler var mı? " . ($hasMarkdown ? "✅ EVET" : "❌ HAYIR") . "\n";
echo "  - Ürün bahsi var mı? " . ($hasProduct ? "✅ EVET" : "❌ HAYIR") . "\n";
echo "  - 'Elimde yok' dedi mi? " . ($hasNegative ? "❌ EVET (KÖTÜ!)" : "✅ HAYIR (İYİ!)") . "\n\n";

// Count product links
preg_match_all('/\[([^\]]+)\]\(([^)]+)\)/', $message, $matches);
$linkCount = count($matches[0]);

if ($linkCount > 0) {
    echo "🔗 BULUNAN LİNKLER ($linkCount adet):\n";
    for ($i = 0; $i < min($linkCount, 5); $i++) {
        echo "  " . ($i + 1) . ". [{$matches[1][$i]}]({$matches[2][$i]})\n";
    }
    if ($linkCount > 5) {
        echo "  ... ve " . ($linkCount - 5) . " link daha\n";
    }
    echo "\n";
}

// Final verdict
if ($hasHttp && $hasMarkdown && !$hasNegative && $linkCount >= 3) {
    echo "🎉 TEST TAMAMEN BAŞARILI!\n";
    echo "   ✅ AI ürün linkleri gösteriyor\n";
    echo "   ✅ En az 3 ürün önerisi var\n";
    echo "   ✅ Olumsuz ifade yok\n";
    exit(0);
} elseif ($hasMarkdown && $linkCount > 0) {
    echo "⚠️ TEST KISMEN BAŞARILI\n";
    echo "   ✅ AI linkler gösteriyor\n";
    echo "   ⚠️ Ancak yeterli sayıda değil veya format sorunlu\n";
    exit(0);
} else {
    echo "❌ TEST BAŞARISIZ!\n";
    echo "   ❌ AI ürün linki vermedi\n";
    echo "   ❌ Hala eski davranışı sergiliyor\n";
    exit(1);
}
