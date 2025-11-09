<?php

namespace Modules\Payment\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'payment_method_id';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'gateway',
        'gateway_mode',
        'gateway_config',
        'supports_purchase',
        'supports_subscription',
        'supports_donation',
        'fixed_fee',
        'percentage_fee',
        'min_amount',
        'max_amount',
        'supports_installment',
        'max_installments',
        'installment_options',
        'supported_currencies',
        'icon',
        'logo_url',
        'sort_order',
        'is_active',
        'requires_verification',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'gateway_config' => 'array',
        'supports_purchase' => 'boolean',
        'supports_subscription' => 'boolean',
        'supports_donation' => 'boolean',
        'supports_installment' => 'boolean',
        'installment_options' => 'array',
        'supported_currencies' => 'array',
        'is_active' => 'boolean',
        'requires_verification' => 'boolean',
        'fixed_fee' => 'decimal:2',
        'percentage_fee' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
    ];

    /**
     * ID accessor - payment_method_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->payment_method_id;
    }

    /**
     * Bu ödeme yöntemine ait ödemeler
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_method_id', 'payment_method_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopePaytr($query)
    {
        return $query->where('gateway', 'paytr');
    }

    public function scopeStripe($query)
    {
        return $query->where('gateway', 'stripe');
    }

    /**
     * Çoklu dil desteği için getter method
     */
    public function getTranslated($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $value = $this->$field;

        if (is_array($value) && isset($value[$locale])) {
            return $value[$locale];
        }

        // Fallback to first available language
        if (is_array($value) && !empty($value)) {
            return array_values($value)[0];
        }

        return $value;
    }

    /**
     * Taksit ücretini hesapla
     */
    public function calculateInstallmentFee($amount, $installmentCount = 1)
    {
        if (!$this->supports_installment || $installmentCount <= 1) {
            return 0;
        }

        // installment_options örneği: {"2": 2.5, "3": 3.5, "6": 5.0}
        $installmentOptions = $this->installment_options ?? [];

        if (isset($installmentOptions[$installmentCount])) {
            $rate = $installmentOptions[$installmentCount];
            return round($amount * ($rate / 100), 2);
        }

        return 0;
    }

    /**
     * Toplam ücret hesapla (sabit + yüzde)
     */
    public function calculateTotalFee($amount)
    {
        $fixedFee = $this->fixed_fee ?? 0;
        $percentageFee = ($amount * ($this->percentage_fee / 100)) ?? 0;

        return round($fixedFee + $percentageFee, 2);
    }
}
