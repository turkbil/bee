<?php

namespace Modules\Payment\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'payable_id',
        'payable_type',
        'payment_method_id',
        'payment_number',
        'payment_type',
        'amount',
        'currency',
        'exchange_rate',
        'amount_in_base_currency',
        'status',
        'gateway',
        'gateway_transaction_id',
        'gateway_payment_id',
        'gateway_response',
        'card_brand',
        'card_last_four',
        'card_holder_name',
        'installment_count',
        'installment_fee',
        'refund_for_payment_id',
        'refund_reason',
        'is_verified',
        'verified_by_user_id',
        'verified_at',
        'paid_at',
        'failed_at',
        'refunded_at',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_in_base_currency' => 'decimal:2',
        'installment_fee' => 'decimal:2',
    ];

    /**
     * ID accessor - payment_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->payment_id;
    }

    /**
     * Polymorphic ilişki - Hangi model'den ödeme alındı?
     * ShopOrder, Subscription, Reservation, Invoice vb.
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Payment Method ilişkisi
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'payment_method_id');
    }

    /**
     * İade edilen ödeme ilişkisi
     */
    public function refundForPayment()
    {
        return $this->belongsTo(Payment::class, 'refund_for_payment_id', 'payment_id');
    }

    /**
     * Bu ödemenin iadeleri
     */
    public function refunds()
    {
        return $this->hasMany(Payment::class, 'refund_for_payment_id', 'payment_id');
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeByPayableType($query, $type)
    {
        return $query->where('payable_type', $type);
    }

    /**
     * Ödeme numarası otomatik oluştur
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = 'PAY-' . date('Y') . '-' . str_pad(Payment::count() + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
