<?php

declare(strict_types=1);

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class AIDynamicDataSource extends Model
{
    protected $connection = 'mysql'; // Central DB for all tenants
    protected $table = 'ai_dynamic_data_sources';
    
    protected $fillable = [
        'name',
        'slug',
        'source_type',
        'source_config',
        'cache_ttl',
        'last_updated',
        'is_active'
    ];
    
    protected $casts = [
        'source_config' => 'array',
        'cache_ttl' => 'integer',
        'last_updated' => 'datetime',
        'is_active' => 'boolean'
    ];
    
    /**
     * Inputs that use this data source
     */
    public function inputs(): HasMany
    {
        return $this->hasMany(AIFeatureInput::class, 'dynamic_data_source_id');
    }
    
    /**
     * Get data options with caching
     */
    public function getDataOptions(): Collection
    {
        if (!$this->is_active) {
            return collect([]);
        }
        
        $cacheKey = "dynamic_data_source_{$this->slug}";
        
        return Cache::remember($cacheKey, $this->cache_ttl, function() {
            return match($this->source_type) {
                'static' => $this->getStaticData(),
                'database' => $this->getDatabaseData(),
                'api' => $this->getApiData(),
                'cache' => $this->getCacheData(),
                default => collect([])
            };
        });
    }
    
    /**
     * Get static data from config
     */
    private function getStaticData(): Collection
    {
        $data = $this->source_config['data'] ?? [];
        
        return collect($data)->map(function ($label, $value) {
            return [
                'value' => $value,
                'label' => $label
            ];
        });
    }
    
    /**
     * Get data from database
     */
    private function getDatabaseData(): Collection
    {
        $config = $this->source_config;
        
        if (!isset($config['model']) || !class_exists($config['model'])) {
            return collect([]);
        }
        
        $query = app($config['model'])->newQuery();
        
        // Apply where conditions
        if (isset($config['where'])) {
            foreach ($config['where'] as $condition) {
                $query->where($condition[0], $condition[1] ?? '=', $condition[2] ?? null);
            }
        }
        
        $valueField = $config['value_field'] ?? 'id';
        $labelField = $config['label_field'] ?? 'name';
        
        return $query->get()->map(function($item) use ($valueField, $labelField) {
            return [
                'value' => $item->{$valueField},
                'label' => $item->{$labelField}
            ];
        });
    }
    
    /**
     * Get data from API
     */
    private function getApiData(): Collection
    {
        $config = $this->source_config;
        
        if (!isset($config['endpoint'])) {
            return collect([]);
        }
        
        try {
            // HTTP client ile API çağrısı yapılacak
            // Bu basit implementasyon şimdilik
            return collect([]);
        } catch (\Exception $e) {
            return collect([]);
        }
    }
    
    /**
     * Get data from cache
     */
    private function getCacheData(): Collection
    {
        $config = $this->source_config;
        $cacheKey = $config['cache_key'] ?? null;
        
        if (!$cacheKey) {
            return collect([]);
        }
        
        $data = Cache::get($cacheKey, []);
        
        return collect($data);
    }
    
    /**
     * Clear this data source's cache
     */
    public function clearCache(): void
    {
        $cacheKey = "dynamic_data_source_{$this->slug}";
        Cache::forget($cacheKey);
        
        $this->update(['last_updated' => now()]);
    }
    
    /**
     * Refresh data and update timestamp
     */
    public function refreshData(): Collection
    {
        $this->clearCache();
        $data = $this->getDataOptions();
        
        $this->update(['last_updated' => now()]);
        
        return $data;
    }
    
    /**
     * Scope for active data sources
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Get config value with default fallback
     */
    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return $this->source_config[$key] ?? $default;
    }
}