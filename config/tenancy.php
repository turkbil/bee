<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

return [
    'tenant_model' => \App\Models\Tenant::class,
    'id_generator' => null, // UUID generator kaldırıldı
    
    'domain_model' => Domain::class,
    
    'central_domains' => [
        'laravel.test',
    ],
    
    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        \App\Tenancy\RedisTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        \App\Tenancy\QueueTenancyBootstrapper::class,
        \App\Tenancy\SessionTenancyBootstrapper::class,
    ],
    
    'database' => [
        'central_connection' => env('DB_CONNECTION', 'mysql'),
        'template_tenant_connection' => null,
        'prefix' => 'tenant',
        'suffix' => '',
        'tenant_connection_driver' => 'mysql', // Tenant bağlantısı için varsayılan driver mysql olarak ayarlandı
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
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