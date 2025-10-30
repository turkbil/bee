<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCustomerAddress extends Model
{
    use SoftDeletes;

    protected $table = 'shop_customer_addresses';
    protected $primaryKey = 'address_id';

    protected $fillable = [
        'customer_id',
        'address_type', // billing, shipping, both
        'first_name',
        'last_name',
        'company_name',
        'tax_office',
        'tax_number',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'neighborhood',
        'district',
        'city',
        'postal_code',
        'country_code',
        'is_default_billing',
        'is_default_shipping',
        'delivery_notes',
        'metadata',
    ];

    protected $casts = [
        'is_default_billing' => 'boolean',
        'is_default_shipping' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * İlişki: Müşteri
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id', 'customer_id');
    }

    /**
     * Scope: Fatura adresleri
     */
    public function scopeBilling($query)
    {
        return $query->whereIn('address_type', ['billing', 'both']);
    }

    /**
     * Scope: Teslimat adresleri
     */
    public function scopeShipping($query)
    {
        return $query->whereIn('address_type', ['shipping', 'both']);
    }

    /**
     * Scope: Varsayılan fatura
     */
    public function scopeDefaultBilling($query)
    {
        return $query->where('is_default_billing', true);
    }

    /**
     * Scope: Varsayılan teslimat
     */
    public function scopeDefaultShipping($query)
    {
        return $query->where('is_default_shipping', true);
    }

    /**
     * Tam isim
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Tek satır adres
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->neighborhood,
            $this->district,
            $this->city,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Adres başlığı (kullanıcı dostu)
     */
    public function getTitleAttribute(): string
    {
        if ($this->company_name) {
            return $this->company_name . ' - ' . $this->city;
        }

        return $this->full_name . ' - ' . $this->city;
    }

    /**
     * Bu adresi varsayılan fatura adresi yap
     */
    public function setAsDefaultBilling(): void
    {
        // Diğer adreslerin varsayılanını kaldır
        self::where('customer_id', $this->customer_id)
            ->where('address_id', '!=', $this->address_id)
            ->update(['is_default_billing' => false]);

        // Bu adresi varsayılan yap
        $this->update(['is_default_billing' => true]);
    }

    /**
     * Bu adresi varsayılan teslimat adresi yap
     */
    public function setAsDefaultShipping(): void
    {
        // Diğer adreslerin varsayılanını kaldır
        self::where('customer_id', $this->customer_id)
            ->where('address_id', '!=', $this->address_id)
            ->update(['is_default_shipping' => false]);

        // Bu adresi varsayılan yap
        $this->update(['is_default_shipping' => true]);
    }

    /**
     * Fatura adresi mi?
     */
    public function isBillingAddress(): bool
    {
        return in_array($this->address_type, ['billing', 'both']);
    }

    /**
     * Teslimat adresi mi?
     */
    public function isShippingAddress(): bool
    {
        return in_array($this->address_type, ['shipping', 'both']);
    }

    /**
     * Checkout için adres verisi hazırla (snapshot)
     */
    public function toCheckoutData(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'tax_office' => $this->tax_office,
            'tax_number' => $this->tax_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address_line_1 . ($this->address_line_2 ? ' ' . $this->address_line_2 : ''),
            'neighborhood' => $this->neighborhood,
            'district' => $this->district,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'country_code' => $this->country_code,
            'delivery_notes' => $this->delivery_notes,
        ];
    }
}
