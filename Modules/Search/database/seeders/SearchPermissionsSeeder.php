<?php

declare(strict_types=1);

namespace Modules\Search\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Tenant;

class SearchPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating Search module permissions...');

        // Define permissions (CRUD)
        $permissions = [
            ['name' => 'search.view', 'description' => 'Arama - Görüntüleme'],
            ['name' => 'search.create', 'description' => 'Arama - Oluşturma'],
            ['name' => 'search.update', 'description' => 'Arama - Güncelleme'],
            ['name' => 'search.delete', 'description' => 'Arama - Silme'],
        ];

        // Create permissions in central database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'guard_name' => 'web',
                ]
            );

            $this->command->info("✓ Created permission: {$permission['name']}");
        }

        // Assign permissions to Super Admin role in each tenant
        $this->assignPermissionsToTenants($permissions);

        $this->command->info('✓ Search permissions seeded successfully!');
    }

    /**
     * Assign permissions to tenants
     */
    protected function assignPermissionsToTenants(array $permissions): void
    {
        try {
            $tenants = Tenant::all();

            $this->command->info("Found {$tenants->count()} tenants");

            foreach ($tenants as $tenant) {
                $tenant->run(function () use ($permissions, $tenant) {
                    // Create permissions in tenant database
                    foreach ($permissions as $permission) {
                        Permission::firstOrCreate(
                            ['name' => $permission['name']],
                            [
                                'description' => $permission['description'],
                                'guard_name' => 'web',
                            ]
                        );
                    }

                    // Assign to Root role (or Admin as fallback)
                    $adminRole = Role::where('name', 'root')->first();

                    if (!$adminRole) {
                        $adminRole = Role::where('name', 'admin')->first();
                    }

                    if ($adminRole) {
                        $permissionNames = array_column($permissions, 'name');
                        $adminRole->givePermissionTo($permissionNames);

                        $this->command->info("  ✓ Assigned permissions to {$adminRole->name} role in tenant: {$tenant->id}");
                    } else {
                        $this->command->warn("  ⚠ Admin role not found in tenant: {$tenant->id}");
                    }

                    // Add module to module_tenants table
                    $this->assignModuleToTenant($tenant);
                });
            }
        } catch (\Exception $e) {
            $this->command->error('Error assigning permissions to tenants: ' . $e->getMessage());
        }
    }

    /**
     * Assign module to tenant
     */
    protected function assignModuleToTenant(Tenant $tenant): void
    {
        try {
            $searchModule = \DB::table('modules')->where('name', 'search')->first();

            if (!$searchModule) {
                $this->command->warn('  ⚠ Search module not found in modules table');
                return;
            }

            // Check if module_tenants table exists
            if (!\Schema::hasTable('module_tenants')) {
                $this->command->warn('  ⚠ module_tenants table does not exist');
                return;
            }

            // Check if already assigned
            $existing = \DB::table('module_tenants')
                ->where('tenant_id', $tenant->id)
                ->where('module_id', $searchModule->module_id)
                ->first();

            if (!$existing) {
                \DB::table('module_tenants')->insert([
                    'tenant_id' => $tenant->id,
                    'module_id' => $searchModule->module_id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("  ✓ Assigned Search module to tenant: {$tenant->id}");
            }
        } catch (\Exception $e) {
            $this->command->warn("  ⚠ Could not assign module to tenant {$tenant->id}: " . $e->getMessage());
        }
    }
}
