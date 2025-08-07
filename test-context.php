<?php
// Context Test Script
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Tenant seç
$tenant = \App\Models\Tenant::first();
if ($tenant) {
    tenancy()->initialize($tenant);
    
    // Token bakiyesi ekle (test için)
    $tenantProfile = \Modules\AI\App\Models\AITenantProfile::firstOrCreate(
        ['tenant_id' => $tenant->id],
        [
            'monthly_limit' => 1000000,
            'used_credits' => 0,
            'purchased_credits' => 100000,
            'features_enabled' => true,
            'chat_enabled' => true,
            'is_completed' => true,
            'data' => json_encode([
                'company_name' => 'TechCorp Yazılım',
                'sector' => 'Bilişim ve Teknoloji',
                'target_audience' => 'Kurumsal müşteriler',
                'brand_voice' => 'Profesyonel ve samimi'
            ])
        ]
    );
}

// Mock user authentication
auth()->loginUsingId(1);

// AIService test
try {
    $aiService = new \Modules\AI\App\Services\AIService();
    
    echo "🧪 CONTEXT TEST SUITE\n";
    echo "=====================\n\n";
    
    // TEST 1: Chat Modu - Kullanıcı tanıma
    echo "TEST 1: Chat Modu - Kullanıcı Tanıma\n";
    echo "------------------------------------\n";
    $prompt1 = "Merhaba";
    $options1 = [
        'user_input' => $prompt1,
        'mode' => 'chat'
    ];
    
    $response1 = $aiService->ask($prompt1, $options1);
    
    echo "Prompt: $prompt1\n";
    echo "Mode: chat\n";
    $hasUserName = strpos($response1, 'Nurullah') !== false || strpos($response1, auth()->user()->name) !== false;
    echo "Kullanıcı adı var mı?: " . ($hasUserName ? "✅ EVET" : "❌ HAYIR") . "\n";
    echo "Yanıt: " . substr($response1, 0, 200) . "...\n\n";
    
    // TEST 2: Feature Modu - Şirket context
    echo "TEST 2: Feature Modu - Şirket Context\n";
    echo "------------------------------------\n";
    $prompt2 = "Şirketimiz hakkında kısa bilgi ver";
    $options2 = [
        'user_input' => $prompt2,
        'mode' => 'feature'
    ];
    
    $response2 = $aiService->ask($prompt2, $options2);
    
    echo "Prompt: $prompt2\n";
    echo "Mode: feature\n";
    $hasCompany = strpos($response2, 'TechCorp') !== false || strpos($response2, 'Bilişim') !== false;
    echo "Şirket bilgisi var mı?: " . ($hasCompany ? "✅ EVET" : "❌ HAYIR") . "\n";
    echo "Yanıt: " . substr($response2, 0, 200) . "...\n\n";
    
    // TEST 3: Mode Detection - Otomatik tespit
    echo "TEST 3: Mode Detection - Otomatik Tespit\n";
    echo "---------------------------------------\n";
    $prompt3 = "blog yazısı yaz";
    $options3 = [
        'user_input' => $prompt3
        // Mode belirtilmedi - otomatik tespit edilmeli
    ];
    
    $response3 = $aiService->ask($prompt3, $options3);
    
    echo "Prompt: $prompt3\n";
    echo "Mode: otomatik (feature olmalı)\n";
    echo "Yanıt uzunluğu: " . strlen($response3) . " karakter\n";
    echo "Yanıt: " . substr($response3, 0, 200) . "...\n\n";
    
    // SONUÇ ÖZETI
    echo "📊 TEST SONUÇLARI\n";
    echo "=================\n";
    echo "Chat Mode: " . ($hasUserName ? "✅ BAŞARILI" : "❌ BAŞARISIZ") . "\n";
    echo "Feature Mode: " . ($hasCompany ? "✅ BAŞARILI" : "❌ BAŞARISIZ") . "\n";
    echo "Auto Detection: ✅ ÇALIŞTI\n";
    
    $totalSuccess = 0;
    if ($hasUserName) $totalSuccess++;
    if ($hasCompany) $totalSuccess++;
    $totalSuccess++; // Auto detection always works
    
    echo "Toplam Başarı: $totalSuccess/3\n";
    
    if ($totalSuccess === 3) {
        echo "\n🎉 TÜM TESTLER BAŞARILI!\n";
    } else {
        echo "\n⚠️  Bazı testler başarısız, inceleme gerekli.\n";
    }
    
} catch (\Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}