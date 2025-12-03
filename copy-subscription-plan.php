<?php

require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Subscription\App\Models\SubscriptionPlan;
use Stancl\Tenancy\Facades\Tenancy;

echo "🔍 Muzibu planını kontrol ediliyor...\n";

// Muzibu tenant (1001)
$muzibuTenant = \Stancl\Tenancy\Database\Models\Tenant::find(1001);
Tenancy::initialize($muzibuTenant);

$muzibuPlan = SubscriptionPlan::where('is_active', true)->first();

if (!$muzibuPlan) {
    echo "❌ Muzibu'da plan bulunamadı\n";
    exit(1);
}

echo "✅ Muzibu planı bulundu:\n";
echo "   Title: " . json_encode($muzibuPlan->title, JSON_UNESCAPED_UNICODE) . "\n";
echo "   Monthly: ₺" . $muzibuPlan->price_monthly . "\n";
echo "   Yearly: ₺" . $muzibuPlan->price_yearly . "\n\n";

// İxtif tenant (2)
echo "🔄 İxtif'e kopyalanıyor...\n";
Tenancy::end();
$ixtifTenant = \Stancl\Tenancy\Database\Models\Tenant::find(2);
Tenancy::initialize($ixtifTenant);

// Check if plan already exists
$existingPlan = SubscriptionPlan::where('is_active', true)->first();
if ($existingPlan) {
    echo "📝 İxtif'te zaten aktif plan var (ID: {$existingPlan->subscription_plan_id}), güncelleniyor...\n";
    $existingPlan->update([
        'title' => $muzibuPlan->title,
        'description' => $muzibuPlan->description,
        'features' => $muzibuPlan->features,
        'price_monthly' => $muzibuPlan->price_monthly,
        'price_yearly' => $muzibuPlan->price_yearly,
        'is_active' => true,
        'is_public' => true,
        'sort_order' => 1,
    ]);
    echo "✅ Plan güncellendi!\n";
} else {
    // Create new plan
    $newPlan = SubscriptionPlan::create([
        'title' => $muzibuPlan->title,
        'description' => $muzibuPlan->description,
        'features' => $muzibuPlan->features,
        'price_monthly' => $muzibuPlan->price_monthly,
        'price_yearly' => $muzibuPlan->price_yearly,
        'is_active' => true,
        'is_public' => true,
        'sort_order' => 1,
    ]);
    echo "✅ Yeni plan oluşturuldu! ID: {$newPlan->subscription_plan_id}\n";
}

echo "\n🎉 İşlem tamamlandı!\n";
