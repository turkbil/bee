<?php

echo "=== LOKAL AI TEST (TENANT2 - İXTİF) ===\n\n";

// Test URL - İXTİF tenant (a.test)
$url = "https://a.test/api/ai/v1/shop-assistant/chat";

// Test data
$data = [
    'message' => 'transpalet ariyorum'
];

// cURL ayarları
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

echo "📍 URL: $url\n";
echo "📨 Mesaj: 'transpalet ariyorum'\n";
echo "🏢 Tenant: a.test (İXTİF - tenant2)\n\n";

echo "⏳ İstek gönderiliyor...\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Hatası: $error\n";
    exit(1);
}

echo "📊 HTTP Status: $httpCode\n\n";

if ($httpCode === 200) {
    $result = json_decode($response, true);

    if ($result && isset($result['success']) && $result['success']) {
        echo "✅ BAŞARILI!\n\n";
        echo "🤖 AI Yanıtı:\n";
        echo "=====================================\n";
        echo $result['data']['message'] ?? 'Yanıt bulunamadı';
        echo "\n=====================================\n\n";

        // Analiz
        $message = $result['data']['message'] ?? '';

        echo "📋 YANIT ANALİZİ:\n";
        echo "- Uzunluk: " . strlen($message) . " karakter\n";
        echo "- Link var mı? " . (strpos($message, 'http') !== false ? '✅ EVET' : '❌ HAYIR') . "\n";
        echo "- Markdown link var mı? " . (strpos($message, '](http') !== false ? '✅ EVET' : '❌ HAYIR') . "\n";
        echo "- 'transpalet' geçiyor mu? " . (stripos($message, 'transpalet') !== false ? '✅ EVET' : '❌ HAYIR') . "\n";
        echo "- 'özellik' var mı? " . (stripos($message, 'özellik') !== false || stripos($message, 'kapasite') !== false ? '✅ EVET' : '❌ HAYIR') . "\n";
        echo "- 'karşılaştırma' var mı? " . (stripos($message, 'karşılaştırma') !== false || stripos($message, 'fark') !== false ? '✅ EVET' : '❌ HAYIR') . "\n";

        // Link sayısı
        preg_match_all('/\[([^\]]+)\]\((http[^\)]+)\)/', $message, $matches);
        $linkCount = count($matches[0]);
        echo "- Kaç ürün linki var? " . $linkCount . " adet\n";

        if ($linkCount > 0) {
            echo "\n🔗 Bulunan Linkler:\n";
            foreach ($matches[1] as $index => $title) {
                echo "  " . ($index + 1) . ". $title\n";
            }
        }

        echo "\n";

        // Beklenti kontrolü
        echo "🎯 BEKLENTİ KONTROLÜ:\n";
        $expectations = [
            '3-5 ürün linki var' => $linkCount >= 3 && $linkCount <= 5,
            'Ürün özellikleri açıklanmış' => stripos($message, 'kapasite') !== false || stripos($message, 'özellik') !== false,
            'Karşılaştırma yapılmış' => stripos($message, 'karşılaştırma') !== false || stripos($message, 'fark') !== false || stripos($message, 'daha') !== false,
            'Detay sorusu sorulmuş' => strpos($message, '?') !== false
        ];

        foreach ($expectations as $expectation => $met) {
            echo ($met ? '✅' : '❌') . " $expectation\n";
        }

    } else {
        echo "❌ API Hatası:\n";
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "\n";
    }
} else {
    echo "❌ HTTP Hatası: $httpCode\n";
    echo "Yanıt: $response\n";
}

echo "\n=== TEST TAMAMLANDI ===\n";
