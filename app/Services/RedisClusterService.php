<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Predis\Client as PredisClient;
use Predis\Connection\Aggregate\RedisCluster;

class RedisClusterService
{
    protected $clusters = [];
    protected $tenantClusterMapping = [];
    
    public function __construct()
    {
        $this->initializeClusters();
        $this->buildTenantMapping();
    }

    /**
     * Redis cluster'larını başlat
     */
    protected function initializeClusters()
    {
        if (!config('redis_cluster.clustering.enabled')) {
            Log::info('Redis clustering disabled, using single instance');
            return;
        }

        $nodes = config('redis_cluster.nodes');
        
        foreach ($nodes as $clusterName => $config) {
            try {
                $this->clusters[$clusterName] = new PredisClient(
                    $config['hosts'],
                    array_merge($config['options'], [
                        'cluster' => 'redis',
                        'parameters' => $config['options']['parameters'] ?? []
                    ])
                );
                
                // Test connection
                $this->clusters[$clusterName]->ping();
                
                Log::info("Redis cluster '{$clusterName}' initialized successfully", [
                    'hosts' => $config['hosts'],
                    'tenant_range' => $config['tenant_range'],
                ]);
                
            } catch (\Exception $e) {
                Log::error("Failed to initialize Redis cluster '{$clusterName}'", [
                    'error' => $e->getMessage(),
                    'hosts' => $config['hosts'],
                ]);
                
                throw $e;
            }
        }
    }

    /**
     * Tenant-cluster mapping'i oluştur
     */
    protected function buildTenantMapping()
    {
        $nodes = config('redis_cluster.nodes');
        $strategy = config('redis_cluster.tenant_mapping.strategy', 'range');
        
        foreach ($nodes as $clusterName => $config) {
            if (isset($config['tenant_range'])) {
                [$start, $end] = $config['tenant_range'];
                
                for ($tenantId = $start; $tenantId <= $end; $tenantId++) {
                    $this->tenantClusterMapping[$tenantId] = $clusterName;
                }
            }
        }
        
        Log::info('Redis tenant-cluster mapping built', [
            'strategy' => $strategy,
            'total_tenants' => count($this->tenantClusterMapping),
        ]);
    }

    /**
     * Tenant için doğru cluster'ı al
     */
    public function getClusterForTenant($tenantKey)
    {
        // Tenant key'den ID'yi çıkar (tenant_123 -> 123)
        $tenantId = is_numeric($tenantKey) ? $tenantKey : (int) str_replace('tenant_', '', $tenantKey);
        
        // Central tenant için özel davranış
        if ($tenantKey === 'central' || $tenantId === 0) {
            return $this->clusters['cluster1'] ?? $this->getDefaultCluster();
        }
        
        $clusterName = $this->tenantClusterMapping[$tenantId] ?? null;
        
        if (!$clusterName || !isset($this->clusters[$clusterName])) {
            // Fallback: hash-based distribution
            $clusterName = $this->getClusterByHash($tenantKey);
        }
        
        return $this->clusters[$clusterName] ?? $this->getDefaultCluster();
    }

    /**
     * Hash-based cluster selection (fallback)
     */
    protected function getClusterByHash($tenantKey)
    {
        $hash = crc32($tenantKey);
        $clusterNames = array_keys($this->clusters);
        $index = abs($hash) % count($clusterNames);
        
        return $clusterNames[$index];
    }

    /**
     * Varsayılan cluster'ı al
     */
    protected function getDefaultCluster()
    {
        if (!config('redis_cluster.clustering.enabled')) {
            return Redis::connection();
        }
        
        return reset($this->clusters) ?: Redis::connection();
    }

    /**
     * Tenant için cache işlemi yap
     */
    public function tenantCache($tenantKey, $key, $value = null, $ttl = null)
    {
        $cluster = $this->getClusterForTenant($tenantKey);
        $prefixedKey = "tenant_{$tenantKey}:" . $key;
        
        if ($value === null) {
            // GET operation
            return $cluster->get($prefixedKey);
        } else {
            // SET operation
            if ($ttl) {
                return $cluster->setex($prefixedKey, $ttl, $value);
            } else {
                return $cluster->set($prefixedKey, $value);
            }
        }
    }

