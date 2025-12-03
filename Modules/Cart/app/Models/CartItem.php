<?php

declare(strict_types=1);

namespace Modules\Cart\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;

class CartItem extends BaseModel
{
    use HasFactory;

    protected $table = 'cart_items';
    protected $primaryKey = 'cart_item_id';

    /**
     * Boot method - Tenant context kontrolü
     */
    protected static function booted(): void
    {
        // Query yapmadan önce tenant context kontrolü
        static::addGlobalScope('tenant_context', function ($builder) {
            // Tenant context yoksa hiçbir sonuç döndürme
            if (!function_exists('tenant') || !tenant()) {
                Log::warning('CartItem: Query attempted without tenant context - returning empty result');
                $builder->whereRaw('1 = 0');
            }
        });
    }

    protected $fillable = [
        'cart_id',
        'is_active',
        'cartable_type',
        'cartable_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'final_price',
        'subtotal',
        'tax_amount',
        'tax_rate',
        'total',
        'currency_id',
        'customization_options',
        'special_instructions',
        'metadata',
        'in_stock',
        'stock_checked_at',
        'moved_from_wishlist',
        // Display fields
        'item_title',
        'item_image',
        'item_sku',
        // Currency metadata
        'original_currency',
        'original_price',
        'conversion_rate',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total' => 'decimal:2',
        'customization_options' => 'array',
        'metadata' => 'array',
        'in_stock' => 'boolean',
        'moved_from_wishlist' => 'boolean',
        'stock_checked_at' => 'datetime',
        // Currency metadata
        'original_price' => 'decimal:2',
        'conversion_rate' => 'decimal:4',
    ];

    /**
     * Sepet ilişkisi
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    /**
     * Polymorphic item ilişkisi
     * ShopProduct, Subscription, Service gibi her türlü item olabilir
     */
    public function cartable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Ürün ilişkisi (Shop items için - backward compatibility)
     * NOT: Bu relationship optional - Shop modülü olmayan sistemlerde çalışmaz
     */
    public function product(): ?BelongsTo
    {
        if (class_exists(\Modules\Shop\App\Models\ShopProduct::class)) {
            return $this->belongsTo(\Modules\Shop\App\Models\ShopProduct::class, 'product_id', 'product_id');
        }
        return null;
    }

    /**
     * Varyant ilişkisi (Shop items için - backward compatibility)
     * NOT: Bu relationship optional - Shop modülü olmayan sistemlerde çalışmaz
     */
    public function variant(): ?BelongsTo
    {
        if (class_exists(\Modules\Shop\App\Models\ShopProductVariant::class)) {
            return $this->belongsTo(\Modules\Shop\App\Models\ShopProductVariant::class, 'product_variant_id', 'variant_id');
        }
        return null;
    }

    /**
     * Para birimi ilişkisi (Shop items için - backward compatibility)
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
     * Adet artır
     */
    public function increaseQuantity(int $amount = 1): void
    {
        $this->quantity += $amount;
        $this->recalculate();
    }

    /**
     * Adet azalt
     */
    public function decreaseQuantity(int $amount = 1): void
    {
        $this->quantity = max(1, $this->quantity - $amount);
        $this->recalculate();
    }

    /**
     * Adet güncelle
     */
    public function updateQuantity(int $quantity): void
    {
        $this->quantity = max(1, $quantity);
        $this->recalculate();
    }

    /**
     * Fiyatları yeniden hesapla
     */
    public function recalculate(): void
    {
        // Subtotal hesapla
        $this->subtotal = $this->final_price * $this->quantity;

        // Tax amount'u yeniden hesapla (quantity'ye göre)
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);

        // Total hesapla
        $this->total = $this->subtotal + $this->tax_amount;

