<?php
// Debug Context Script
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
    
    // AITenantProfile oluştur/güncelle
    $tenantProfile = \Modules\AI\App\Models\AITenantProfile::firstOrCreate(
        ['tenant_id' => $tenant->id],
        [
            'is_completed' => true,
            'data' => json_encode([
                'company_name' => 'TechCorp Yazılım',
                'sector' => 'Bilişim ve Teknoloji',
                'target_audience' => 'Kurumsal müşteriler',
                'brand_voice' => 'Profesyonel ve samimi'
            ])
        ]
    );
    
    // Profil complete yap
    $tenantProfile->update([
        'is_completed' => true,
        'data' => json_encode([
            'company_name' => 'TechCorp Yazılım',
            'sector' => 'Bilişim ve Teknoloji',
            'target_audience' => 'Kurumsal müşteriler',
            'brand_voice' => 'Profesyonel ve samimi'
        ])
    ]);
}

// Mock user
\App\Models\User::firstOrCreate(
    ['id' => 1],
    [
        'name' => 'Nurullah Okatan',
        'email' => 'nurullah@example.com',
        'password' => bcrypt('password')
    ]
);

auth()->loginUsingId(1);

echo "🔍 CONTEXT DEBUG\n";
echo "================\n\n";

echo "📋 KONTROLLER:\n";
echo "Tenant ID: " . ($tenant ? $tenant->id : 'YOK') . "\n";
echo "User ID: " . (auth()->id() ?: 'YOK') . "\n";
echo "User Name: " . (auth()->user() ? auth()->user()->name : 'YOK') . "\n";

if ($tenant) {
    $profile = \Modules\AI\App\Models\AITenantProfile::where('tenant_id', $tenant->id)->first();
    echo "Profile Var: " . ($profile ? 'EVET' : 'HAYIR') . "\n";
    echo "Profile Complete: " . ($profile && $profile->is_completed ? 'EVET' : 'HAYIR') . "\n";
    echo "Profile Data: " . ($profile && $profile->data ? 'VAR' : 'YOK') . "\n";
    
    if ($profile && $profile->data) {
        $data = json_decode($profile->data, true);
        echo "Company Name: " . ($data['company_name'] ?? 'YOK') . "\n";
    }
}

echo "\n🧪 CONTEXT METODLARI TEST:\n";

try {
    $aiService = new \Modules\AI\App\Services\AIService();
    
    // getTenantBrandContext() methodunu test et
    echo "\n1. getTenantBrandContext() test:\n";
    $brandContext = $aiService->getTenantBrandContext();
    echo "Brand Context: " . ($brandContext ? 'VAR (' . strlen($brandContext) . ' karakter)' : 'YOK') . "\n";
    if ($brandContext) {
        echo "İçerik önizleme: " . substr($brandContext, 0, 200) . "...\n";
    }
    
    // buildFullSystemPrompt() methodunu test et
    echo "\n2. buildFullSystemPrompt() test:\n";
    
    // Chat mode test
    $chatOptions = [
        'user_input' => 'Merhaba',
        'mode' => 'chat'
    ];
    $chatPrompt = $aiService->buildFullSystemPrompt('', $chatOptions);
    echo "Chat Prompt Length: " . strlen($chatPrompt) . " karakter\n";
    $hasUserName = strpos($chatPrompt, 'Nurullah') !== false || strpos($chatPrompt, auth()->user()->name) !== false;
    echo "User Name in Chat Prompt: " . ($hasUserName ? 'VAR' : 'YOK') . "\n";
    
    // Feature mode test  
    $featureOptions = [
        'user_input' => 'Şirket hakkında bilgi',
        'mode' => 'feature'
    ];
    $featurePrompt = $aiService->buildFullSystemPrompt('', $featureOptions);
    echo "Feature Prompt Length: " . strlen($featurePrompt) . " karakter\n";
    $hasCompany = strpos($featurePrompt, 'TechCorp') !== false || strpos($featurePrompt, 'Bilişim') !== false;
    echo "Company Info in Feature Prompt: " . ($hasCompany ? 'VAR' : 'YOK') . "\n";
    
    echo "\n📝 PROMPT İÇERİKLERİ:\n";
    echo "Chat Prompt:\n" . str_repeat('-', 50) . "\n";
    echo substr($chatPrompt, 0, 500) . "...\n\n";
    
    echo "Feature Prompt:\n" . str_repeat('-', 50) . "\n";
    echo substr($featurePrompt, 0, 500) . "...\n\n";
    
} catch (\Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}