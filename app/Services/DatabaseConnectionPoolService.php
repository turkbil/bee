<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DatabaseConnectionPoolService
{
    protected $pools = [];
    protected $activeConnections = [];
    protected $poolStats = [];
    
    public function __construct()
    {
        $this->initializePools();
        $this->startMonitoring();
    }
    
    /**
     * Connection pool'larını başlat
     */
    public function initializePools()
    {
        $poolConfig = config('database_pool.mysql_pool');
        $tenantSettings = config('database_pool.tenant_pool_settings');
        
        // Ana pool'u oluştur
        $this->pools['central'] = [
            'max_connections' => $poolConfig['max_connections'],
            'active_connections' => 0,
            'idle_connections' => 0,
            'created_at' => now(),
        ];
        
        // Database connection pool initialized
    }
    
    /**
     * Tenant için connection al
     */
    public function getTenantConnection($tenantKey)
    {
        $maxPerTenant = config('database_pool.tenant_pool_settings.max_per_tenant', 5);
        $strategy = config('database_pool.tenant_pool_settings.sharing_strategy', 'round_robin');
        
        // Tenant pool'unu kontrol et
        if (!isset($this->pools[$tenantKey])) {
            $this->createTenantPool($tenantKey);
        }
        
        $pool = $this->pools[$tenantKey];
        
        // Max connection limit kontrolü
        if ($pool['active_connections'] >= $maxPerTenant) {
            return $this->getSharedConnection($strategy);
        }
        
        // Yeni connection oluştur
        $connectionName = "tenant_{$tenantKey}_" . uniqid();
        
        try {
            $this->createTenantDatabaseConnection($tenantKey, $connectionName);
            
            $this->pools[$tenantKey]['active_connections']++;
            $this->activeConnections[$connectionName] = [
                'tenant' => $tenantKey,
                'created_at' => now(),
                'last_used' => now(),
            ];
            
            // Log::debug("New tenant connection created", [
            //     'tenant' => $tenantKey,
            //     'connection' => $connectionName,
            //     'active_count' => $this->pools[$tenantKey]['active_connections'],
            // ]);
            
            return $connectionName;
            
        } catch (\Exception $e) {
            Log::error("Failed to create tenant connection", [
                'tenant' => $tenantKey,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Tenant pool'u oluştur
     */
    protected function createTenantPool($tenantKey)
    {
        $this->pools[$tenantKey] = [
            'max_connections' => config('database_pool.tenant_pool_settings.max_per_tenant', 5),
            'active_connections' => 0,
            'idle_connections' => 0,
            'created_at' => now(),
            'last_activity' => now(),
        ];
    }
    
    /**
     * Tenant database connection'ı oluştur
     */
    protected function createTenantDatabaseConnection($tenantKey, $connectionName)
    {
        // Tenant'ın gerçek database adını al
        $tenant = \App\Models\Tenant::find($tenantKey);
        if (!$tenant) {
            throw new \Exception("Tenant not found: {$tenantKey}");
        }
        
        $databaseName = $tenant->tenancy_db_name ?? "tenant_{$tenantKey}";
        
        $baseConfig = config('database.connections.mysql');
        $tenantConfig = array_merge($baseConfig, [
            'database' => $databaseName,
            'options' => array_merge($baseConfig['options'] ?? [], [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ]),
        ]);
        
        Config::set("database.connections.{$connectionName}", $tenantConfig);
        
        // Connection test et
        try {
            DB::connection($connectionName)->getPdo();
        } catch (\Exception $e) {
            Log::error("Failed to create tenant connection", [
                'tenant' => $tenantKey,
                'database' => $databaseName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Shared connection al (connection limit aşıldığında)
     */
    protected function getSharedConnection($strategy)
    {
        switch ($strategy) {
            case 'round_robin':
                return $this->getRoundRobinConnection();
            case 'least_connections':
                return $this->getLeastConnectionsConnection();
            default:
                return $this->getRoundRobinConnection();
        }
    }
    
    /**
     * Round robin stratejisi ile connection al
     */
    protected function getRoundRobinConnection()
    {
        static $lastUsed = 0;
        
        $connections = array_keys($this->activeConnections);
        if (empty($connections)) {
            throw new \Exception('No active connections available for sharing');
        }
        
        $connectionName = $connections[$lastUsed % count($connections)];
        $lastUsed++;
        
        // Son kullanım zamanını güncelle
        $this->activeConnections[$connectionName]['last_used'] = now();
        
        return $connectionName;
    }
    
    /**
     * En az kullanılan connection'ı al
     */
    protected function getLeastConnectionsConnection()
    {
        $connections = $this->activeConnections;
        if (empty($connections)) {
            throw new \Exception('No active connections available for sharing');
        }
        
        // En eski kullanılan connection'ı bul
        $oldestConnection = null;
        $oldestTime = now();
        
        foreach ($connections as $name => $info) {
            if ($info['last_used'] < $oldestTime) {
                $oldestTime = $info['last_used'];
                $oldestConnection = $name;
            }
        }
        
        // Son kullanım zamanını güncelle
        $this->activeConnections[$oldestConnection]['last_used'] = now();
        
        return $oldestConnection;
    }
    
    /**
     * Connection'ı serbest bırak
     */
    public function releaseConnection($connectionName)
    {
        if (isset($this->activeConnections[$connectionName])) {
            $tenantKey = $this->activeConnections[$connectionName]['tenant'];
            
            // Idle timeout kontrolü
            $idleTimeout = config('database_pool.mysql_pool.idle_timeout', 300);
            $lastUsed = $this->activeConnections[$connectionName]['last_used'];
            
            if (now()->diffInSeconds($lastUsed) > $idleTimeout) {
                $this->closeConnection($connectionName);
            } else {
                // Connection'ı idle pool'a taşı
                $this->pools[$tenantKey]['active_connections']--;
                $this->pools[$tenantKey]['idle_connections']++;
            }
        }
    }
    
    /**
     * Connection'ı kapat
     */
    public function closeConnection($connectionName)
    {
        if (isset($this->activeConnections[$connectionName])) {
            $tenantKey = $this->activeConnections[$connectionName]['tenant'];
            
            try {
                DB::disconnect($connectionName);
                Config::forget("database.connections.{$connectionName}");
                
                $this->pools[$tenantKey]['active_connections']--;
                unset($this->activeConnections[$connectionName]);
                
                Log::debug("Connection closed", [
                    'connection' => $connectionName,
                    'tenant' => $tenantKey,
                ]);
                
            } catch (\Exception $e) {
                Log::error("Failed to close connection", [
                    'connection' => $connectionName,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
    
    /**
     * Pool monitoring'i başlat
     */
    protected function startMonitoring()
    {
        if (!config('database_pool.tenant_pool_settings.monitor_enabled', true)) {
            return;
        }
        
        // Cache'te pool stats'ları kaydet
        $this->updatePoolStats();
    }
    
    /**
     * Pool istatistiklerini güncelle
     */
    public function updatePoolStats()
    {
        try {
            $totalActive = array_sum(array_column($this->pools, 'active_connections'));
            $totalIdle = array_sum(array_column($this->pools, 'idle_connections'));
            
            $stats = [
            'total_pools' => count($this->pools),
            'total_active_connections' => $totalActive,
            'total_idle_connections' => $totalIdle,
            'pools' => $this->pools,
            'updated_at' => now(),
        ];
        
            Cache::put('database_pool_stats', $stats, 300); // 5 dakika cache
            
            $this->poolStats = $stats;
        } catch (\Exception $e) {
            // Cache bağlantısı yoksa sessizce devam et
            Log::debug('Could not update pool stats: ' . $e->getMessage());
        }
    }
    
    /**
     * Pool istatistiklerini al
     */
    public function getPoolStats()
    {
        return Cache::get('database_pool_stats', $this->poolStats);
    }
    
    /**
     * Idle connection'ları temizle
     */
    public function cleanupIdleConnections()
    {
        $idleTimeout = config('database_pool.mysql_pool.idle_timeout', 300);
        $now = now();
        
        foreach ($this->activeConnections as $connectionName => $info) {
            if ($now->diffInSeconds($info['last_used']) > $idleTimeout) {
                $this->closeConnection($connectionName);
            }
        }
        
        $this->updatePoolStats();
    }
}