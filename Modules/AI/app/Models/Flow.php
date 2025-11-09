<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flow extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Central DB with tenant_id filtering
    protected $table = 'ai_flows';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'flow_data',
        'metadata',
        'priority',
        'status'
    ];

    protected $casts = [
        'flow_data' => 'array',
        'metadata' => 'array',
        'priority' => 'integer'
    ];

    /**
     * Scope: Only active flows
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Order by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    /**
     * Get highest priority active flow for tenant
     */
    public static function getActiveFlow($tenantId = null)
    {
        $tenantId = $tenantId ?: (function_exists('tenant') ? tenant('id') : null);

        return static::where('tenant_id', $tenantId)
            ->active()
            ->byPriority()
            ->first();
    }

    /**
     * Scope: Filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
