<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Stancl\Tenancy\Database\Models\Domain;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
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
        
        // Ana sistemde Nurullah kullanıcısı oluştur
        $nurullahUser = User::create([
            'name' => 'Nurullah Okatan',
            'email' => 'nurullah@nurullah.net',
            'password' => Hash::make('nurullah'),
            'email_verified_at' => now(),
        ]);

        // 1. Önce Central tenant'ı ekle
        // DOĞRUDAN SQL komutu ile ekle, model olaylarını tetiklemeden
        DB::table('tenants')->insert([
            'title' => 'Laravel',
            'tenancy_db_name' => 'laravel',
            'is_active' => true,
            'central' => true,
            'data' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Son eklenen tenant ID'sini al
        $centralTenantId = DB::table('tenants')->where('title', 'Laravel')->value('id');

        // Domain ekleyin
        DB::table('domains')->insert([
            'domain' => 'laravel.test',
            'tenant_id' => $centralTenantId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Central tenant için Admin kullanıcısı oluştur (Nurullah kullanıcısı zaten var)
        User::create([
            'name' => 'Laravel Admin',
            'email' => 'admin@laravel.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // 2. Normal tenant'ları oluştur (central olmayan)
        $normalTenants = [
            [
                'title' => 'Kırmızı',
                'domain' => 'a.test',
                'email' => 'a@test',
                'db_name' => 'tenant_a',
            ],
            [
                'title' => 'Sarı',
                'domain' => 'b.test',
                'email' => 'b@test',
                'db_name' => 'tenant_b',
            ],
            [
                'title' => 'Mavi',
                'domain' => 'c.test',
                'email' => 'c@test',
                'db_name' => 'tenant_c',
            ]
        ];

        // Faker için
        $faker = Faker::create('tr_TR');

        // Normal tenant'ları oluştur
        foreach ($normalTenants as $config) {
            // Tenant oluştur
            $tenant = Tenant::create([
                'title' => $config['title'],
                'tenancy_db_name' => $config['db_name'],
                'is_active' => true,
                'central' => false,
                'data' => [],
            ]);

            // Domain bağla
            $tenant->domains()->create(['domain' => $config['domain']]);

            // Tenant dizinlerini hazırla
            $this->prepareTenantDirectories($tenant->id);
            
            // Tenant'a geç ve kullanıcı oluştur
            $tenant->run(function () use ($config, $faker) {
                // Admin kullanıcısı
                User::create([
                    'name' => $config['title'] . ' Yönetici',
                    'email' => $config['email'],
                    'password' => Hash::make('test'),
                    'email_verified_at' => now(),
                ]);
                
                // Nurullah kullanıcısı
                User::create([
                    'name' => 'Nurullah Okatan',
                    'email' => 'nurullah@nurullah.net',
                    'password' => Hash::make('nurullah'),
                    'email_verified_at' => now(),
                ]);

                // 10 rastgele kullanıcı ekle
                foreach(range(1, 10) as $index) {
                    $firstName = $faker->firstName;
                    $lastName = $faker->lastName;
                    
                    User::create([
                        'name' => $firstName . ' ' . $lastName,
                        'email' => strtolower($faker->unique()->userName) . '@' . $faker->freeEmailDomain,
                        'password' => Hash::make('password'),
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
            File::ensureDirectoryExists($path, 0775, true);
        }

        // Public storage dizini
        $publicStoragePath = public_path("storage/tenant{$tenantId}");
        if (File::isDirectory($publicStoragePath)) {
            File::deleteDirectory($publicStoragePath);
        }
        File::ensureDirectoryExists($publicStoragePath, 0775, true);
    }
}