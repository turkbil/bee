<?php

namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\TenantCacheManager;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\TenantManagement\App\Services\CacheOperationTracker;

#[Layout('admin.layout')]
class TenantCacheComponent extends Component
{
    use WithPagination;

    public $selectedTenantId = null;
    public $selectedTenant = null;
    public $selectedCacheType = '';
    public $search = '';
    public $cacheFilter = 'all';
    
    // Cache data
    public $cacheStats = [];
    public $cacheKeys = [];
    public $showCacheKeys = false;
    public $keyPattern = '*';
    public $keyLimit = 100;
    
    // Cache key management
    public $selectedKey = '';
    public $keyValue = '';
    public $keyTtl = 3600;
    public $showKeyDetails = false;
    
    // Batch operations
    public $selectedKeys = [];
    public $selectAll = false;
    public $bulkAction = '';
    
    // Memory allocation
    public $memoryLimits = [];
    public $editingMemoryLimit = false;
    public $newMemoryLimit = 256;
    
    // Cache configuration
    public $cacheConfig = [];
    public $showConfig = false;
    
    // Real-time monitoring
    public $autoRefresh = false;
    public $refreshInterval = 5; // seconds

    protected $listeners = [
        'tenantSelected' => 'selectTenant',
        'refreshCache' => 'loadCacheData',
        'keyUpdated' => 'loadCacheKeys'
    ];

    protected $queryString = [
        'selectedTenantId' => ['except' => null],
        'cacheFilter' => ['except' => 'all'],
        'keyPattern' => ['except' => '*']
    ];

    public function mount()
    {
        $this->loadCacheData();
    }

    public function selectTenant($tenantId)
    {
        $this->selectedTenantId = $tenantId;
        $this->resetPage();
        $this->selectedKeys = [];
        $this->selectAll = false;
        $this->loadCacheData();
    }

    public function clearTenantSelection()
    {
        $this->selectedTenantId = null;
        $this->resetPage();
        $this->cacheStats = [];
        $this->cacheKeys = [];
        $this->showCacheKeys = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCacheFilter()
    {
        $this->resetPage();
        $this->loadCacheKeys();
    }

    public function updatedKeyPattern()
    {
        $this->loadCacheKeys();
    }

    public function updatedKeyLimit()
    {
        $this->loadCacheKeys();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedKeys = array_column($this->cacheKeys, 'key');
        } else {
            $this->selectedKeys = [];
        }
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        
        if ($this->autoRefresh) {
            $this->dispatch('startAutoRefresh', ['interval' => $this->refreshInterval * 1000]);
        } else {
            $this->dispatch('stopAutoRefresh');
        }
    }

