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
     * Combines global nodes from central DB + tenant-specific nodes from tenant DB
     */
    public static function getForTenant($tenantId): array
    {
        // IMPORTANT: Change version number if node classes change
        $version = 'v4'; // Updated to v4 after complete TenantSpecific → Shop namespace migration
        $cacheKey = "ai_workflow_nodes_tenant_{$tenantId}_{$version}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            \Log::info('🔍 AIWorkflowNode::getForTenant called', ['tenant_id' => $tenantId, 'cache_key' => "ai_workflow_nodes_tenant_{$tenantId}"]);

            $nodes = collect();

            // 1. Get global nodes from CENTRAL DB
            try {
                \Log::info('🔍 Querying central DB for global nodes');

                $centralNodes = \DB::connection('mysql')->table('ai_workflow_nodes')
                    ->where('is_active', true)
                    ->where('is_global', true)
                    ->orderBy('category')
                    ->orderBy('order')
                    ->get();

                \Log::info('🔍 Central DB returned ' . $centralNodes->count() . ' nodes');

                foreach ($centralNodes as $node) {
                    $nodeData = [
                        'type' => $node->node_key,
                        'name' => json_decode($node->node_name, true),
                        'description' => json_decode($node->node_description, true),
                        'class' => $node->node_class,
                        'category' => $node->category,
                        'icon' => $node->icon,
                        'default_config' => json_decode($node->default_config ?? '[]', true),
                        'is_global' => true,
                    ];

                    if ($node->node_key === 'category_detection') {
                        \Log::info('🔍 category_detection node loaded', ['class' => $node->node_class]);
                    }

                    $nodes->push($nodeData);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch global nodes from central DB', ['error' => $e->getMessage()]);
            }

            // 2. Get tenant-specific nodes from TENANT DB (if in tenant context)
            if (tenancy()->initialized) {
                try {
                    $tenantNodes = static::where('is_active', true)
                        ->where('is_global', false)
                        ->where(function ($query) use ($tenantId) {
                            $query->whereJsonContains('tenant_whitelist', $tenantId)
                                ->orWhereNull('tenant_whitelist');
                        })
                        ->orderBy('category')
                        ->orderBy('order')
                        ->get();

                    foreach ($tenantNodes as $node) {
                        $nodes->push([
                            'type' => $node->node_key,
                            'name' => $node->node_name,
                            'description' => $node->node_description,
                            'class' => $node->node_class,
                            'category' => $node->category,
                            'icon' => $node->icon,
                            'default_config' => $node->default_config ?? [],
                            'is_global' => false,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to fetch tenant nodes', ['error' => $e->getMessage()]);
                }
            }

            return $nodes->map(function ($node) {
                // Normalize name/description format
                if (is_array($node['name'])) {
                    $locale = app()->getLocale();
                    $node['name'] = $node['name'][$locale] ?? $node['name']['en'] ?? $node['type'];
                }
                if (is_array($node['description']) && !empty($node['description'])) {
                    $locale = app()->getLocale();
                    $node['description'] = $node['description'][$locale] ?? $node['description']['en'] ?? null;
                }
                return $node;
            })->toArray();
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
