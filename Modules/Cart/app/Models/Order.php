<?php

declare(strict_types=1);

namespace Modules\Cart\App\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Order extends BaseModel
{
    use HasFactory;

    protected $table = 'cart_orders';
    protected $primaryKey = 'order_id';

    // BaseModel'deki is_active default'unu devre dışı bırak (cart_orders tablosunda yok)
    protected $attributes = [];

    protected $fillable = [
        'order_number',
        'user_id',
        'order_type',
        'order_source',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'currency',
        'payment_status',
        'paid_amount',
        'requires_shipping',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'coupon_code',
        'coupon_discount',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_company',
        'customer_tax_office',
        'customer_tax_number',
        'billing_address',
        'shipping_address',
        'agreed_terms',
        'agreed_privacy',
        'agreed_marketing',
        'customer_notes',
        'admin_notes',
        'metadata',
        'ip_address',
        'user_agent',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'requires_shipping' => 'boolean',
        'agreed_terms' => 'boolean',
        'agreed_privacy' => 'boolean',
        'agreed_marketing' => 'boolean',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'metadata' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * User relation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Payments (polymorphic)
     */
    public function payments(): MorphMany
    {
        if (class_exists(\Modules\Payment\App\Models\Payment::class)) {
            return $this->morphMany(\Modules\Payment\App\Models\Payment::class, 'payable');
        }
        return $this->morphMany(self::class, 'payable'); // Fallback
    }

    /**
     * Generate order number (PayTR: sadece alfanumerik, özel karakter yok)
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return "{$prefix}{$date}{$random}"; // Tire yok - PayTR uyumlu
    }

    /**
     * Recalculate totals
     */
    public function recalculateTotals(): void
    {
        $items = $this->items()->get();

        $this->subtotal = $items->sum('subtotal');
        $this->tax_amount = $items->sum('tax_amount');
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_cost - $this->discount_amount - $this->coupon_discount;

        $this->save();
    }

    /**
     * Check if order requires shipping
     */
    public function checkRequiresShipping(): bool
    {
        // Tüm itemlar dijitalse kargo gerekmez
        $hasPhysical = $this->items()->where('is_digital', false)->exists();
        return $hasPhysical;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(float $amount = null): void
    {
        $this->paid_amount = $amount ?? $this->total_amount;
        $this->payment_status = 'paid';
        $this->save();
    }

    /**
     * Mark as shipped
     */
    public function markAsShipped(string $trackingNumber = null): void
    {
        $this->status = 'shipped';
        $this->tracking_number = $trackingNumber;
        $this->shipped_at = now();
        $this->save();
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(): void
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Cancel order
     */
    public function cancel(string $reason = null): void
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        if ($reason) {
            $this->admin_notes = ($this->admin_notes ? $this->admin_notes . "\n" : '') . "İptal nedeni: {$reason}";
        }
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessors
     */
    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->paid_amount;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsCompletedAttribute(): bool
    {
        return in_array($this->status, ['delivered', 'completed']);
    }
}
