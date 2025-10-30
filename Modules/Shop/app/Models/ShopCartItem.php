<?php

declare(strict_types=1);

namespace Modules\Shop\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCartItem extends BaseModel
{
    use HasFactory;

    protected $table = 'shop_cart_items';
    protected $primaryKey = 'cart_item_id';

    protected $fillable = [
        'cart_id',
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
        'customization_options',
        'special_instructions',
        'in_stock',
        'stock_checked_at',
        'moved_from_wishlist',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total' => 'decimal:2',
        'customization_options' => 'array',
        'in_stock' => 'boolean',
        'moved_from_wishlist' => 'boolean',
        'stock_checked_at' => 'datetime',
    ];

    /**
     * Sepet ilişkisi
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShopCart::class, 'cart_id', 'cart_id');
    }

    /**
     * Ürün ilişkisi
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'product_id');
    }

    /**
     * Varyant ilişkisi
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ShopProductVariant::class, 'product_variant_id', 'variant_id');
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
        $this->subtotal = $this->final_price * $this->quantity;
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
        $product = $this->product;

        if (!$product || !$product->stock_tracking) {
            $this->in_stock = true;
        } else {
            $this->in_stock = $product->current_stock >= $this->quantity;
        }

        $this->stock_checked_at = now();
        $this->save();

        return $this->in_stock;
    }
}
