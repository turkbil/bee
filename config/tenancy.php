<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;

return [
    'tenant_model' => \App\Models\Tenant::class,
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'domain_model' => Domain::class,

    /**
     * Central app domains list (non-tenant domains)
     */
    'central_domains' => [
        '127.0.0.1',
        'localhost',
    ],

    /**
     * Bootstrappers: These make Laravel features tenant-aware.
     */
    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],

    /**
     * Database configuration
     */
    'database' => [
        'central_connection' => env('DB_CONNECTION', 'mysql'),

        /**
         * Disable tenant-specific database creation as we use a single database.
         */
        'managers' => [
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\NullDatabaseManager::class,
        ],
    ],

    /**
     * Cache configuration
     */
    'cache' => [
        'tag_base' => 'tenant', // Tags are used to separate tenants' cache data
        'store' => env('TENANCY_CACHE_DRIVER', env('CACHE_DRIVER', 'redis')),
    ],

    /**
     * Filesystem configuration
     */
    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
        ],
        'asset_helper_tenancy' => false,
    ],

    /**
     * Redis tenancy configuration
     */
    'redis' => [
        'prefix_base' => 'tenant', // Redis önbellek verilerini tenant bazında ayırır
        'connection' => 'default',
    ],

    /**
     * Features are additional functionality.
     */
    'features' => [
        Stancl\Tenancy\Features\UniversalRoutes::class, // Makes routes shared across tenants
        Stancl\Tenancy\Features\TenantConfig::class, // Allows tenant-specific configuration
    ],

    /**
     * Migration parameters for tenants
     */
    'migration_parameters' => [
        '--force' => true,
        '--path' => [database_path('migrations/tenant')],
        '--realpath' => true,
    ],

    /**
     * Seeder parameters for tenants
     */
    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder',
    ],
];
