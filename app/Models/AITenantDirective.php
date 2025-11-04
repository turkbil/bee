<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * AI Tenant Directive Model
 *
 * Central configuration table for tenant-specific AI behavior
 * Key-value pairs for controlling AI responses and actions
 */
class AITenantDirective extends Model
{
    use HasFactory;

    protected $table = 'ai_tenant_directives';

    protected $fillable = [
        'tenant_id',
        'directive_key',
        'directive_value',
        'directive_type',
        'category',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Directive categories
     */
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_BEHAVIOR = 'behavior';
    const CATEGORY_PRICING = 'pricing';
    const CATEGORY_CONTACT = 'contact';
    const CATEGORY_DISPLAY = 'display';
    const CATEGORY_LEAD = 'lead';

    /**
     * Directive types
     */
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_JSON = 'json';
    const TYPE_ARRAY = 'array';

    /**
     * Scope: Get active directives only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get directives by tenant
     */
    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope: Get directives by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get single directive value for tenant
     */
    public static function getValue(int $tenantId, string $key, $default = null)
    {
        $cacheKey = "directive_{$tenantId}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId, $key, $default) {
            $directive = self::byTenant($tenantId)
                ->active()
                ->where('directive_key', $key)
                ->first();

            if (!$directive) {
                return $default;
            }

            return self::parseValue($directive->directive_value, $directive->directive_type);
        });
    }

    /**
     * Get all directives for tenant as key-value array
     */
    public static function getAllForTenant(int $tenantId): array
    {
        $cacheKey = "tenant_directives_{$tenantId}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId) {
            $directives = self::byTenant($tenantId)
                ->active()
                ->get();

            $result = [];
            foreach ($directives as $directive) {
                $result[$directive->directive_key] = self::parseValue(
                    $directive->directive_value,
                    $directive->directive_type
                );
            }

            return $result;
        });
    }

    /**
     * Get directives by category for tenant
     */
    public static function getByCategory(int $tenantId, string $category): array
    {
        $cacheKey = "tenant_directives_{$tenantId}_{$category}";

        return Cache::remember($cacheKey, 3600, function () use ($tenantId, $category) {
            $directives = self::byTenant($tenantId)
                ->active()
                ->byCategory($category)
                ->get();

            $result = [];
            foreach ($directives as $directive) {
                $result[$directive->directive_key] = self::parseValue(
                    $directive->directive_value,
                    $directive->directive_type
                );
            }

            return $result;
        });
    }

    /**
     * Set directive value for tenant
     */
    public static function setValue(int $tenantId, string $key, $value, string $type = self::TYPE_STRING, string $category = self::CATEGORY_GENERAL): self
    {
        // Convert value to string for storage
        $stringValue = is_array($value) || is_object($value)
            ? json_encode($value)
            : (string) $value;

        $directive = self::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'directive_key' => $key,
            ],
            [
                'directive_value' => $stringValue,
                'directive_type' => $type,
                'category' => $category,
                'is_active' => true,
            ]
        );

        // Clear cache
        self::clearCache($tenantId);

        return $directive;
    }

    /**
     * Parse directive value based on type
     */
    protected static function parseValue(string $value, string $type)
    {
        return match ($type) {
            self::TYPE_INTEGER => (int) $value,
            self::TYPE_BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::TYPE_JSON, self::TYPE_ARRAY => json_decode($value, true) ?? [],
            default => $value, // TYPE_STRING
        };
    }

    /**
     * Clear cache for tenant directives
     */
    public static function clearCache(int $tenantId): void
    {
        Cache::forget("tenant_directives_{$tenantId}");

        // Clear individual directive caches
        $keys = self::byTenant($tenantId)->pluck('directive_key');
        foreach ($keys as $key) {
            Cache::forget("directive_{$tenantId}_{$key}");
        }

        // Clear category caches
        $categories = [
            self::CATEGORY_GENERAL,
            self::CATEGORY_BEHAVIOR,
            self::CATEGORY_PRICING,
            self::CATEGORY_CONTACT,
            self::CATEGORY_DISPLAY,
            self::CATEGORY_LEAD,
        ];

        foreach ($categories as $category) {
            Cache::forget("tenant_directives_{$tenantId}_{$category}");
        }
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-set tenant_id if using tenant context
        static::creating(function ($model) {
            if (!$model->tenant_id && function_exists('tenant')) {
                $model->tenant_id = tenant('id');
            }
        });

        // Clear cache on update/delete
        static::saved(function ($model) {
            self::clearCache($model->tenant_id);
        });

        static::deleted(function ($model) {
            self::clearCache($model->tenant_id);
        });
    }
}
