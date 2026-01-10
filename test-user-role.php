<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Tenant context'e gir
$tenantModel = \App\Models\Tenant::find(1001); // Muzibu
tenancy()->initialize($tenantModel);

// Kullanıcıyı yükle
$user = \App\Models\User::with(['roles'])->find(3);

echo "=== USER ROLE DEBUG ===\n";
echo "User ID: {$user->id}\n";
echo "User Name: {$user->name}\n";
echo "User Email: {$user->email}\n\n";

echo "=== ROLES RELATIONSHIP ===\n";
$roles = $user->roles;
echo "Roles Count: " . $roles->count() . "\n";

if ($roles->count() > 0) {
    foreach ($roles as $role) {
        echo "\nRole ID: {$role->id}\n";
        echo "Role Name: {$role->name}\n";
        echo "Role Type: {$role->role_type}\n";
        echo "Guard Name: {$role->guard_name}\n";
    }

    $firstRole = $roles->first();
    echo "\n=== FIRST ROLE (Like Component Does) ===\n";
    echo "First Role Name: {$firstRole->name}\n";
} else {
    echo "NO ROLES FOUND!\n";
}

echo "\n=== hasRole() METHOD ===\n";
echo "hasRole('admin'): " . ($user->hasRole('admin') ? 'TRUE' : 'FALSE') . "\n";
echo "hasRole('editor'): " . ($user->hasRole('editor') ? 'TRUE' : 'FALSE') . "\n";
echo "hasRole('user'): " . ($user->hasRole('user') ? 'TRUE' : 'FALSE') . "\n";

echo "\n=== DIRECT DB CHECK ===\n";
$dbRoles = \DB::table('model_has_roles')
    ->where('model_id', 3)
    ->where('model_type', 'App\Models\User')
    ->get();

echo "DB Roles Count: " . $dbRoles->count() . "\n";
foreach ($dbRoles as $dbRole) {
    $roleName = \DB::table('roles')->where('id', $dbRole->role_id)->value('name');
    echo "DB Role ID: {$dbRole->role_id}, Name: {$roleName}\n";
}

echo "\n=== SPATIE PERMISSION CONFIG ===\n";
echo "Permission Model: " . config('permission.models.role') . "\n";
echo "Table: " . config('permission.table_names.model_has_roles') . "\n";
echo "Guard: web\n";
