<?php

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;

class QueueTenancyBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant)
    {
        $tenantKey = $tenant->getTenantKey();
        
        // Database queue için tenant'a özel table
        if (config('queue.default') === 'database') {
            Config::set([
                'queue.connections.database.table' => 'tenant_' . $tenantKey . '_jobs',
                'queue.connections.database.connection' => 'tenant',
            ]);
        }
        
        // Redis queue için tenant prefix
        if (config('queue.default') === 'redis') {
            Config::set([
                'queue.connections.redis.queue' => 'tenant_' . $tenantKey . '_default',
                'queue.connections.redis.connection' => 'default',
                'database.redis.options.prefix' => 'tenant_' . $tenantKey . ':queue:',
            ]);
        }
        
        // Beanstalkd queue için tenant tube
        if (config('queue.default') === 'beanstalkd') {
            Config::set([
                'queue.connections.beanstalkd.queue' => 'tenant_' . $tenantKey . '_default',
            ]);
        }
        
        // SQS queue için tenant prefix
        if (config('queue.default') === 'sqs') {
            $queueUrl = config('queue.connections.sqs.queue');
            Config::set([
                'queue.connections.sqs.queue' => $queueUrl . '_tenant_' . $tenantKey,
            ]);
        }
        
        // Queue konfigürasyonu güncellendi
    }

    public function revert()
    {
        // Orijinal queue ayarlarına geri dön
        Config::set([
            'queue.connections.database.table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue.connections.database.connection' => env('DB_QUEUE_CONNECTION'),
            'queue.connections.redis.queue' => env('REDIS_QUEUE', 'default'),
            'queue.connections.beanstalkd.queue' => env('BEANSTALKD_QUEUE', 'default'),
            'queue.connections.sqs.queue' => env('SQS_QUEUE'),
        ]);
        
        // Redis prefix'i temizle
        if (config('queue.default') === 'redis') {
            Config::set('database.redis.options.prefix', env('REDIS_PREFIX', ''));
        }
        
        // Queue konfigürasyonu güncellendi
    }
}