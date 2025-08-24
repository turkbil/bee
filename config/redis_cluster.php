<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redis Cluster Configuration for 500 Tenants
    |--------------------------------------------------------------------------
    |
    | 500 tenant için Redis clustering konfigürasyonu
    | Her tenant grubunu ayrı Redis cluster'da izole eder
    |
    */

    'clustering' => [
        'enabled' => env('REDIS_CLUSTER_ENABLED', false),
        'algorithm' => env('REDIS_CLUSTER_ALGORITHM', 'consistent_hashing'), // consistent_hashing, hash_tag
    ],

    /*
    |--------------------------------------------------------------------------
    | Cluster Nodes Configuration
    |--------------------------------------------------------------------------
    |
    | Her cluster node için konfigürasyon
    |
    */
    
    'nodes' => [
        'cluster1' => [
            'hosts' => [
                env('REDIS_CLUSTER1_HOST1', '127.0.0.1:7000'),
                env('REDIS_CLUSTER1_HOST2', '127.0.0.1:7001'),
                env('REDIS_CLUSTER1_HOST3', '127.0.0.1:7002'),
            ],
            'options' => [
                'cluster' => 'redis',
                'parameters' => [
                    'password' => env('REDIS_CLUSTER1_PASSWORD', null),
                    'database' => env('REDIS_CLUSTER1_DATABASE', 0),
                    'read_write_timeout' => 60,
                    'persistent' => true,
                ],
            ],
            'tenant_range' => [1, 125], // Tenant 1-125
        ],
        
        'cluster2' => [
            'hosts' => [
                env('REDIS_CLUSTER2_HOST1', '127.0.0.1:7003'),
                env('REDIS_CLUSTER2_HOST2', '127.0.0.1:7004'),
                env('REDIS_CLUSTER2_HOST3', '127.0.0.1:7005'),
            ],
            'options' => [
                'cluster' => 'redis',
                'parameters' => [
                    'password' => env('REDIS_CLUSTER2_PASSWORD', null),
                    'database' => env('REDIS_CLUSTER2_DATABASE', 0),
                    'read_write_timeout' => 60,
                    'persistent' => true,
                ],
            ],
            'tenant_range' => [126, 250], // Tenant 126-250
        ],
        
        'cluster3' => [
            'hosts' => [
                env('REDIS_CLUSTER3_HOST1', '127.0.0.1:7006'),
                env('REDIS_CLUSTER3_HOST2', '127.0.0.1:7007'),
                env('REDIS_CLUSTER3_HOST3', '127.0.0.1:7008'),
            ],
            'options' => [
                'cluster' => 'redis',
                'parameters' => [
                    'password' => env('REDIS_CLUSTER3_PASSWORD', null),
                    'database' => env('REDIS_CLUSTER3_DATABASE', 0),
                    'read_write_timeout' => 60,
                    'persistent' => true,
                ],
            ],
            'tenant_range' => [251, 375], // Tenant 251-375
        ],
        
        'cluster4' => [
            'hosts' => [
                env('REDIS_CLUSTER4_HOST1', '127.0.0.1:7009'),
                env('REDIS_CLUSTER4_HOST2', '127.0.0.1:7010'),
                env('REDIS_CLUSTER4_HOST3', '127.0.0.1:7011'),
            ],
            'options' => [
                'cluster' => 'redis',
                'parameters' => [
                    'password' => env('REDIS_CLUSTER4_PASSWORD', null),
                    'database' => env('REDIS_CLUSTER4_DATABASE', 0),
                    'read_write_timeout' => 60,
                    'persistent' => true,
                ],
            ],
            'tenant_range' => [376, 500], // Tenant 376-500
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant-Cluster Mapping Strategy
    |--------------------------------------------------------------------------
    |
    | Tenant'ları cluster'lara nasıl dağıtacağımız
    |
    */
    
    'tenant_mapping' => [
        'strategy' => env('REDIS_TENANT_MAPPING_STRATEGY', 'range'), // range, hash, custom
        'rebalance_enabled' => env('REDIS_REBALANCE_ENABLED', true),
        'rebalance_threshold' => env('REDIS_REBALANCE_THRESHOLD', 80), // %80 dolunca rebalance
    ],

    /*
    |--------------------------------------------------------------------------
    | High Availability Configuration
    |--------------------------------------------------------------------------
    |
    | Failover ve backup ayarları
    |
    */
    
    'high_availability' => [
        'sentinel_enabled' => env('REDIS_SENTINEL_ENABLED', false),
        'sentinel_hosts' => explode(',', env('REDIS_SENTINEL_HOSTS', '127.0.0.1:26379,127.0.0.1:26380,127.0.0.1:26381')),
        'master_name' => env('REDIS_MASTER_NAME', 'mymaster'),
        'failover_timeout' => env('REDIS_FAILOVER_TIMEOUT', 30),
        
        'backup' => [
            'enabled' => env('REDIS_BACKUP_ENABLED', true),
            'schedule' => env('REDIS_BACKUP_SCHEDULE', '0 2 * * *'), // Her gün saat 02:00
            'retention_days' => env('REDIS_BACKUP_RETENTION', 7),
            'storage_path' => env('REDIS_BACKUP_PATH', storage_path('redis-backups')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Performance
    |--------------------------------------------------------------------------
    |
    | İzleme ve performans ayarları
    |
    */
    
    'monitoring' => [
        'enabled' => env('REDIS_MONITORING_ENABLED', true),
        'slow_log_enabled' => env('REDIS_SLOW_LOG_ENABLED', true),
        'slow_log_threshold' => env('REDIS_SLOW_LOG_THRESHOLD', 10000), // microseconds
        'max_memory_policy' => env('REDIS_MAX_MEMORY_POLICY', 'allkeys-lru'),
        
        'metrics' => [
            'collect_interval' => env('REDIS_METRICS_INTERVAL', 60), // seconds
            'retention_period' => env('REDIS_METRICS_RETENTION', 86400), // 1 day
            'alert_thresholds' => [
                'memory_usage' => env('REDIS_ALERT_MEMORY_THRESHOLD', 85), // %
                'connection_usage' => env('REDIS_ALERT_CONNECTION_THRESHOLD', 80), // %
                'response_time' => env('REDIS_ALERT_RESPONSE_TIME', 100), // ms
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Pool Settings
    |--------------------------------------------------------------------------
    |
    | Redis connection pooling ayarları
    |
    */
    
    'connection_pool' => [
        'enabled' => env('REDIS_CONNECTION_POOL_ENABLED', true),
        'max_connections' => env('REDIS_POOL_MAX_CONNECTIONS', 100),
        'min_connections' => env('REDIS_POOL_MIN_CONNECTIONS', 10),
        'max_idle_time' => env('REDIS_POOL_MAX_IDLE_TIME', 300), // 5 minutes
        'validation_interval' => env('REDIS_POOL_VALIDATION_INTERVAL', 30), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Scaling Configuration
    |--------------------------------------------------------------------------
    |
    | Otomatik ölçeklendirme ayarları
    |
    */
    
    'auto_scaling' => [
        'enabled' => env('REDIS_AUTO_SCALING_ENABLED', false),
        'scale_up_threshold' => env('REDIS_SCALE_UP_THRESHOLD', 80), // %80 kullanım
        'scale_down_threshold' => env('REDIS_SCALE_DOWN_THRESHOLD', 30), // %30 kullanım
        'cooldown_period' => env('REDIS_SCALING_COOLDOWN', 300), // 5 minutes
        'max_nodes_per_cluster' => env('REDIS_MAX_NODES_PER_CLUSTER', 6),
        'min_nodes_per_cluster' => env('REDIS_MIN_NODES_PER_CLUSTER', 3),
    ],
];