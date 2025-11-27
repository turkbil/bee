<?php

namespace Modules\Subscription\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'subscription_id';

    // BaseModel'den gelen is_active varsayılanını override et
    protected $attributes = [];

    protected $fillable = [
        'customer_id',
        'plan_id',
        'subscription_number',
        'status',
        'billing_cycle',
        'price_per_cycle',
        'currency',
        'has_trial',
        'trial_days',
        'trial_ends_at',
        'started_at',
        'current_period_start',
        'current_period_end',
        'next_billing_date',
        'cancelled_at',
        'expires_at',
        'payment_method_id',
        'auto_renew',
        'billing_cycles_completed',
        'total_paid',
        'cancellation_reason',
        'cancellation_feedback',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'cancellation_feedback' => 'array',
        'price_per_cycle' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'started_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'next_billing_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'expires_at' => 'datetime',
        'has_trial' => 'boolean',
        'auto_renew' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id', 'subscription_plan_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('current_period_end')
                  ->orWhere('current_period_end', '>', now());
            });
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->whereIn('status', ['active', 'trial'])
            ->whereNotNull('current_period_end')
            ->where('current_period_end', '<=', now()->addDays($days))
            ->where('current_period_end', '>', now());
    }

    // Status Checks
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               ($this->current_period_end === null || $this->current_period_end->isFuture());
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' &&
               $this->trial_ends_at !== null &&
               $this->trial_ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->current_period_end !== null && $this->current_period_end->isPast());
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    // Helpers
    public function daysRemaining(): int
    {
        if ($this->isTrial() && $this->trial_ends_at) {
            return max(0, now()->diffInDays($this->trial_ends_at, false));
        }

        if ($this->current_period_end) {
            return max(0, now()->diffInDays($this->current_period_end, false));
        }

        return 0;
    }

    public function cancel(?string $reason = null): bool
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
            'cancellation_reason' => $reason,
        ]);

        return true;
    }

    public function pause(): bool
    {
        $this->update(['status' => 'paused']);
        return true;
    }

    public function resume(): bool
    {
        $this->update(['status' => 'active']);
        return true;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'trial' => 'info',
            'expired' => 'danger',
            'cancelled' => 'secondary',
            'paused' => 'warning',
            'pending_payment' => 'warning',
            default => 'secondary',
        };
    }

    // Generate unique subscription number
    public static function generateSubscriptionNumber(): string
    {
        do {
            $number = 'SUB-' . strtoupper(uniqid());
        } while (self::where('subscription_number', $number)->exists());

        return $number;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->subscription_number)) {
                $subscription->subscription_number = self::generateSubscriptionNumber();
            }
        });
    }
}
