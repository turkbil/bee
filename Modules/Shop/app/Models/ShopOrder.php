<?php

namespace Modules\Shop\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrder extends Model
{
    protected $table = 'shop_orders';

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
}
