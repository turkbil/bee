<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

return [
    'tenant_model' => \App\Models\Tenant::class,
    'id_generator' => null, // UUID generator kaldırıldı
    
    'domain_model' => Domain::class,
    
    'central_domains' => [
        env('APP_DOMAIN', 'laravel.test'),
        'tuufi.com',
        'www.tuufi.com',
    ],

    /**
     * Parent domain for tenant aliases
     * Tüm tenant domain'leri bu parent domain'e alias olarak eklenir
     * Farklı sunucuya yüklenirse .env'den değiştirilebilir
     */
    'parent_domain' => env('TENANT_PARENT_DOMAIN', 'tuufi.com'),
    
    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        \App\Tenancy\RedisTenancyBootstrapper::class,
        \App\Tenancy\StorageTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class, // ✅ Vendor bootstrapper - job'larda tenant init için
        \App\Tenancy\QueueTenancyBootstrapper::class, // Custom queue config
        \App\Tenancy\SessionTenancyBootstrapper::class,
        \App\Tenancy\MailConfigBootstrapper::class, // ✅ Mail config'i Settings'ten yükle
    ],
    
    'database' => [
        'central_connection' => env('DB_CONNECTION', 'mysql'),
        'template_tenant_connection' => null,
        'prefix' => 'tenant',
        'suffix' => '',
        'tenant_connection_driver' => 'mysql', // Tenant bağlantısı için varsayılan driver mysql olarak ayarlandı
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class, // ✅ Back to default - CREATE permission granted
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
        ],
    ],
    
    'redis' => [
        'prefix_base' => 'tenant',
        'prefixed_connections' => [
            'default',
            'cache',
        ],
    ],
    
    'cache' => [
        'tag_base' => 'tenant',
        'store' => 'redis', // Cache için redis store kullanılacak
    ],
    
    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
            's3',
        ],
        'root_override' => [
            'local' => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],
        'suffix_storage_path' => true,
        'asset_helper_tenancy' => false,
    ],
    
    'asset_url_generator' => null,
    'create_asset_url' => false,
    
    'routes' => true,
    
    'migration_parameters' => [
        '--force' => true,
        '--path' => [
            'database/migrations/tenant',
            'Modules/*/database/migrations/tenant'
        ],
    ],
    
    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder',
        '--force' => true,
    ],
];