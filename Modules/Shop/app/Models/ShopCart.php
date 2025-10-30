<?php

declare(strict_types=1);

namespace Modules\Shop\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShopCart extends BaseModel
{
    use HasFactory;

    protected $table = 'shop_carts';
    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'customer_id',
        'session_id',
        'device_id',
        'status',
        'items_count',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'total',
        'coupon_code',
        'coupon_discount',
        'converted_to_order_id',
        'converted_at',
        'abandoned_at',
        'recovery_token',
        'recovery_email_sent_at',
        'recovery_email_count',
        'ip_address',
        'user_agent',
        'currency_code', // Renamed from 'currency' to avoid relation conflict
        'currency_id',
        'metadata',
        'last_activity_at',
    ];

    protected $casts = [
        'items_count' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'recovery_email_count' => 'integer',
        'metadata' => 'array',
        'converted_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'recovery_email_sent_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Sepetteki ürünler
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShopCartItem::class, 'cart_id', 'cart_id');
    }

    /**
     * Sepet para birimi
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(ShopCurrency::class, 'currency_id', 'currency_id');
    }

    /**
     * Müşteri ilişkisi
     * TODO: ShopCustomer model'i oluşturulunca aktifleştir
     */
    // public function customer(): BelongsTo
    // {
    //     return $this->belongsTo(ShopCustomer::class, 'customer_id', 'customer_id');
    // }

    /**
     * Dönüştürülen sipariş
     * TODO: ShopOrder model'i oluşturulunca aktifleştir
     */
    // public function order(): BelongsTo
    // {
    //     return $this->belongsTo(ShopOrder::class, 'converted_to_order_id', 'order_id');
    // }

    /**
     * Aktif sepetler
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Terk edilmiş sepetler
     */
    public function scopeAbandoned($query)
    {
        return $query->where('status', 'abandoned');
    }

    /**
     * Misafir sepetleri
     */
    public function scopeGuest($query)
    {
        return $query->whereNull('customer_id');
    }

    /**
     * Kayıtlı kullanıcı sepetleri
     */
    public function scopeRegistered($query)
    {
        return $query->whereNotNull('customer_id');
    }

    /**
     * Session için sepet bul veya oluştur
     */
    public static function findOrCreateForSession(string $sessionId): self
    {
        return static::firstOrCreate(
            [
                'session_id' => $sessionId,
                'status' => 'active',
            ],
            [
                'currency_code' => 'TRY',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'last_activity_at' => now(),
            ]
        );
    }

    /**
     * Müşteri için sepet bul veya oluştur
     */
    public static function findOrCreateForCustomer(int $customerId): self
    {
        return static::firstOrCreate(
            [
                'customer_id' => $customerId,
                'status' => 'active',
            ],
            [
                'currency_code' => 'TRY',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'last_activity_at' => now(),
            ]
        );
    }

    /**
     * Recovery token oluştur
     */
    public function generateRecoveryToken(): string
    {
        $this->recovery_token = Str::random(64);
        $this->save();

        return $this->recovery_token;
    }

    /**
     * Son aktiviteyi güncelle
     */
    public function touchActivity(): void
    {
        $this->last_activity_at = now();
        $this->save();
    }

    /**
     * Sepet boş mu?
     */
    public function isEmpty(): bool
    {
        return $this->items_count === 0;
    }

    /**
     * Sepette ürün var mı?
     */
    public function hasItems(): bool
    {
        return $this->items_count > 0;
    }

    /**
     * Toplamları yeniden hesapla
     */
    public function recalculateTotals(): void
    {
        $items = $this->items()->with('product')->get();

        $this->items_count = $items->sum('quantity');
        $this->subtotal = $items->sum('subtotal');
        $this->tax_amount = $items->sum('tax_amount');
        $this->total = $this->subtotal + $this->tax_amount + $this->shipping_cost - $this->coupon_discount;

        $this->save();
    }
}
