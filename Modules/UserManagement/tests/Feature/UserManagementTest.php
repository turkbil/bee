<?php

namespace Modules\UserManagement\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Modules\UserManagement\App\Models\ModulePermission;
use Modules\UserManagement\App\Models\UserModulePermission;

class UserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $superAdmin;
    protected $admin;
    protected $editor;
    protected $user;
    protected $adminRole;
    protected $editorRole;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->initializeTenancy();
        
        // Test rolleri oluştur
        $this->adminRole = Role::create(['name' => 'Super Admin']);
        $this->editorRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'Editor']);
        $this->userRole = Role::create(['name' => 'User']);
        
        // Test permissions oluştur
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Rollere yetkileri ata
        $this->adminRole->givePermissionTo($permissions);
        $this->editorRole->givePermissionTo(['users.view', 'users.edit']);
        
        // Test kullanıcıları oluştur
        $this->superAdmin = User::factory()->create(['name' => 'Super Admin']);
        $this->superAdmin->assignRole($this->adminRole);
        
        $this->admin = User::factory()->create(['name' => 'Admin User']);
        $this->admin->assignRole($this->editorRole);
        
        $this->editor = User::factory()->create(['name' => 'Editor User']);
        $this->editor->assignRole($userRole);
        
        $this->user = User::factory()->create(['name' => 'Normal User']);
        $this->user->assignRole($this->userRole);
    }

    /** @test */
    public function super_admin_can_access_user_management()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->get('/admin/users');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function normal_user_cannot_access_user_management()
    {
        $response = $this->actingAs($this->user)
                         ->get('/admin/users');
        
        $response->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_create_user()
    {
        $userData = [
            'name' => 'Yeni Kullanıcı',
            'email' => 'yeni@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
            'roles' => [$this->userRole->id]
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/users', $userData);
        
        $response->assertRedirect();
        
        // Kullanıcının oluşturulduğunu kontrol et
        $this->assertDatabaseHas('users', [
            'name' => 'Yeni Kullanıcı',
            'email' => 'yeni@example.com',
            'status' => 'active'
        ]);
        
        // Şifrenin hash'lendiğini kontrol et
        $user = User::where('email', 'yeni@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
        
        // Rolün atandığını kontrol et
        $this->assertTrue($user->hasRole($this->userRole));
    }

    /** @test */
    public function user_creation_requires_valid_email()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/users', $userData);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_creation_requires_unique_email()
    {
        $userData = [
            'name' => 'Test User',
            'email' => $this->user->email, // Mevcut kullanıcının email'i
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/users', $userData);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function password_confirmation_must_match()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password'
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/users', $userData);
        
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function super_admin_can_update_user()
    {
        $updateData = [
            'name' => 'Güncellenmiş İsim',
            'email' => 'updated@example.com',
            'status' => 'inactive',
            'roles' => [$this->editorRole->id]
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->put('/admin/users/' . $this->user->id, $updateData);
        
        $response->assertRedirect();
        
        // Kullanıcının güncellendiğini kontrol et
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Güncellenmiş İsim',
            'email' => 'updated@example.com',
            'status' => 'inactive'
        ]);
        
        // Rolün güncellendiğini kontrol et
        $this->user->refresh();
        $this->assertTrue($this->user->hasRole($this->editorRole));
        $this->assertFalse($this->user->hasRole($this->userRole));
    }

    /** @test */
    public function super_admin_can_delete_user()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->delete('/admin/users/' . $this->user->id);
        
        $response->assertRedirect();
        
        // Soft delete kontrolü
        $this->assertSoftDeleted('users', [
            'id' => $this->user->id
        ]);
    }

    /** @test */
    public function super_admin_can_create_role()
    {
        $roleData = [
            'name' => 'Yeni Rol',
            'guard_name' => 'web',
            'permissions' => ['users.view', 'users.edit']
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/roles', $roleData);
        
        $response->assertRedirect();
        
        // Rolün oluşturulduğunu kontrol et
        $this->assertDatabaseHas('roles', [
            'name' => 'Yeni Rol',
            'guard_name' => 'web'
        ]);
        
        // Yetkilerin atandığını kontrol et
        $role = Role::where('name', 'Yeni Rol')->first();
        $this->assertTrue($role->hasPermissionTo('users.view'));
        $this->assertTrue($role->hasPermissionTo('users.edit'));
    }

    /** @test */
    public function role_name_must_be_unique()
    {
        $roleData = [
            'name' => $this->adminRole->name,
            'guard_name' => 'web'
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/roles', $roleData);
        
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function super_admin_can_update_role()
    {
        $updateData = [
            'name' => 'Güncellenmiş Rol',
            'guard_name' => 'web',
            'permissions' => ['users.view']
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->put('/admin/roles/' . $this->editorRole->id, $updateData);
        
        $response->assertRedirect();
        
        // Rolün güncellendiğini kontrol et
        $this->assertDatabaseHas('roles', [
            'id' => $this->editorRole->id,
            'name' => 'Güncellenmiş Rol'
        ]);
        
        // Yetkilerin güncellendiğini kontrol et
        $this->editorRole->refresh();
        $this->assertTrue($this->editorRole->hasPermissionTo('users.view'));
        $this->assertFalse($this->editorRole->hasPermissionTo('users.edit'));
    }

    /** @test */
    public function protected_roles_cannot_be_deleted()
    {
        // Super Admin rolünün korumalı olduğunu varsayıyoruz
        $response = $this->actingAs($this->superAdmin)
                         ->delete('/admin/roles/' . $this->adminRole->id);
        
        $response->assertStatus(403);
        
        // Rolün hala var olduğunu kontrol et
        $this->assertDatabaseHas('roles', [
            'id' => $this->adminRole->id
        ]);
    }

    /** @test */
    public function super_admin_can_create_permission()
    {
        $permissionData = [
            'name' => 'new.permission',
            'guard_name' => 'web',
            'description' => 'Yeni yetki açıklaması'
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/permissions', $permissionData);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('permissions', [
            'name' => 'new.permission',
            'guard_name' => 'web'
        ]);
    }

    /** @test */
    public function permission_name_must_be_unique()
    {
        $permissionData = [
            'name' => 'users.view', // Zaten var olan yetki
            'guard_name' => 'web'
        ];
        
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/permissions', $permissionData);
        
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function bulk_user_operations_work()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // Toplu silme
        $response = $this->actingAs($this->superAdmin)
                         ->delete('/admin/users/bulk', [
                             'ids' => [$user1->id, $user2->id]
                         ]);
        
        $response->assertRedirect();
        
        $this->assertSoftDeleted('users', ['id' => $user1->id]);
        $this->assertSoftDeleted('users', ['id' => $user2->id]);
    }

    /** @test */
    public function bulk_status_change_works()
    {
        $user1 = User::factory()->create(['status' => 'active']);
        $user2 = User::factory()->create(['status' => 'active']);
        
        $response = $this->actingAs($this->superAdmin)
                         ->patch('/admin/users/bulk-status', [
                             'ids' => [$user1->id, $user2->id],
                             'status' => 'inactive'
                         ]);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('users', [
            'id' => $user1->id,
            'status' => 'inactive'
        ]);
        
        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'status' => 'inactive'
        ]);
    }

    /** @test */
    public function module_permissions_work_correctly()
    {
        // Modül yetkisi oluştur
        $modulePermission = ModulePermission::create([
            'module_name' => 'Portfolio',
            'permission_name' => 'portfolio.view',
            'description' => 'Portfolio görüntüleme yetkisi'
        ]);
        
        // Kullanıcıya modül yetkisi ata
        UserModulePermission::create([
            'user_id' => $this->user->id,
            'module_permission_id' => $modulePermission->id,
            'granted' => true
        ]);
        
        // Kullanıcının modül yetkisine sahip olduğunu kontrol et
        $this->assertTrue(
            $this->user->hasModulePermission('Portfolio', 'portfolio.view')
        );
    }

    /** @test */
    public function activity_logs_are_recorded()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->post('/admin/users', [
                             'name' => 'Log Test User',
                             'email' => 'log@example.com',
                             'password' => 'password123',
                             'password_confirmation' => 'password123',
                             'status' => 'active'
                         ]);
        
        // Aktivite loglarının kaydedildiğini kontrol et
        $this->assertDatabaseHas('activity_log', [
            'causer_id' => $this->superAdmin->id,
            'description' => 'Kullanıcı oluşturuldu',
            'subject_type' => User::class
        ]);
    }

    /** @test */
    public function user_search_works()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->get('/admin/users?search=' . $this->user->name);
        
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
    }

    /** @test */
    public function user_filtering_by_role_works()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->get('/admin/users?role=' . $this->adminRole->id);
        
        $response->assertStatus(200);
        $response->assertSee($this->superAdmin->name);
        $response->assertDontSee($this->user->name);
    }

    /** @test */
    public function user_filtering_by_status_works()
    {
        $this->user->update(['status' => 'inactive']);
        
        $response = $this->actingAs($this->superAdmin)
                         ->get('/admin/users?status=inactive');
        
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
    }

    /** @test */
    public function password_update_works()
    {
        $response = $this->actingAs($this->superAdmin)
                         ->patch('/admin/users/' . $this->user->id . '/password', [
                             'password' => 'newpassword123',
                             'password_confirmation' => 'newpassword123'
                         ]);
        
        $response->assertRedirect();
        
        // Şifrenin güncellendiğini kontrol et
        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    /** @test */
    public function tenant_isolation_works_for_users()
    {
        $this->switchTenant('another-tenant');
        
        // Farklı tenant'ta kullanıcı oluştur
        $otherTenantUser = User::factory()->create([
            'name' => 'Other Tenant User'
        ]);
        
        // Ana tenant'a geri dön
        $this->switchTenant('main-tenant');
        
        // Ana tenant'taki admin diğer tenant'ın kullanıcısını görememeli
        $response = $this->actingAs($this->superAdmin)
                         ->get('/admin/users');
        
        $response->assertStatus(200);
        $response->assertDontSee('Other Tenant User');
    }

    private function initializeTenancy()
    {
        // Tenant context'i ayarla
    }

    private function switchTenant($tenantId)
    {
        // Tenant switching logic
    }
}