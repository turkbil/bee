<?php

namespace Modules\TenantManagement\App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;
use Carbon\Carbon;

class DatabaseReadReplicaService
{
    protected $readConnections = [];
    protected $writeConnection = 'mysql';
    protected $replicationMap = [];
    
    public function __construct()
    {
        $this->initializeConnections();
    }

    /**
     * Read replica bağlantılarını başlat
     */
    private function initializeConnections()
    {
        // Central DB için read replica
        $this->readConnections['central'] = [
            'read_1' => 'mysql_read_1',
            'read_2' => 'mysql_read_2'
        ];

        // Tenant DB'ler için read replica mapping
        $this->replicationMap = Cache::remember('tenant_replica_map', 3600, function() {
            return Tenant::where('is_active', true)
                ->get()
                ->mapWithKeys(function($tenant) {
                    return [
                        $tenant->id => [
                            'write' => "tenant_{$tenant->id}",
                            'reads' => [
                                "tenant_{$tenant->id}_read_1",
                                "tenant_{$tenant->id}_read_2"
                            ]
                        ]
                    ];
                });
        });
    }

    /**
     * Tenant için uygun read replica connection seç
     */
    public function getReadConnection(?int $tenantId = null): string
    {
        if (!$tenantId) {
            // Central DB read replica
            $connections = $this->readConnections['central'];
            return $this->selectBestReadReplica($connections);
        }

        // Tenant specific read replica
        if (!isset($this->replicationMap[$tenantId])) {
            // Fallback to write connection
            return $this->replicationMap[$tenantId]['write'] ?? 'mysql';
        }

        $readConnections = $this->replicationMap[$tenantId]['reads'];
        return $this->selectBestReadReplica($readConnections);
    }

    /**
     * En uygun read replica'yı seç (yük bazında)
     */
    private function selectBestReadReplica(array $connections): string
    {
        $loads = [];
        
        foreach ($connections as $conn) {
            try {
                // Connection load check
                $load = $this->checkConnectionLoad($conn);
                $loads[$conn] = $load;
            } catch (\Exception $e) {
                // Connection failed, remove from consideration
                \Log::warning("Read replica connection failed: {$conn} - " . $e->getMessage());
                continue;
            }
        }

        if (empty($loads)) {
            // Fallback to write connection
            return $this->writeConnection;
        }

        // En düşük yüklü connection'ı seç
        return array_keys($loads, min($loads))[0];
    }

    /**
     * Connection yükünü kontrol et
     */
    private function checkConnectionLoad(string $connection): float
    {
        try {
            $start = microtime(true);
            DB::connection($connection)->select('SELECT 1');
            $responseTime = microtime(true) - $start;
            
            // Response time bazında yük hesapla
            return $responseTime * 1000; // millisecond cinsinden
        } catch (\Exception $e) {
            return PHP_FLOAT_MAX; // En yüksek yük = kullanılamaz
        }
    }

    /**
     * Read query için uygun connection'ı al
     */
    public function executeReadQuery(string $sql, array $bindings = [], ?int $tenantId = null)
    {
        $connection = $this->getReadConnection($tenantId);
        
        try {
            return DB::connection($connection)->select($sql, $bindings);
        } catch (\Exception $e) {
            // Read replica fail, fallback to write
            \Log::warning("Read replica failed, fallback to write: " . $e->getMessage());
            
            $writeConn = $tenantId 
                ? ($this->replicationMap[$tenantId]['write'] ?? 'mysql')
                : 'mysql';
                
            return DB::connection($writeConn)->select($sql, $bindings);
        }
    }

    /**
     * Tenant için optimized query execution
     */
    public function executeTenantReadQuery(int $tenantId, callable $query)
    {
        $connection = $this->getReadConnection($tenantId);
        
        try {
            return DB::connection($connection)->transaction(function() use ($query) {
                return $query();
            }, 1); // Read-only, 1 attempt
        } catch (\Exception $e) {
            // Fallback to write connection
            \Log::warning("Tenant read replica failed: {$tenantId} - " . $e->getMessage());
            
            $writeConn = $this->replicationMap[$tenantId]['write'] ?? 'mysql';
            return DB::connection($writeConn)->transaction(function() use ($query) {
                return $query();
            });
        }
    }

    /**
     * Connection health check
     */
    public function healthCheck(): array
    {
        $status = [
            'central' => [],
            'tenants' => []
        ];

        // Central read replicas health
        foreach ($this->readConnections['central'] as $name => $conn) {
            $status['central'][$name] = $this->pingConnection($conn);
        }

        // Tenant read replicas health (sample)
        $sampleTenants = array_slice(array_keys($this->replicationMap->toArray()), 0, 5);
        
        foreach ($sampleTenants as $tenantId) {
            $connections = $this->replicationMap[$tenantId]['reads'] ?? [];
            foreach ($connections as $conn) {
                $status['tenants'][$tenantId][$conn] = $this->pingConnection($conn);
            }
        }

        return $status;
    }

