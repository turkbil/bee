<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Payment\App\Contracts\Payable;

class Subscription extends Model implements Payable
{
    use SoftDeletes;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_number',
        'status', // active, expired, cancelled, pending
        'price_per_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'auto_renewal',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price_per_cycle' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'auto_renewal' => 'boolean',
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->subscription_number)) {
                $subscription->subscription_number = 'SUB-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
            }
        });
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Scope: Active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    /**
     * Scope: Expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere('ends_at', '<=', now());
        });
    }

    /**
     * Scope: In trial period
     */
    public function scopeTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now());
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    /**
     * Check if in trial period
     */
    public function isInTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): int
    {
        if (!$this->ends_at) return 0;
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    // Payable Interface Implementation

    public function getPayableAmount(): float
    {
        return (float) $this->price_per_cycle;
    }

    public function getPayableDescription(): string
    {
        $planName = $this->plan ? $this->plan->title : 'Abonelik';
        return "{$planName} - {$this->subscription_number}";
    }

    public function getPayableCustomer(): array
    {
        return [
            'name' => $this->user->name ?? 'Misafir',
            'email' => $this->user->email ?? '',
            'phone' => $this->user->phone ?? '',
            'address' => 'Türkiye',
        ];
    }

    public function getPayableDetails(): ?array
    {
        return [
            'items' => [[
                'name' => $this->plan ? $this->plan->title : 'Abonelik',
                'price' => $this->price_per_cycle,
                'quantity' => 1,
            ]]
        ];
    }
}
