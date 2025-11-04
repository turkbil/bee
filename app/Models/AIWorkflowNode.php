<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AIWorkflowNode extends Model
{
    protected $table = 'ai_workflow_nodes';

    protected $fillable = [
        'node_key',
        'node_class',
        'node_name',
        'node_description',
        'category',
        'icon',
        'order',
        'is_global',
        'is_active',
        'tenant_whitelist',
        'default_config',
    ];

    protected $casts = [
        'node_name' => 'array',
        'node_description' => 'array',
        'tenant_whitelist' => 'array',
        'default_config' => 'array',
        'is_global' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get translated node name
     */
    public function getName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->node_name[$locale] ?? $this->node_name['en'] ?? $this->node_key;
    }

    /**
     * Get translated description
     */
    public function getDescription(?string $locale = null): ?string
    {
        if (!$this->node_description) {
            return null;
        }
        $locale = $locale ?? app()->getLocale();
        return $this->node_description[$locale] ?? $this->node_description['en'] ?? null;
    }

    /**
     * Check if node is available for tenant
     */
    public function isAvailableForTenant($tenantId): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->is_global) {
            return true;
        }

        if (!$this->tenant_whitelist) {
            return false;
        }

        return in_array($tenantId, $this->tenant_whitelist);
    }

    /**
     * Get all active nodes for tenant
     */
    public static function getForTenant($tenantId): array
    {
        $cacheKey = "ai_workflow_nodes_tenant_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            return static::where('is_active', true)
                ->where(function ($query) use ($tenantId) {
                    $query->where('is_global', true)
                        ->orWhereJsonContains('tenant_whitelist', $tenantId);
                })
                ->orderBy('category')
                ->orderBy('order')
                ->get()
                ->map(function ($node) {
                    return [
                        'type' => $node->node_key,
                        'name' => $node->getName(),
                        'description' => $node->getDescription(),
                        'class' => $node->node_class,
                        'category' => $node->category,
                        'icon' => $node->icon,
                        'default_config' => $node->default_config ?? [],
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get nodes grouped by category
     */
    public static function getByCategory($tenantId): array
    {
        $nodes = static::getForTenant($tenantId);

        $grouped = [];
        foreach ($nodes as $node) {
            $category = $node['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $node;
        }

        return $grouped;
    }

    /**
     * Clear cache for tenant
     */
    public static function clearCache($tenantId = null): void
    {
        if ($tenantId) {
            Cache::forget("ai_workflow_nodes_tenant_{$tenantId}");
        } else {
            // Clear all tenant caches
            Cache::flush();
        }
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
