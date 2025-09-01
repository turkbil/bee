<?php

namespace App\Services;

use Illuminate\Support\Facades\Request;

class TenantQueueService
{
    /**
     * 🏢 CENTRAL TENANT DOMAINS - These are central domains
     */
    private const CENTRAL_DOMAINS = [
        'laravel.test',
        'localhost',
        '127.0.0.1'
    ];

    /**
     * 🔍 Check if current request is from central tenant
     */
    public static function isCentralTenant(): bool
    {
        $host = request()->getHost() ?? 'localhost';
        
        return in_array($host, self::CENTRAL_DOMAINS);
    }

    /**
     * 🎯 Get appropriate queue connection for current tenant
     */
    public static function getQueueConnection(): string
    {
        if (self::isCentralTenant()) {
            return 'central_isolated';
        }
        
        return 'tenant_isolated';
    }

    /**
     * 🗂️ Get appropriate queue name for current tenant
     */
    public static function getQueueName(string $operation = 'default'): string
    {
        if (self::isCentralTenant()) {
            return "central_{$operation}";
        }
        
        $host = request()->getHost() ?? 'default';
        $cleanHost = str_replace('.', '_', $host);
        
        return "tenant_{$cleanHost}_{$operation}";
    }

    /**
     * 🔧 Get Redis prefix for current tenant
     */
    public static function getRedisPrefix(): string
    {
        if (self::isCentralTenant()) {
            return 'central_';
        }
        
        $host = request()->getHost() ?? 'default';
        $cleanHost = str_replace('.', '_', $host);
        
        return "tenant_{$cleanHost}_";
    }

    /**
     * 📊 Get tenant info for logging
     */
    public static function getTenantInfo(): array
    {
        $host = request()->getHost() ?? 'localhost';
        $isCentral = self::isCentralTenant();
        
        return [
            'host' => $host,
            'is_central' => $isCentral,
            'queue_connection' => self::getQueueConnection(),
            'queue_name' => self::getQueueName(),
            'redis_prefix' => self::getRedisPrefix(),
            'tenant_type' => $isCentral ? 'central' : 'regular'
        ];
    }
}