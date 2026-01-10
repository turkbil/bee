<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Tenant context'e gir
$tenantModel = \App\Models\Tenant::find(1001);
tenancy()->initialize($tenantModel);

echo "=== FIXING SPATIE PERMISSION CACHE ===\n\n";

// 1. Spatie Permission cache'ini temizle
app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
echo "✅ Spatie Permission cache cleared\n";

// 2. Laravel cache'ini temizle
\Illuminate\Support\Facades\Artisan::call('cache:clear');
echo "✅ Laravel cache cleared\n";

// 3. Kullanıcıyı tekrar test et
$user = \App\Models\User::with(['roles'])->find(3);
echo "\n=== TEST AFTER FIX ===\n";
echo "User: {$user->name}\n";
echo "Roles Count: " . $user->roles->count() . "\n";

if ($user->roles->count() > 0) {
    $role = $user->roles->first();
    echo "Role Name: {$role->name}\n";
    echo "hasRole('admin'): " . ($user->hasRole('admin') ? 'TRUE' : 'FALSE') . "\n";
} else {
    echo "❌ STILL NO ROLES! Need deeper fix.\n";
}
