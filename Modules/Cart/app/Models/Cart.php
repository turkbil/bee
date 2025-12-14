<?php

declare(strict_types=1);

namespace Modules\Cart\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Cart extends BaseModel
{
    use HasFactory;
    use \Modules\Cart\App\Traits\HasAddresses;

    protected $table = 'carts';
    protected $primaryKey = 'cart_id';

    /**
     * Boot method - Tenant context kontrolü
     */
    protected static function booted(): void
    {
        // Query yapmadan önce tenant context kontrolü
        static::addGlobalScope('tenant_context', function ($builder) {
            // Tenant context yoksa hiçbir sonuç döndürme (central DB'ye sorgu engellenir)
            if (!function_exists('tenant') || !tenant()) {
                Log::warning('Cart: Query attempted without tenant context - returning empty result');
                $builder->whereRaw('1 = 0'); // Hiçbir sonuç döndürmez
            }
        });
    }

    protected $fillable = [
        'customer_id',
        'session_id',
        'device_id',
        'status',
        'is_active',
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
        'currency_code',
        'currency_id',
        'metadata',
        'last_activity_at',
        // Address fields
        'billing_address',
        'shipping_address',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
        // Address fields
        'billing_address' => 'array',
        'shipping_address' => 'array',
    ];

    /**
     * Sepetteki ürünler
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    /**
     * Aktif ürünler
     */
    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }

    /**
     * Sepet sahibi kullanıcı (Central DB'den)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id', 'id');
    }

    /**
     * Sepet para birimi (Shop modülü varsa)
     * NOT: Bu relationship optional - Shop modülü olmayan sistemlerde çalışmaz
     */
    public function currency(): ?BelongsTo
    {
        if (class_exists(\Modules\Shop\App\Models\ShopCurrency::class)) {
            return $this->belongsTo(\Modules\Shop\App\Models\ShopCurrency::class, 'currency_id', 'currency_id');
        }
        return null;
    }

    /**
     * Aktif sepetler
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
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
                'is_active' => true,
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
                'is_active' => true,
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
        $items = $this->items()->where('is_active', true)->get();

        $this->items_count = $items->sum('quantity');
        $this->subtotal = $items->sum('subtotal');
        $this->tax_amount = $items->sum('tax_amount');
        $this->total = $this->subtotal + $this->tax_amount + $this->shipping_cost - $this->coupon_discount;

        $this->save();
    }

    /**
     * Sepeti temizle
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->items_count = 0;
        $this->subtotal = 0;
        $this->tax_amount = 0;
        $this->total = 0;
        $this->save();
    }

    /**
     * Sepeti siparişe dönüştür
     */
    public function markAsConverted(int $orderId): void
    {
        $this->status = 'converted';
        $this->converted_to_order_id = $orderId;
        $this->converted_at = now();
        $this->save();
    }

    /**
     * Sepeti terk edilmiş olarak işaretle
     */
    public function markAsAbandoned(): void
    {
        $this->status = 'abandoned';
        $this->abandoned_at = now();
        $this->save();
    }
}
