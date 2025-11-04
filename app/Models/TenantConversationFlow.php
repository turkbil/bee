<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tenant AI Conversation Flow Model
 *
 * Stores visual conversation flows designed by tenant admins
 * Each flow consists of nodes (actions) and edges (connections)
 */
class TenantConversationFlow extends Model
{
    use HasFactory;

    protected $table = 'tenant_conversation_flows';

    protected $fillable = [
        'tenant_id',
        'flow_name',
        'flow_description',
        'flow_data',
        'start_node_id',
        'is_active',
        'priority',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'flow_data' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get conversations using this flow
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(AIConversation::class, 'flow_id');
    }

    /**
     * Scope: Get active flows only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get flows by tenant
     */
    public function scopeByTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope: Get flows ordered by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    /**
     * Get the highest priority active flow for a tenant
     */
    public static function getActiveFlowForTenant(int $tenantId): ?self
    {
        return self::byTenant($tenantId)
            ->active()
            ->byPriority()
            ->first();
    }

    /**
     * Get node by ID from flow data
     */
    public function getNode(string $nodeId): ?array
    {
        $nodes = $this->flow_data['nodes'] ?? [];

        foreach ($nodes as $node) {
            if ($node['id'] === $nodeId) {
                return $node;
            }
        }

        return null;
    }

    /**
     * Get all nodes from flow
     */
    public function getNodes(): array
    {
        return $this->flow_data['nodes'] ?? [];
    }

    /**
     * Get all edges from flow
     */
    public function getEdges(): array
    {
        return $this->flow_data['edges'] ?? [];
    }

    /**
     * Get next node ID based on current node and edge connection
     */
    public function getNextNodeId(string $currentNodeId, ?string $outputId = null): ?string
    {
        $edges = $this->getEdges();

        foreach ($edges as $edge) {
            if ($edge['source'] === $currentNodeId) {
                // If output ID specified, match it
                if ($outputId && isset($edge['sourceOutput']) && $edge['sourceOutput'] !== $outputId) {
                    continue;
                }

                return $edge['target'];
            }
        }

        return null;
    }

    /**
     * Validate flow structure
     */
    public function validateFlow(): array
    {
        $errors = [];

        // Check if flow_data exists
        if (empty($this->flow_data)) {
            $errors[] = 'Flow data is empty';
            return $errors;
        }

        // Check if nodes exist
        $nodes = $this->getNodes();
        if (empty($nodes)) {
            $errors[] = 'Flow has no nodes';
        }

        // Check if start node exists
        if (!$this->getNode($this->start_node_id)) {
            $errors[] = "Start node '{$this->start_node_id}' not found in flow";
        }

        // Check for circular dependencies (basic check)
        // TODO: Implement comprehensive circular dependency detection

        return $errors;
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
    }
}