        $this->save();
    }

    /**
     * Satır toplamı hesapla (vergi dahil)
     */
    public function getRowTotalAttribute(): float
    {
        return (float) $this->total;
    }

    /**
     * Satır toplamı hesapla (vergi hariç)
     */
    public function getRowSubtotalAttribute(): float
    {
        return (float) $this->subtotal;
    }

    /**
     * Birim fiyatı güncelle
     */
    public function updatePrice(float $unitPrice, float $discountAmount = 0): void
    {
        $this->unit_price = $unitPrice;
        $this->discount_amount = $discountAmount;
        $this->final_price = $unitPrice - $discountAmount;
        $this->recalculate();
    }

    /**
     * Stok durumunu kontrol et
     */
    public function checkStock(): bool
    {
        // Polymorphic item'ın stock tracking özelliği varsa kontrol et
        if ($this->cartable && method_exists($this->cartable, 'hasStock')) {
            $this->in_stock = $this->cartable->hasStock($this->quantity);
        }
        // Backward compatibility: Shop Product için
        elseif ($this->product_id && $this->product) {
            if (!$this->product->stock_tracking) {
                $this->in_stock = true;
            } else {
                $this->in_stock = $this->product->current_stock >= $this->quantity;
            }
        }
        // Default: Her zaman stokta varsay
        else {
            $this->in_stock = true;
        }

        $this->stock_checked_at = now();
        $this->save();

        return $this->in_stock;
    }

    /**
     * Aktif item'lar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Shop product item'ları (backward compatibility)
     */
    public function scopeShopProducts($query)
    {
        return $query->where('cartable_type', \Modules\Shop\App\Models\ShopProduct::class);
    }

    /**
     * Item adını al (polymorphic)
     */
    public function getItemNameAttribute(): string
    {
        if ($this->cartable && method_exists($this->cartable, 'getTranslated')) {
            return $this->cartable->getTranslated('title', app()->getLocale());
        } elseif ($this->product) {
            return $this->product->getTranslated('title', app()->getLocale());
        }
        return 'Unknown Item';
    }

    /**
     * Bu item kargo gerektiriyor mu?
     * SubscriptionPlan, dijital ürünler = false
     * ShopProduct, fiziksel ürünler = true
     */
    public function requiresShipping(): bool
    {
        // Dijital ürün türleri - kargo gerektirmez
        $digitalTypes = [
            'Modules\\Subscription\\App\\Models\\SubscriptionPlan',
            'Modules\\Subscription\\App\\Models\\Subscription',
        ];

        // cartable_type kontrolü
        if (in_array($this->cartable_type, $digitalTypes)) {
            return false;
        }

        // Cartable'ın kendi requiresShipping() metodu varsa kullan
        if ($this->cartable && method_exists($this->cartable, 'requiresShipping')) {
            return $this->cartable->requiresShipping();
        }

        // ShopProduct için is_digital kontrolü
        if ($this->product && property_exists($this->product, 'is_digital')) {
            return !$this->product->is_digital;
        }

        // Default: fiziksel ürün varsay, kargo gerektirir
        return true;
    }

    /**
     * Item resmini al (polymorphic)
     */
    public function getItemImageAttribute(): ?string
    {
        // 🎯 ÖNCELİK 1: Database'e kaydedilen snapshot image (ShopCartBridge tarafından set edilir)
        // Bu, sepete eklendiği anki görsel URL'sidir (değişmez)
        if (!empty($this->attributes['item_image'])) {
            return $this->attributes['item_image'];
        }

        // 🎯 ÖNCELİK 2: cartable'ın kendi getMainImage() method'u varsa (polymorphic)
        if ($this->cartable && method_exists($this->cartable, 'getMainImage')) {
            $mainImage = $this->cartable->getMainImage();
            if ($mainImage) {
                // Thumbmaker ile resize et
                return thumb($mainImage, 80, 80, ['scale' => 1]);
            }
        }

        // 🎯 ÖNCELİK 3: Backward compatibility - Spatie Media Library
        if ($this->cartable) {
            if (method_exists($this->cartable, 'getFirstMediaUrl')) {
                // hero collection'ını dene (YENİ SYSTEM)
                $mediaUrl = $this->cartable->getFirstMediaUrl('hero');
                if ($mediaUrl) {
                    return $mediaUrl;
                }

                // featured_image collection'ını dene (ESKİ SYSTEM)
                $mediaUrl = $this->cartable->getFirstMediaUrl('featured_image');
                if ($mediaUrl) {
                    return $mediaUrl;
                }

                // products collection'ını dene (fallback)
                $mediaUrl = $this->cartable->getFirstMediaUrl('products');
                if ($mediaUrl) {
                    return $mediaUrl;
                }

                // default collection'ı dene (fallback)
                $mediaUrl = $this->cartable->getFirstMediaUrl();
                if ($mediaUrl) {
                    return $mediaUrl;
                }
            }
        }

        return null;
    }
}
