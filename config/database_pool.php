<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Connection Pooling Configuration
    |--------------------------------------------------------------------------
    |
    | 500 tenant için MySQL connection limit sorununu önlemek için
    | connection pooling ayarları
    |
    */

    'mysql_pool' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            // Connection pooling için önemli ayarlar
            PDO::ATTR_PERSISTENT => true, // Persistent connections
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'",
        ]) : [],
        
        // Connection pool settings
        'pool_size' => env('DB_POOL_SIZE', 50), // Max connections per pool
        'max_connections' => env('DB_MAX_CONNECTIONS', 200), // Total max connections
        'min_connections' => env('DB_MIN_CONNECTIONS', 10), // Min connections to keep open
        'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 30),
        'idle_timeout' => env('DB_IDLE_TIMEOUT', 300), // 5 dakika idle timeout
        'validation_query' => 'SELECT 1',
        'test_on_borrow' => true,
        'test_while_idle' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Database Pool Configuration
    |--------------------------------------------------------------------------
    |
    | Her tenant için dinamik connection pool ayarları
    |
    */
    
    'tenant_pool_settings' => [
        // Tenant başına max connection
        'max_per_tenant' => env('DB_MAX_PER_TENANT', 5),
        
        // Connection sharing stratejisi
        'sharing_strategy' => env('DB_SHARING_STRATEGY', 'round_robin'), // round_robin, least_connections
        
        // Connection reuse timeout
        'reuse_timeout' => env('DB_REUSE_TIMEOUT', 60),
        
        // Pool monitoring
        'monitor_enabled' => env('DB_POOL_MONITOR', true),
        'monitor_interval' => env('DB_POOL_MONITOR_INTERVAL', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | ProxySQL Integration (Optional)
    |--------------------------------------------------------------------------
    |
    | ProxySQL kullanıyorsak bu ayarları aktif et
    |
    */
    
    'proxysql' => [
        'enabled' => env('PROXYSQL_ENABLED', false),
        'host' => env('PROXYSQL_HOST', '127.0.0.1'),
        'port' => env('PROXYSQL_PORT', 6033),
        'admin_host' => env('PROXYSQL_ADMIN_HOST', '127.0.0.1'),
        'admin_port' => env('PROXYSQL_ADMIN_PORT', 6032),
        'admin_user' => env('PROXYSQL_ADMIN_USER', 'admin'),
        'admin_password' => env('PROXYSQL_ADMIN_PASSWORD', 'admin'),
        
        // Query routing rules
        'read_hostgroup' => env('PROXYSQL_READ_HOSTGROUP', 0),
        'write_hostgroup' => env('PROXYSQL_WRITE_HOSTGROUP', 1),
        
        // Connection multiplexing
        'multiplex' => env('PROXYSQL_MULTIPLEX', true),
        'max_connections' => env('PROXYSQL_MAX_CONNECTIONS', 1000),
    ],
];