    public function loadCacheData()
    {
        try {
            if ($this->selectedTenantId) {
                $this->loadTenantCacheStats();
                if ($this->showCacheKeys) {
                    $this->loadCacheKeys();
                }
            } else {
                $this->loadSystemCacheStats();
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache verileri yüklenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function loadTenantCacheStats()
    {
        if (!$this->selectedTenantId) {
            return;
        }

        try {
            $tenant = Tenant::find($this->selectedTenantId);
            if (!$tenant) {
                return;
            }

            $cacheManager = new TenantCacheManager();
            tenancy()->initialize($tenant);

            $redis = Redis::connection();
            
            // Multiple pattern support for backward compatibility
            $patterns = [
                "*tenant_{$this->selectedTenantId}*",
                "tenant_{$this->selectedTenantId}:*",
                "*tuufi_redis_tenant_{$this->selectedTenantId}*",
                "*{$this->selectedTenantId}:*"
            ];
            
            $keys = [];
            foreach ($patterns as $pattern) {
                $patternKeys = $redis->keys($pattern);
                $keys = array_merge($keys, $patternKeys);
            }
            $keys = array_unique($keys);
            
            $totalSize = 0;
            $keyCount = count($keys);
            $expiredKeys = 0;
            $hitRate = 0;
            
            if ($keyCount > 0) {
                // Sample first 100 keys for memory calculation
                foreach (array_slice($keys, 0, 100) as $key) {
                    try {
                        $size = $redis->memory('usage', $key);
                        $totalSize += $size;
                        
                        if ($redis->ttl($key) === -2) {
                            $expiredKeys++;
                        }
                    } catch (\Exception $e) {
                        // Ignore individual key errors
                    }
                }
                
                // Estimate total size
                if ($keyCount > 100) {
                    $avgSize = $totalSize / 100;
                    $totalSize = $avgSize * $keyCount;
                }

                // Calculate hit rate with multiple pattern support
                $hitCounters = [
                    "tenant:{$this->selectedTenantId}:cache:hits",
                    "cache:tenant_{$this->selectedTenantId}:hits",
                    "tenant_{$this->selectedTenantId}:hits",
                    "tuufi_redis_tenant_{$this->selectedTenantId}:hits"
                ];

                $missCounters = [
                    "tenant:{$this->selectedTenantId}:cache:misses",
                    "cache:tenant_{$this->selectedTenantId}:misses",
                    "tenant_{$this->selectedTenantId}:misses",
                    "tuufi_redis_tenant_{$this->selectedTenantId}:misses"
                ];
                
                $hits = 0;
                $misses = 0;
                
                foreach ($hitCounters as $counter) {
                    $hits += (int) $redis->get($counter) ?: 0;
                }
                
                foreach ($missCounters as $counter) {
                    $misses += (int) $redis->get($counter) ?: 0;
                }
                $total = $hits + $misses;
                $hitRate = $total > 0 ? round(($hits / $total) * 100, 2) : 0;
            }

            $this->cacheStats = [
                'total_keys' => $keyCount,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                'expired_keys' => $expiredKeys,
                'hit_rate' => $hitRate,
                'hits' => $hits ?? 0,
                'misses' => $misses ?? 0,
                'memory_limit' => $this->getTenantMemoryLimit($this->selectedTenantId),
                'memory_usage_percent' => $this->calculateMemoryUsagePercent($totalSize, $this->selectedTenantId),
                'memory_usage' => round($totalSize / 1024 / 1024, 2) . ' MB',
                'memory_percentage' => $this->calculateMemoryUsagePercent($totalSize, $this->selectedTenantId),
            ];
            
        } catch (\Exception $e) {
            $this->cacheStats = [];
            throw $e;
        }
    }

    public function loadSystemCacheStats()
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info('memory');
            
            $this->cacheStats = [
                'system_memory_used' => round($info['used_memory'] / 1024 / 1024, 2),
                'system_memory_peak' => round($info['used_memory_peak'] / 1024 / 1024, 2),
                'total_keys' => $redis->dbsize(),
                'connected_clients' => $redis->info('clients')['connected_clients'] ?? 0,
                'redis_version' => $redis->info('server')['redis_version'] ?? 'Unknown',
            ];
            
        } catch (\Exception $e) {
            $this->cacheStats = [];
        }
    }

    public function loadCacheKeys()
    {
        if (!$this->selectedTenantId) {
            return;
        }

        try {
            $redis = Redis::connection();
            $basePattern = "*tenant_{$this->selectedTenantId}*";
            
            if ($this->keyPattern === '*') {
                $pattern = $basePattern . '*';
            } else {
                $pattern = $basePattern . $this->keyPattern;
            }
            
            $allKeys = $redis->keys($pattern);
            $keys = array_slice($allKeys, 0, $this->keyLimit);
            
            $keyData = [];
            foreach ($keys as $key) {
                try {
                    $type = $redis->type($key);
                    $ttl = $redis->ttl($key);
                    $size = $redis->memory('usage', $key);
                    
                    // Clean key name for display
                    $cleanKey = str_replace($basePattern, '', $key);
                    
                    // Get tenant info
                    $tenant = Tenant::find($this->selectedTenantId);
                    
                    $keyData[] = [
                        'key' => $key,
                        'clean_key' => $cleanKey,
                        'type' => $type,
                        'ttl' => $ttl,
                        'size' => $size,
                        'size_kb' => round($size / 1024, 2),
                        'size_human' => $this->formatBytes($size),
                        'ttl_human' => $ttl > 0 ? $this->formatTtl($ttl) : 'Never',
                        'expires_at' => $ttl > 0 ? now()->addSeconds($ttl)->format('Y-m-d H:i:s') : null,
                        'is_expired' => $ttl === -2,
                        'tenant_id' => $this->selectedTenantId,
                        'tenant_name' => $tenant ? $tenant->title : null,
                        'hits' => rand(1, 100), // Mock data for now
                        'last_accessed' => now()->subMinutes(rand(1, 60)),
                    ];
                } catch (\Exception $e) {
                    // Skip problematic keys
                }
            }
            
            // Apply filters
            if ($this->cacheFilter !== 'all') {
                $keyData = array_filter($keyData, function($key) {
                    return match($this->cacheFilter) {
                        'expired' => $key['is_expired'],
                        'active' => !$key['is_expired'] && $key['ttl'] !== -1,
                        'persistent' => $key['ttl'] === -1,
                        default => true
                    };
                });
            }
            
            $this->cacheKeys = array_values($keyData);
            $this->showCacheKeys = true;
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache anahtarları yüklenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function viewKey($key)
    {
        try {
            $redis = Redis::connection();
            $type = $redis->type($key);
            $ttl = $redis->ttl($key);
            
            $value = match($type) {
                'string' => $redis->get($key),
                'hash' => $redis->hgetall($key),
                'list' => $redis->lrange($key, 0, -1),
                'set' => $redis->smembers($key),
                'zset' => $redis->zrange($key, 0, -1, 'WITHSCORES'),
                default => 'Unsupported type: ' . $type
            };

            $this->selectedKey = $key;
            $this->keyValue = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value;
            $this->keyTtl = $ttl > 0 ? $ttl : 3600;
            $this->showKeyDetails = true;
            
            $this->dispatch('showModal', ['id' => 'modal-key-details']);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Anahtar değeri görüntülenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updateKey()
    {
        if (!$this->selectedKey) {
            return;
        }

        try {
            $redis = Redis::connection();
            
            // Update value
            $redis->set($this->selectedKey, $this->keyValue);
            
            // Update TTL
            if ($this->keyTtl > 0) {
                $redis->expire($this->selectedKey, $this->keyTtl);
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Anahtar güncellendi.',
                'type' => 'success'
            ]);
            
            $this->dispatch('hideModal', ['id' => 'modal-key-details']);
            $this->loadCacheKeys();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Anahtar güncellenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function deleteKey($key)
    {
        try {
            $redis = Redis::connection();
            $redis->del($key);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Cache anahtarı silindi.',
                'type' => 'success'
            ]);
            
            $this->loadCacheKeys();
            $this->loadCacheData();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Anahtar silinirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function clearTenantCache($tenantId = null)
    {
        $targetTenantId = $tenantId ?: $this->selectedTenantId;
        
        try {
            $redis = Redis::connection();
            $pattern = "*tenant_{$targetTenantId}*";
            $keys = $redis->keys($pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
                $count = count($keys);
            } else {
                $count = 0;
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => "{$count} cache anahtarı temizlendi.",
                'type' => 'success'
            ]);
            
            $this->loadCacheData();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache temizlenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function clearExpiredKeys()
    {
        try {
            $redis = Redis::connection();
            $pattern = "*tenant_{$this->selectedTenantId}*";
            $keys = $redis->keys($pattern);
            
            $expiredCount = 0;
            foreach ($keys as $key) {
                if ($redis->ttl($key) === -2) {
                    $redis->del($key);
                    $expiredCount++;
                }
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => "{$expiredCount} süresi dolmuş anahtar temizlendi.",
                'type' => 'success'
            ]);
            
            $this->loadCacheData();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Süresi dolmuş anahtarlar temizlenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedKeys) || !$this->bulkAction) {
            return;
        }

        try {
            $redis = Redis::connection();
            $count = count($this->selectedKeys);
            
            switch ($this->bulkAction) {
                case 'delete':
                    $redis->del($this->selectedKeys);
                    $message = "{$count} anahtar silindi.";
                    break;
                    
                case 'extend_ttl':
                    foreach ($this->selectedKeys as $key) {
                        $redis->expire($key, 3600); // 1 hour
                    }
                    $message = "{$count} anahtarın süresi 1 saat uzatıldı.";
                    break;
                    
                case 'make_persistent':
                    foreach ($this->selectedKeys as $key) {
                        $redis->persist($key);
                    }
                    $message = "{$count} anahtar kalıcı hale getirildi.";
                    break;
                    
                default:
                    throw new \Exception('Geçersiz bulk aksiyon');
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ]);

            $this->selectedKeys = [];
            $this->selectAll = false;
            $this->bulkAction = '';
            $this->loadCacheData();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Toplu işlem sırasında hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updateMemoryLimit()
    {
        if (!$this->selectedTenantId || !$this->newMemoryLimit) {
            return;
        }

        try {
            // Store memory limit in database or config
            $tenant = Tenant::find($this->selectedTenantId);
            if ($tenant) {
                $data = $tenant->data ?? [];
                $data['cache_memory_limit'] = $this->newMemoryLimit;
                $tenant->update(['data' => $data]);
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı',
                    'message' => 'Bellek limiti güncellendi.',
                    'type' => 'success'
                ]);
                
                $this->editingMemoryLimit = false;
                $this->loadCacheData();
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Bellek limiti güncellenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function exportCacheReport()
    {
        try {
            $report = [
                'tenant_id' => $this->selectedTenantId,
                'export_date' => now()->toISOString(),
                'cache_stats' => $this->cacheStats,
                'cache_keys' => $this->cacheKeys,
                'key_pattern' => $this->keyPattern,
                'total_exported_keys' => count($this->cacheKeys)
            ];

            $filename = 'cache_report_tenant_' . $this->selectedTenantId . '_' . now()->format('Y_m_d_H_i') . '.json';
            $filepath = storage_path('app/reports/' . $filename);
            
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            file_put_contents($filepath, json_encode($report, JSON_PRETTY_PRINT));
            
            $this->dispatch('toast', [
                'title' => 'Rapor Oluşturuldu',
                'message' => "Cache raporu {$filename} olarak kaydedildi.",
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Rapor oluşturulurken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function clearAllCache()
    {
        try {
            $redis = Redis::connection();
            
            if ($this->selectedTenantId) {
                $this->clearTenantCache($this->selectedTenantId);
            } else {
                $redis->flushdb();
                $this->dispatch('toast', [
                    'title' => 'Başarılı',
                    'message' => 'Tüm cache temizlendi.',
                    'type' => 'success'
                ]);
            }
            
            $this->loadCacheData();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache temizlenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function optimizeCache()
    {
        try {
            // Clear expired keys first
            $this->clearExpiredKeys();
            
            if ($this->selectedTenantId) {
                $redis = Redis::connection();
                $pattern = "*tenant_{$this->selectedTenantId}*";
                $keys = $redis->keys($pattern);
                
                // Compress large values if possible
                $optimizedCount = 0;
                foreach ($keys as $key) {
                    $value = $redis->get($key);
                    if (is_string($value) && strlen($value) > 1024) {
                        $compressed = gzcompress($value);
                        if (strlen($compressed) < strlen($value)) {
                            $redis->set($key, $compressed);
                            $optimizedCount++;
                        }
                    }
                }
                
                $this->dispatch('toast', [
                    'title' => 'Optimizasyon Tamamlandı',
                    'message' => "{$optimizedCount} anahtar optimize edildi.",
                    'type' => 'success'
                ]);
            }
            
            $this->loadCacheData();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache optimize edilirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function refreshStats()
    {
        $this->loadCacheData();
        
        $this->dispatch('toast', [
            'title' => 'İstatistikler Yenilendi',
            'message' => 'Cache istatistikleri güncellendi.',
            'type' => 'info'
        ]);
    }

    public function compressCache()
    {
        try {
            if (!$this->selectedTenantId) {
                return;
            }
            
            $redis = Redis::connection();
            $pattern = "*tenant_{$this->selectedTenantId}*";
            $keys = $redis->keys($pattern);
            
            $compressedCount = 0;
            foreach ($keys as $key) {
                $value = $redis->get($key);
                if (is_string($value) && strlen($value) > 512) {
                    $compressed = gzcompress($value, 6);
                    if (strlen($compressed) < strlen($value) * 0.8) {
                        $redis->set($key . ':compressed', $compressed);
                        $redis->expire($key . ':compressed', $redis->ttl($key));
                        $compressedCount++;
                    }
                }
            }
            
            $this->dispatch('toast', [
                'title' => 'Sıkıştırma Tamamlandı',
                'message' => "{$compressedCount} anahtar sıkıştırıldı.",
                'type' => 'success'
            ]);
            
            $this->loadCacheData();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache sıkıştırılırken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function viewCacheValue($key)
    {
        $this->viewKey($key);
    }

    public function refreshKey($key)
    {
        try {
            $redis = Redis::connection();
            
            if ($redis->exists($key)) {
                $ttl = $redis->ttl($key);
                if ($ttl > 0) {
                    $redis->expire($key, $ttl + 3600); // Extend by 1 hour
                }
                
                $this->dispatch('toast', [
                    'title' => 'Anahtar Yenilendi',
                    'message' => 'Anahtar süresi uzatıldı.',
                    'type' => 'success'
                ]);
                
                $this->loadCacheKeys();
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Anahtar yenilenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function getTenantsProperty()
    {
        $query = Tenant::query();
        
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%');
        }
        
        return $query->orderBy('title')->paginate(20);
    }

    private function formatBytes($bytes)
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    private function formatTtl($seconds)
    {
        if ($seconds >= 3600) {
            return round($seconds / 3600, 1) . 'h';
        } elseif ($seconds >= 60) {
            return round($seconds / 60, 1) . 'm';
        }
        return $seconds . 's';
    }

    private function getTenantMemoryLimit($tenantId): int
    {
        $tenant = Tenant::find($tenantId);
        if ($tenant && isset($tenant->data['cache_memory_limit'])) {
            return (int) $tenant->data['cache_memory_limit'];
        }
        return 256; // Default 256MB
    }

    private function calculateMemoryUsagePercent($usedBytes, $tenantId): float
    {
        $limitMB = $this->getTenantMemoryLimit($tenantId);
        $limitBytes = $limitMB * 1024 * 1024;
        
        if ($limitBytes <= 0) {
            return 0;
        }
        
        return round(($usedBytes / $limitBytes) * 100, 2);
    }

    public function render()
    {
        // Default cache stats if empty
        if (empty($this->cacheStats)) {
            $this->cacheStats = [
                'total_keys' => 0,
                'hit_rate' => 0,
                'memory_usage' => '0 MB',
                'memory_percentage' => 0,
                'avg_response_time' => 0,
                'hits' => 0,
                'misses' => 0,
                'active_connections' => 0,
                'expired_keys' => 0
            ];
        }

        // Make sure cacheKeys is not an object to avoid method_exists error
        $cacheKeys = is_array($this->cacheKeys) ? $this->cacheKeys : [];

        return view('tenantmanagement::livewire.tenantcache', [
            'tenants' => $this->tenants,
            'cacheStats' => $this->cacheStats,
            'cacheKeys' => $cacheKeys,
        ]);
    }
}