    /**
     * Connection ping test
     */
    private function pingConnection(string $connection): array
    {
        try {
            $start = microtime(true);
            DB::connection($connection)->select('SELECT 1 as ping');
            $responseTime = round((microtime(true) - $start) * 1000, 2);
            
            return [
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'last_check' => Carbon::now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'response_time_ms' => null,
                'last_check' => Carbon::now()->toISOString()
            ];
        }
    }

    /**
     * Replication lag check
     */
    public function checkReplicationLag(): array
    {
        $lagReport = [];

        // Central replication lag
        foreach ($this->readConnections['central'] as $name => $readConn) {
            $lagReport['central'][$name] = $this->measureReplicationLag('mysql', $readConn);
        }

        // Sample tenant replication lag
        $sampleTenants = array_slice(array_keys($this->replicationMap->toArray()), 0, 3);
        
        foreach ($sampleTenants as $tenantId) {
            $writeConn = $this->replicationMap[$tenantId]['write'];
            $readConns = $this->replicationMap[$tenantId]['reads'];
            
            foreach ($readConns as $readConn) {
                $lagReport['tenants'][$tenantId][$readConn] = 
                    $this->measureReplicationLag($writeConn, $readConn);
            }
        }

        return $lagReport;
    }

    /**
     * Measure replication lag between write and read connections
     */
    private function measureReplicationLag(string $writeConn, string $readConn): array
    {
        try {
            // Write a test record
            $testId = uniqid('replica_test_');
            $timestamp = microtime(true);
            
            DB::connection($writeConn)->table('replica_lag_test')->insert([
                'test_id' => $testId,
                'written_at' => $timestamp,
                'created_at' => Carbon::now()
            ]);

            // Wait and check on read replica
            $maxWait = 5; // seconds
            $found = false;
            $lag = null;
            
            for ($i = 0; $i < $maxWait * 10; $i++) {
                usleep(100000); // 100ms
                
                $record = DB::connection($readConn)
                    ->table('replica_lag_test')
                    ->where('test_id', $testId)
                    ->first();
                
                if ($record) {
                    $lag = round((microtime(true) - $timestamp) * 1000, 2); // ms
                    $found = true;
                    break;
                }
            }

            // Cleanup
            DB::connection($writeConn)->table('replica_lag_test')
                ->where('test_id', $testId)->delete();

            return [
                'status' => $found ? 'healthy' : 'lagged',
                'lag_ms' => $lag,
                'max_acceptable_lag_ms' => 1000,
                'is_acceptable' => $lag !== null && $lag < 1000
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'lag_ms' => null,
                'is_acceptable' => false
            ];
        }
    }

    /**
     * Optimize tenant query routing
     */
    public function optimizeQueryRouting(int $tenantId): array
    {
        $recommendations = [];

        // Tenant'in query pattern analizi
        $queryStats = $this->analyzeTenantQueryPatterns($tenantId);
        
        if ($queryStats['read_write_ratio'] > 3) {
            $recommendations[] = [
                'type' => 'read_replica_optimization',
                'message' => 'Bu tenant çoğunlukla read query yapıyor, read replica kullanımını artırın',
                'current_ratio' => $queryStats['read_write_ratio'],
                'suggested_action' => 'increase_read_replica_usage'
            ];
        }

        if ($queryStats['avg_response_time'] > 500) {
            $recommendations[] = [
                'type' => 'performance_warning',
                'message' => 'Yavaş query tespit edildi, read replica dağılımını optimize edin',
                'avg_response_time_ms' => $queryStats['avg_response_time'],
                'suggested_action' => 'optimize_connection_distribution'
            ];
        }

        return $recommendations;
    }

    /**
     * Analyze tenant query patterns
     */
    private function analyzeTenantQueryPatterns(int $tenantId): array
    {
        // Bu method gerçek query istatistiklerini analiz edecek
        // Şimdilik sample data dönüyor
        
        return [
            'read_write_ratio' => 4.2,
            'avg_response_time' => 245, // ms
            'query_count_24h' => 1580,
            'peak_hours' => ['09:00-11:00', '14:00-16:00'],
            'slowest_queries' => [
                'SELECT * FROM large_table' => 850, // ms
                'Complex JOIN query' => 620
            ]
        ];
    }
}