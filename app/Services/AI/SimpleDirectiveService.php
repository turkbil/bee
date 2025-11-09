<?php

namespace App\Services\AI;

use App\Models\AITenantDirective;
use Illuminate\Support\Facades\Cache;

/**
 * Simple Directive Service
 *
 * Global (tenant_id=0) ve tenant-specific directive yönetimi
 * Inheritance ve override desteği
 */
class SimpleDirectiveService
{
    /**
     * Get directive value with global fallback
     * Önce tenant'a özel, sonra global (tenant_id=0) bakar
     */
    public function getDirective(string $key, ?int $tenantId = null, $default = null)
    {
        $tenantId = $tenantId ?: (function_exists('tenant') ? tenant('id') : null);

        if (!$tenantId) {
            // No tenant context, check global only
            return $this->getGlobalDirective($key, $default);
        }

        // Cache key
        $cacheKey = "directive_{$tenantId}_{$key}";

        return Cache::remember($cacheKey, 3600, function() use ($key, $tenantId, $default) {
            // 1. Önce tenant-specific directive
            $directive = AITenantDirective::where('tenant_id', $tenantId)
                ->where('directive_key', $key)
                ->where('is_active', true)
                ->first();

            if ($directive) {
                return $this->parseValue($directive->directive_value, $directive->directive_type);
            }

            // 2. Sonra global directive (tenant_id = 0)
            $global = AITenantDirective::where('tenant_id', 0)
                ->where('directive_key', $key)
                ->where('is_active', true)
                ->first();

            if ($global) {
                return $this->parseValue($global->directive_value, $global->directive_type);
            }

            // 3. Default
            return $default;
        });
    }

    /**
     * Get global directive only
     */
    public function getGlobalDirective(string $key, $default = null)
    {
        $directive = AITenantDirective::where('tenant_id', 0)
            ->where('directive_key', $key)
            ->where('is_active', true)
            ->first();

        if ($directive) {
            return $this->parseValue($directive->directive_value, $directive->directive_type);
        }

        return $default;
    }

    /**
     * Set global directive
     */
    public function setGlobalDirective(string $key, $value, string $type = 'string', string $category = 'general'): bool
    {
        try {
            AITenantDirective::setValue(0, $key, $value, $type, $category);

            // Clear all tenant caches for this key
            $this->clearDirectiveCaches($key);

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to set global directive: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Check if tenant has override for a directive
     */
    public function hasOverride(string $key, int $tenantId): bool
    {
        return AITenantDirective::where('tenant_id', $tenantId)
            ->where('directive_key', $key)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get all directives for tenant (merged with globals)
     */
    public function getAllDirectives(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?: (function_exists('tenant') ? tenant('id') : null);

        // Get all global directives
        $globals = AITenantDirective::where('tenant_id', 0)
            ->where('is_active', true)
            ->pluck('directive_value', 'directive_key')
            ->toArray();

        if (!$tenantId) {
            return $globals;
        }

        // Get tenant-specific directives
        $tenantSpecific = AITenantDirective::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->pluck('directive_value', 'directive_key')
            ->toArray();

        // Merge: tenant overrides global
        return array_merge($globals, $tenantSpecific);
    }

    /**
     * Parse value based on type
     */
    protected function parseValue($value, string $type)
    {
        return match($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'float' => (float) $value,
            'json', 'array' => json_decode($value, true) ?? [],
            default => $value // string
        };
    }

    /**
     * Clear directive caches
     */
    protected function clearDirectiveCaches(string $key): void
    {
        // Clear all tenant-specific caches for this key
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            Cache::forget("directive_{$tenant->id}_{$key}");
        }

        // Clear global cache
        Cache::forget("directive_0_{$key}");
    }
}