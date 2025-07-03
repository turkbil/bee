<?php

namespace Modules\AI\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;
use App\Models\User;

class AITokenPurchase extends Model
{
    protected $table = 'ai_token_purchases';
    
    protected $fillable = [
        'tenant_id',
        'user_id',
        'package_id',
        'token_amount',
        'price_paid',
        'amount',
        'currency',
        'status',
        'payment_method',
        'payment_transaction_id',
        'payment_data',
        'notes',
        'purchased_at'
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'amount' => 'decimal:2',
        'token_amount' => 'integer',
        'payment_data' => 'array',
        'purchased_at' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'purchased_at'
    ];

    /**
     * Get the tenant that owns the purchase
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user that made the purchase
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package that was purchased
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(\Modules\AI\App\Models\AITokenPackage::class, 'package_id');
    }

    /**
     * Scope for completed purchases
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending purchases
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark purchase as completed
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'purchased_at' => now()
        ]);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_paid, 2) . ' ' . $this->currency;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning', 
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }
}