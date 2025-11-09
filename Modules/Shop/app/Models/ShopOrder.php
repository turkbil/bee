<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Payment\App\Contracts\Payable;

class ShopOrder extends Model implements Payable
{
    protected $table = 'shop_orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'tenant_id',
        'customer_id', // ShopCustomer ilişkisi
        'order_number',

        // İletişim bilgileri (snapshot)
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_company',
        'customer_tax_office',
        'customer_tax_number',

        // Teslimat bilgileri (snapshot)
        'shipping_address',
        'shipping_city',
        'shipping_district',
        'shipping_postal_code',

        'notes',
        'subtotal',
        'tax_amount',
        'total',
        'status',
        'payment_status',

        // KVKK/GDPR
        'agreed_kvkk',
        'agreed_distance_selling',
        'agreed_preliminary_info',
        'agreed_marketing',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'agreed_kvkk' => 'boolean',
        'agreed_distance_selling' => 'boolean',
        'agreed_preliminary_info' => 'boolean',
        'agreed_marketing' => 'boolean',
    ];

    /**
     * İlişki: Müşteri
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id', 'customer_id');
    }

    /**
     * İlişki: Sipariş kalemleri
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class, 'order_id');
    }

    /**
     * İlişki: Ödemeler (Polymorphic)
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(\Modules\Payment\App\Models\Payment::class, 'payable');
    }

    // Payable Interface Implementation

    public function getPaymentAmount(): float
    {
        return (float) $this->total;
    }

    public function getPaymentCurrency(): string
    {
        return 'TRY'; // Shop için default TRY
    }

    public function getPaymentCustomer(): array
    {
        return [
            'name' => $this->customer_name ?? 'Misafir',
            'email' => $this->customer_email ?? '',
            'phone' => $this->customer_phone ?? '',
            'address' => $this->shipping_address ?? '',
            'city' => $this->shipping_city ?? '',
        ];
    }

    public function getPaymentBasket(): array
    {
        $basket = [];
        foreach ($this->items as $item) {
            $basket[] = [
                'name' => $item->product_title ?? 'Ürün',
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
            ];
        }
        return $basket;
    }

    public function getPaymentDescription(): string
    {
        return "Sipariş #" . $this->order_number;
    }

    public function onPaymentCompleted($payment): void
    {
        $this->update([
            'payment_status' => 'paid',
            'status' => 'processing', // Sipariş işleme alınacak
        ]);
    }

    public function onPaymentFailed($payment): void
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }

    public function onPaymentCancelled($payment): void
    {
        $this->update([
            'payment_status' => 'cancelled',
        ]);
    }
}
