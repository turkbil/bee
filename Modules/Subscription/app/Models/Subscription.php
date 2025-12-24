<?php

namespace Modules\Subscription\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Carbon\Carbon;

#[ObservedBy([\App\Observers\SubscriptionObserver::class])]
class Subscription extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'subscription_id';

    // BaseModel'den gelen is_active varsayılanını override et
    protected $attributes = [];

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'subscription_number',
        'status',
        'billing_cycle', // Deprecated - backward compatibility
        'cycle_key', // Yeni dinamik cycle sistemi
        'cycle_metadata', // Cycle bilgileri (label, duration_days, trial_days...)
        'price_per_cycle',
        'currency',
        'has_trial',
        'trial_days',
        'trial_ends_at',
        'started_at',
        'starts_at', // Alias for started_at
        'ends_at', // Alias for current_period_end
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
        'cycle_metadata' => 'array', // Yeni: Dinamik cycle bilgileri
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id'); // Alias for user()
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id', 'subscription_plan_id');
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPendingPayment(): bool
    {
        return $this->status === 'pending_payment';
    }

    // Helpers
    public function daysRemaining(): int
    {
        if ($this->isTrial() && $this->trial_ends_at) {
            return max(0, (int) floor(now()->diffInDays($this->trial_ends_at, false)));
        }

        if ($this->current_period_end) {
            return max(0, (int) floor(now()->diffInDays($this->current_period_end, false)));
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
            'pending' => 'info',
            'expired' => 'danger',
            'cancelled' => 'secondary',
            'paused' => 'warning',
            'pending_payment' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Status label'ı al (çoklu dil desteği)
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => __('subscription::admin.status_active'),
            'trial' => __('subscription::admin.status_trial'),
            'pending' => __('subscription::admin.status_pending'),
            'expired' => __('subscription::admin.status_expired'),
            'cancelled' => __('subscription::admin.status_cancelled'),
            'paused' => __('subscription::admin.status_paused'),
            'pending_payment' => __('subscription::admin.status_pending_payment'),
            default => $this->status,
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

    /**
     * Cycle label'ı al (çoklu dil desteği)
     */
    public function getCycleLabel(?string $locale = null): ?string
    {
        if (!$this->cycle_metadata) {
            return null;
        }

        $locale = $locale ?? app()->getLocale();

        return $this->cycle_metadata['label'][$locale]
            ?? $this->cycle_metadata['label']['tr']
            ?? $this->cycle_metadata['label']['en']
            ?? $this->cycle_key;
    }

    /**
     * Cycle süresini al (gün olarak)
     */
    public function getCycleDuration(): ?int
    {
        return $this->cycle_metadata['duration_days'] ?? null;
    }

    /**
     * Cycle fiyatını al
     */
    public function getCyclePrice(): ?float
    {
        return $this->cycle_metadata['price'] ?? $this->price_per_cycle;
    }

    /**
     * Kullanıcı daha önce deneme kullandı mı?
     */
    public function scopeHasUsedTrial($query, int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('has_trial', true)
            ->exists();
    }

    /**
     * User için trial kullanımını kontrol et (statik helper)
     * Pending_payment olanlar sayılmaz (ödeme başarısız olursa trial hakkı yanmaz)
     */
    public static function userHasUsedTrial(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('has_trial', true)
            ->where('status', '!=', 'pending_payment') // Pending olanları sayma
            ->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->subscription_number)) {
                $subscription->subscription_number = self::generateSubscriptionNumber();
            }
        });

        // 🔥 Premium cache'i otomatik temizle (subscription değiştiğinde)
        $clearPremiumCache = function ($subscription) {
            if ($subscription->user_id) {
                \Cache::forget('user_' . $subscription->user_id . '_is_premium_tenant_1001');
            }
        };

        static::created($clearPremiumCache);
        static::updated($clearPremiumCache);
        static::deleted($clearPremiumCache);
    }

    // ==========================================
    // SUBSCRIPTION CHAIN SYSTEM METHODS
    // ==========================================

    /**
     * Subscription'ı sil ve zinciri yeniden düzenle
     * Silinen subscription'dan sonraki tüm subscription'ların tarihlerini geriye kaydır
     *
     * @return bool
     */
    public function deleteAndRechain(): bool
    {
        return \DB::transaction(function () {
            $userId = $this->user_id;
            $deletedStart = $this->current_period_start;
            $deletedEnd = $this->current_period_end;

            // Silinen subscription'ın süresini hesapla
            $deletedDuration = $deletedStart && $deletedEnd
                ? $deletedEnd->diffInDays($deletedStart)
                : 0;

            // Silinen subscription'dan SONRA başlayan tüm subscription'ları bul
            $laterSubscriptions = self::where('user_id', $userId)
                ->where('subscription_id', '!=', $this->subscription_id)
                ->where('current_period_start', '>=', $deletedStart)
                ->whereIn('status', ['active', 'pending'])
                ->orderBy('current_period_start', 'asc')
                ->get();

            // Tarihleri geriye kaydır
            foreach ($laterSubscriptions as $sub) {
                $newStart = $sub->current_period_start->subDays($deletedDuration);
                $newEnd = $sub->current_period_end->subDays($deletedDuration);

                // Eğer yeni başlangıç tarihi şu anki zamandan önceyse ve pending ise active yap
                $newStatus = $sub->status;
                if ($newStatus === 'pending' && $newStart->isPast()) {
                    $newStatus = 'active';
                }

                $sub->update([
                    'current_period_start' => $newStart,
                    'current_period_end' => $newEnd,
                    'next_billing_date' => $newEnd,
                    'status' => $newStatus,
                ]);

                \Log::info('📅 Subscription tarihleri kaydırıldı', [
                    'subscription_id' => $sub->subscription_id,
                    'old_start' => $sub->getOriginal('current_period_start'),
                    'new_start' => $newStart->toDateTimeString(),
                    'old_end' => $sub->getOriginal('current_period_end'),
                    'new_end' => $newEnd->toDateTimeString(),
                    'days_shifted' => $deletedDuration,
                ]);
            }

            // Subscription'ı sil
            $this->delete();

            \Log::info('🗑️ Subscription silindi ve zincir yeniden düzenlendi', [
                'deleted_subscription_id' => $this->subscription_id,
                'user_id' => $userId,
                'duration_days' => $deletedDuration,
                'affected_subscriptions' => $laterSubscriptions->count(),
            ]);

            // Kullanıcının subscription_expires_at değerini yeniden hesapla
            $user = User::find($userId);
            if ($user) {
                $user->recalculateSubscriptionExpiry();
            }

            return true;
        });
    }

    /**
     * Kullanıcının subscription zincirindeki sırasını al
     * 1 = aktif, 2 = ilk bekleyen, 3 = ikinci bekleyen...
     */
    public function getChainPositionAttribute(): int
    {
        if ($this->status === 'active') {
            return 1;
        }

        if ($this->status !== 'pending') {
            return 0; // Zincirde değil
        }

        $position = self::where('user_id', $this->user_id)
            ->whereIn('status', ['active', 'pending'])
            ->where('current_period_start', '<', $this->current_period_start)
            ->count();

        return $position + 1;
    }

    /**
     * Bu subscription silinebilir mi?
     * Sadece pending veya pending_payment durumundakiler silinebilir
     * Active subscription silinemez (önce cancel edilmeli)
     */
    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['pending', 'pending_payment']);
    }
}