    /**
     * Tenant cache'ini temizle
     */
    public function clearTenantCache($tenantKey)
    {
        $cluster = $this->getClusterForTenant($tenantKey);
        $pattern = "tenant_{$tenantKey}:*";
        
        try {
            $keys = $cluster->keys($pattern);
            if (!empty($keys)) {
                $cluster->del($keys);
                Log::info("Cleared cache for tenant: {$tenantKey}", [
                    'keys_deleted' => count($keys),
                ]);
                
                return count($keys);
            }
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error("Failed to clear cache for tenant: {$tenantKey}", [
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Cluster istatistiklerini al
     */
    public function getClusterStats()
    {
        $stats = [
            'clusters' => [],
            'total_memory' => 0,
            'total_keys' => 0,
            'total_connections' => 0,
        ];
        
        foreach ($this->clusters as $clusterName => $cluster) {
            try {
                $info = $cluster->info();
                
                $clusterStats = [
                    'name' => $clusterName,
                    'status' => 'online',
                    'memory_used' => $info['used_memory'] ?? 0,
                    'total_keys' => $info['total_keys'] ?? 0,
                    'connected_clients' => $info['connected_clients'] ?? 0,
                    'uptime' => $info['uptime_in_seconds'] ?? 0,
                    'tenant_range' => config("redis_cluster.nodes.{$clusterName}.tenant_range", []),
                ];
                
                $stats['clusters'][$clusterName] = $clusterStats;
                $stats['total_memory'] += $clusterStats['memory_used'];
                $stats['total_keys'] += $clusterStats['total_keys'];
                $stats['total_connections'] += $clusterStats['connected_clients'];
                
            } catch (\Exception $e) {
                $stats['clusters'][$clusterName] = [
                    'name' => $clusterName,
                    'status' => 'offline',
                    'error' => $e->getMessage(),
                ];
                
                Log::error("Failed to get stats for cluster: {$clusterName}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return $stats;
    }

    /**
     * Cluster rebalancing kontrolü
     */
    public function checkRebalancing()
    {
        if (!config('redis_cluster.tenant_mapping.rebalance_enabled')) {
            return false;
        }
        
        $threshold = config('redis_cluster.tenant_mapping.rebalance_threshold', 80);
        $stats = $this->getClusterStats();
        
        foreach ($stats['clusters'] as $clusterName => $clusterData) {
            if ($clusterData['status'] === 'offline') {
                continue;
            }
            
            // Memory usage check
            $memoryUsage = $clusterData['memory_used'];
            $maxMemory = config("redis_cluster.nodes.{$clusterName}.max_memory", 1024 * 1024 * 1024); // 1GB default
            $usagePercent = ($memoryUsage / $maxMemory) * 100;
            
            if ($usagePercent > $threshold) {
                Log::warning("Cluster {$clusterName} needs rebalancing", [
                    'memory_usage_percent' => $usagePercent,
                    'threshold' => $threshold,
                ]);
                
                return [
                    'needs_rebalancing' => true,
                    'cluster' => $clusterName,
                    'usage_percent' => $usagePercent,
                ];
            }
        }
        
        return ['needs_rebalancing' => false];
    }

    /**
     * Cluster health check
     */
    public function healthCheck()
    {
        $health = [
            'status' => 'healthy',
            'clusters' => [],
            'issues' => [],
        ];
        
        foreach ($this->clusters as $clusterName => $cluster) {
            try {
                $ping = $cluster->ping();
                $health['clusters'][$clusterName] = [
                    'status' => $ping === 'PONG' ? 'healthy' : 'unhealthy',
                    'response_time' => microtime(true),
                ];
                
            } catch (\Exception $e) {
                $health['clusters'][$clusterName] = [
                    'status' => 'offline',
                    'error' => $e->getMessage(),
                ];
                
                $health['issues'][] = "Cluster {$clusterName} is offline: " . $e->getMessage();
            }
        }
        
        // Overall health
        $offlineClusters = array_filter($health['clusters'], function($cluster) {
            return $cluster['status'] === 'offline';
        });
        
        if (count($offlineClusters) > 0) {
            $health['status'] = count($offlineClusters) === count($health['clusters']) ? 'critical' : 'degraded';
        }
        
        return $health;
    }

    /**
     * Failover işlemi
     */
    public function failover($clusterName)
    {
        if (!isset($this->clusters[$clusterName])) {
            throw new \InvalidArgumentException("Cluster {$clusterName} not found");
        }
        
        try {
            // Sentinel kullanıyorsak failover komutunu gönder
            if (config('redis_cluster.high_availability.sentinel_enabled')) {
                $sentinelHosts = config('redis_cluster.high_availability.sentinel_hosts');
                $masterName = config('redis_cluster.high_availability.master_name');
                
                foreach ($sentinelHosts as $sentinelHost) {
                    try {
                        $sentinel = new PredisClient($sentinelHost);
                        $result = $sentinel->sentinel('failover', $masterName);
                        
                        Log::info("Failover initiated for cluster: {$clusterName}", [
                            'sentinel_host' => $sentinelHost,
                            'result' => $result,
                        ]);
                        
                        return true;
                        
                    } catch (\Exception $e) {
                        Log::error("Failover failed on sentinel: {$sentinelHost}", [
                            'error' => $e->getMessage(),
                        ]);
                        continue;
                    }
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("Failover failed for cluster: {$clusterName}", [
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Cluster'ları yeniden başlat
     */
    public function restartCluster($clusterName)
    {
        unset($this->clusters[$clusterName]);
        
        $config = config("redis_cluster.nodes.{$clusterName}");
        if (!$config) {
            throw new \InvalidArgumentException("Cluster configuration not found: {$clusterName}");
        }
        
        $this->clusters[$clusterName] = new PredisClient(
            $config['hosts'],
            $config['options']
        );
        
        Log::info("Cluster restarted: {$clusterName}");
        
        return true;
    }
}