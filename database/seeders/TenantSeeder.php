<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\TenantHelpers;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Bu seeder sadece central veritabanında çalışmalı
        if (!TenantHelpers::isCentral()) {
            $this->command->info('TenantSeeder sadece central veritabanında çalışır.');
            return;
        }
        // Eski tenant dizinlerini temizle
        $this->cleanupOldTenantDirectories();

        // Mevcut tenant veritabanlarını temizle
        $databases = DB::select("SHOW DATABASES WHERE `Database` LIKE 'tenant_%'");
        foreach($databases as $database) {
            $dbName = current((array)$database);
            DB::statement("DROP DATABASE IF EXISTS `$dbName`");
        }

        // Mevcut domain ve tenant'ları temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Domain::query()->delete();
        Tenant::query()->delete();
        User::query()->delete(); // Central tenant'ta mevcut kullanıcıları temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // 1. Önce Central tenant'ı ekle
        // DOĞRUDAN SQL komutu ile ekle, model olaylarını tetiklemeden
        DB::table('tenants')->insert([
            'title' => 'Laravel',
            'fullname' => 'Nurullah Okatan',
            'email' => 'nurullah@nurullah.net',
            'phone' => '+90 533 123 45 67',
            'tenancy_db_name' => 'laravel',
            'is_active' => true,
            'central' => true,
            'theme_id' => 1,
            'tenant_default_locale' => 'tr',
            'data' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Son eklenen tenant ID'sini al
        $centralTenantId = DB::table('tenants')->where('title', 'Laravel')->value('id');

        // Domain ekleyin - Environment'a göre
        DB::table('domains')->insert([
            'domain' => env('APP_DOMAIN', 'laravel.test'),
            'tenant_id' => $centralTenantId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Central tenant için standart kullanıcılar oluştur
        // Nurullah kullanıcısı
        User::create([
            'name' => 'Nurullah Okatan',
            'email' => 'nurullah@nurullah.net',
            'password' => Hash::make('test'),
            'email_verified_at' => now(),
        ]);
        
        // Türk Bilişim kullanıcısı
        User::create([
            'name' => 'Türk Bilişim',
            'email' => 'info@turkbilisim.com.tr',
            'password' => Hash::make('test'),
            'email_verified_at' => now(),
        ]);
        
        // Laravel Admin kullanıcısı
        User::create([
            'name' => 'Laravel Admin',
            'email' => 'laravel@test',
            'password' => Hash::make('test'),
            'email_verified_at' => now(),
        ]);
        
        // Central için 5 test kullanıcısı ekle
        $testUsers = [
            ['name' => 'Test Kullanıcı 1', 'email' => 'test1@test.com'],
            ['name' => 'Test Kullanıcı 2', 'email' => 'test2@test.com'],
            ['name' => 'Test Kullanıcı 3', 'email' => 'test3@test.com'],
            ['name' => 'Test Kullanıcı 4', 'email' => 'test4@test.com'],
            ['name' => 'Test Kullanıcı 5', 'email' => 'test5@test.com'],
        ];

        foreach($testUsers as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('test'),
                'email_verified_at' => now(),
            ]);
        }

        // 2. Normal tenant'ları oluştur (central olmayan)
        $normalTenants = [
            [
                'title' => 'Kırmızı',
                'fullname' => 'Ahmet Kırmızı',
                'email' => 'ahmet@kirmizi.test',
                'phone' => '+90 532 111 11 11',
                'domain' => 'a.test',
                'user_email' => 'a@test',
                'db_name' => 'tenant_a',
                'default_locale' => 'en',
            ],
            [
                'title' => 'Sarı',
                'fullname' => 'Fatma Sarı',
                'email' => 'fatma@sari.test',
                'phone' => '+90 533 222 22 22',
                'domain' => 'b.test',
                'user_email' => 'b@test',
                'db_name' => 'tenant_b',
                'default_locale' => 'ar',
            ],
            [
                'title' => 'Mavi',
                'fullname' => 'Mehmet Mavi',
                'email' => 'mehmet@mavi.test',
                'phone' => '+90 534 333 33 33',
                'domain' => 'c.test',
                'user_email' => 'c@test',
                'db_name' => 'tenant_c',
                'default_locale' => 'en',
            ]
        ];

        // Normal tenant'ları oluştur
        foreach ($normalTenants as $config) {
            // Tenant oluştur
            $tenant = Tenant::create([
                'title' => $config['title'],
                'fullname' => $config['fullname'],
                'email' => $config['email'],
                'phone' => $config['phone'],
                'tenancy_db_name' => $config['db_name'],
                'is_active' => true,
                'central' => false,
                'theme_id' => 1,
                'tenant_default_locale' => $config['default_locale'],
                'data' => [],
            ]);

            // Domain bağla
            $tenant->domains()->create(['domain' => $config['domain']]);

            // Tenant dizinlerini hazırla
            $this->prepareTenantDirectories($tenant->id);
            
            // Tenant'a geç ve kullanıcı oluştur
            $tenant->run(function () use ($config) {
                // Admin kullanıcısı
                User::create([
                    'name' => $config['title'] . ' Yönetici',
                    'email' => $config['user_email'],
                    'password' => Hash::make('test'),
                    'email_verified_at' => now(),
                ]);

                // Nurullah kullanıcısı
                User::create([
                    'name' => 'Nurullah Okatan',
                    'email' => 'nurullah@nurullah.net',
                    'password' => Hash::make('test'),
                    'email_verified_at' => now(),
                ]);

                // Türk Bilişim kullanıcısı
                User::create([
                    'name' => 'Türk Bilişim',
                    'email' => 'info@turkbilisim.com.tr',
                    'password' => Hash::make('test'),
                    'email_verified_at' => now(),
                ]);

                // 3 test kullanıcısı ekle
                $tenantTestUsers = [
                    ['name' => $config['title'] . ' Test 1', 'email' => 'test1@' . $config['domain']],
                    ['name' => $config['title'] . ' Test 2', 'email' => 'test2@' . $config['domain']],
                    ['name' => $config['title'] . ' Test 3', 'email' => 'test3@' . $config['domain']],
                ];

                foreach($tenantTestUsers as $user) {
                    User::create([
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'password' => Hash::make('test'),
                        'email_verified_at' => now(),
                    ]);
                }
            });
        }

        // Tenant dizinlerini temizle (sadece güvence için)
        try {
            Artisan::call('tenants:clean');
        } catch (\Exception $e) {
            // Olası hatalar için sessizce ilerle
        }
    }

    /**
     * Eski tenant dizinlerini temizle
     */
    protected function cleanupOldTenantDirectories()
    {
        // Storage path'indeki tüm tenant dizinlerini bul ve sil
        $storagePath = storage_path();
        $tenantDirectories = glob($storagePath . '/tenant*', GLOB_ONLYDIR);

        foreach ($tenantDirectories as $directory) {
            try {
                // Alt dizinleri önce temizle (framework, app, logs, sessions)
                $subDirs = [
                    $directory . '/framework',
                    $directory . '/app',
                    $directory . '/logs',
                    $directory . '/sessions',
                ];
                
                foreach ($subDirs as $subDir) {
                    if (File::isDirectory($subDir)) {
                        File::deleteDirectory($subDir);
                    }
                }
                
                // Ana dizini temizle
                File::deleteDirectory($directory);
            } catch (\Exception $e) {
                \Log::warning("Tenant dizini silinemedi: " . $directory . " - " . $e->getMessage());
            }
        }

        // Public storage'daki tenant dizinlerini de temizle
        $publicTenantPath = public_path('storage/tenant*');
        $publicTenantDirectories = glob($publicTenantPath, GLOB_ONLYDIR);

        foreach ($publicTenantDirectories as $directory) {
            try {
                File::deleteDirectory($directory);
            } catch (\Exception $e) {
                \Log::warning("Public tenant dizini silinemedi: " . $directory . " - " . $e->getMessage());
            }
        }
    }

    /**
     * Tenant için gerekli dizinleri hazırla
     * 
     * @param int $tenantId
     */
    protected function prepareTenantDirectories($tenantId)
    {
        // Central tenant için klasör oluşturma
        if ($tenantId == DB::table('tenants')->where('central', true)->value('id')) {
            return;
        }
        
        // Önce tenant dizini varsa temizle
        $tenantPath = storage_path("tenant{$tenantId}");
        if (File::isDirectory($tenantPath)) {
            File::deleteDirectory($tenantPath);
        }
        
        // Framework cache dizini
        $frameworkCachePath = storage_path("tenant{$tenantId}/framework/cache");
        File::ensureDirectoryExists($frameworkCachePath, 0775, true);
        
        // Framework diğer alt dizinleri
        $frameworkPaths = [
            storage_path("tenant{$tenantId}/framework/sessions"),
            storage_path("tenant{$tenantId}/framework/views"),
            storage_path("tenant{$tenantId}/framework/testing"),
        ];
        
        foreach ($frameworkPaths as $path) {
            File::ensureDirectoryExists($path, 0775, true);
        }

        // Diğer gerekli dizinler
        $paths = [
            storage_path("tenant{$tenantId}/app"),
            storage_path("tenant{$tenantId}/logs"),
            storage_path("tenant{$tenantId}/sessions"),
        ];

        foreach ($paths as $path) {
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0775, true, true);
            }
        }

        // Public storage dizini
        $publicStoragePath = public_path("storage/tenant{$tenantId}");
        if (File::isDirectory($publicStoragePath)) {
            File::deleteDirectory($publicStoragePath);
        }
        if (!File::isDirectory($publicStoragePath)) {
            File::makeDirectory($publicStoragePath, 0775, true, true);
        }
    }
}