<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flow extends Model
{
    use HasFactory;

    protected $table = 'ai_flows';

    protected $fillable = [
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
     * Get highest priority active flow
     */
    public static function getActiveFlow()
    {
        return static::active()
            ->byPriority()
            ->first();
    }
}
