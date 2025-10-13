<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Yeni tenant oluşturulduğunda otomatik çalışacak seeder
     */
    public function run(): void
    {
        $this->command->info('🚀 Tenant seeding başlatılıyor...');

        // Mevcut tenant'ı al
        $currentTenant = tenant();

        if (!$currentTenant) {
            $this->command->error('❌ Tenant context bulunamadı!');
            return;
        }

        // 1. Dilleri kopyala (100 dil, sadece TR aktif)
        $this->seedLanguages();

        // 2. Modül-Tenant ilişkilerini oluştur
        $this->seedModuleTenants($currentTenant);

        // 3. Permissions'ları kopyala
        $this->seedPermissions();

        // 4. Rolleri oluştur
        $this->seedRoles();

        // 5. Rol-Permission ilişkilerini oluştur
        $this->assignPermissionsToRoles();

        // 6. Kullanıcıları oluştur
        $this->seedUsers();

        // 7. Menüyü kopyala
        $this->seedMenu();

        // 8. Anasayfayı oluştur
        $this->seedHomePage();

        // 9. AI Bilgi Bankası
        $this->seedAIKnowledgeBase();

        $this->command->info('✅ Tenant seeding tamamlandı!');
        $this->command->info('📧 Login: nurullah@nurullah.net / g0nulcelen');
        $this->command->info('📧 Login: info@turkbilisim.com.tr / gonu1celen');
    }

    /**
     * 1. Central DB'den 100 dili kopyala, sadece TR aktif
     */
    protected function seedLanguages(): void
    {
        $this->command->info('  📝 Diller kopyalanıyor...');

        // Central DB'den dilleri al
        $languages = DB::connection('mysql')->table('tenant_languages')->get();

        foreach ($languages as $lang) {
            // Sadece Türkçe aktif olsun
            $isActive = ($lang->code === 'tr') ? 1 : 0;
            $isVisible = ($lang->code === 'tr') ? 1 : 0;
            $isDefault = ($lang->code === 'tr') ? 1 : 0;
            $isMainLanguage = ($lang->code === 'tr') ? 1 : 0;

            DB::table('tenant_languages')->insertOrIgnore([
                'code' => $lang->code,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'direction' => $lang->direction,
                'flag_icon' => $lang->flag_icon,
                'is_active' => $isActive,
                'is_visible' => $isVisible,
                'is_main_language' => $isMainLanguage,
                'is_default' => $isDefault,
                'is_rtl' => $lang->is_rtl,
                'flag_emoji' => $lang->flag_emoji,
                'url_prefix_mode' => $lang->url_prefix_mode,
                'sort_order' => $lang->sort_order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('    ✅ 100 dil eklendi (Sadece TR aktif)');
    }

    /**
     * 2. Tüm aktif modülleri module_tenants tablosuna ekle
     */
    protected function seedModuleTenants(Tenant $tenant): void
    {
        $this->command->info('  📝 Modüller aktif ediliyor...');

        // Central DB'den aktif modülleri al
        $modules = DB::connection('mysql')->table('modules')
            ->where('is_active', 1)
            ->get();

        foreach ($modules as $module) {
            DB::connection('mysql')->table('module_tenants')->insertOrIgnore([
                'tenant_id' => $tenant->id,
                'module_id' => $module->module_id,
                'is_active' => 1,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('    ✅ ' . $modules->count() . ' modül aktif edildi');
    }

    /**
     * 3. Central DB'den permissions'ları kopyala
     */
    protected function seedPermissions(): void
    {
        $this->command->info('  📝 Permissions kopyalanıyor...');

        // Central DB'den permissions'ları al
        $permissions = DB::connection('mysql')->table('permissions')->get();

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm->name, 'guard_name' => $perm->guard_name]
            );
        }

        $this->command->info('    ✅ ' . $permissions->count() . ' permission eklendi');
    }

    /**
     * 4. Rolleri oluştur
     */
    protected function seedRoles(): void
    {
        $this->command->info('  📝 Roller oluşturuluyor...');

        $roles = [
            'root' => 'Süper Yönetici - Tüm yetkiler',
            'admin' => 'Yönetici - Sistem yönetimi',
            'editor' => 'Editör - İçerik yönetimi',
            'user' => 'Kullanıcı - Temel yetkiler',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        $this->command->info('    ✅ 4 rol oluşturuldu');
    }

    /**
     * 5. Rollere permissions ata
     */
    protected function assignPermissionsToRoles(): void
    {
        $this->command->info('  📝 Rol-Permission ilişkileri oluşturuluyor...');

        $rootRole = Role::where('name', 'root')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $editorRole = Role::where('name', 'editor')->first();
        $userRole = Role::where('name', 'user')->first();

        $allPermissions = Permission::all();

        // Root: Tüm yetkiler
        if ($rootRole) {
            $rootRole->syncPermissions($allPermissions);
        }

        // Admin: view, create, update yetkiler (delete hariç)
        if ($adminRole) {
            $adminPermissions = $allPermissions->filter(function ($perm) {
                return !str_contains($perm->name, '.delete');
            });
            $adminRole->syncPermissions($adminPermissions);
        }

        // Editor: Sadece view, create, update (delete hariç)
        if ($editorRole) {
            $editorPermissions = $allPermissions->filter(function ($perm) {
                return str_contains($perm->name, '.view')
                    || str_contains($perm->name, '.create')
                    || str_contains($perm->name, '.update');
            });
            $editorRole->syncPermissions($editorPermissions);
        }

        // User: Sadece view
        if ($userRole) {
            $userPermissions = $allPermissions->filter(function ($perm) {
                return str_contains($perm->name, '.view');
            });
            $userRole->syncPermissions($userPermissions);
        }

        $this->command->info('    ✅ Rol yetkileri atandı');
    }

    /**
     * 6. Kullanıcıları oluştur
     */
    protected function seedUsers(): void
    {
        $this->command->info('  📝 Kullanıcılar oluşturuluyor...');

        // Nurullah
        $nurullah = User::firstOrCreate(
            ['email' => 'nurullah@nurullah.net'],
            [
                'name' => 'Nurullah',
                'password' => Hash::make('g0nulcelen'),
                'email_verified_at' => now(),
            ]
        );
        $nurullah->assignRole('root');

        // Info
        $info = User::firstOrCreate(
            ['email' => 'info@turkbilisim.com.tr'],
            [
                'name' => 'Türk Bilişim',
                'password' => Hash::make('gonu1celen'),
                'email_verified_at' => now(),
            ]
        );
        $info->assignRole('admin');

        $this->command->info('    ✅ 2 kullanıcı oluşturuldu');
    }

    /**
     * 7. Ana menüyü kopyala
     */
    protected function seedMenu(): void
    {
        $this->command->info('  📝 Menü kopyalanıyor...');

        // Central DB'den header menüsünü al
        $menu = DB::connection('mysql')->table('menus')
            ->where('location', 'header')
            ->first();

        if (!$menu) {
            $this->command->warn('    ⚠️  Header menüsü bulunamadı');
            return;
        }

        // Menüyü tenant DB'ye ekle
        $newMenuId = DB::table('menus')->insertGetId([
            'name' => $menu->name,
            'slug' => $menu->slug,
            'location' => $menu->location,
            'is_default' => $menu->is_default,
            'is_active' => $menu->is_active,
            'settings' => $menu->settings,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Menu items'ları kopyala
        $menuItems = DB::connection('mysql')->table('menu_items')
            ->where('menu_id', $menu->menu_id)
            ->get();

        foreach ($menuItems as $item) {
            DB::table('menu_items')->insert([
                'menu_id' => $newMenuId,
                'parent_id' => $item->parent_id,
                'title' => $item->title,
                'url_type' => $item->url_type ?? 'internal',
                'url_data' => $item->url_data ?? null,
                'target' => $item->target ?? '_self',
                'icon' => $item->icon ?? null,
                'visibility' => $item->visibility ?? 'public',
                'sort_order' => $item->sort_order ?? 0,
                'is_active' => $item->is_active ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('    ✅ Menü ve ' . $menuItems->count() . ' menu item kopyalandı');
    }

    /**
     * 8. Anasayfayı oluştur
     */
    protected function seedHomePage(): void
    {
        $this->command->info('  📝 Anasayfa oluşturuluyor...');

        DB::table('pages')->insert([
            'slug' => json_encode(['tr' => 'anasayfa']),
            'title' => json_encode(['tr' => 'Ana Sayfa']),
            'body' => json_encode(['tr' => '<h1>Hoş Geldiniz</h1><p>Bu sitenin ana sayfasıdır.</p>']),
            'is_homepage' => 1,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('    ✅ Anasayfa oluşturuldu');
    }

    /**
     * 9. AI Bilgi Bankası
     */
    protected function seedAIKnowledgeBase(): void
    {
        $this->command->info('  📝 AI Bilgi Bankası (İxtif - 30 soru-cevap) oluşturuluyor...');

        $this->call(\Modules\SettingManagement\Database\Seeders\AIKnowledgeBaseSeeder::class);

        $this->command->info('    ✅ AI Bilgi Bankası oluşturuldu');
    }
}
