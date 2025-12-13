<?php

declare(strict_types=1);

namespace Modules\Cart\App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends BaseModel
{
    use HasFactory;

    protected $table = 'cart_order_items';
    protected $primaryKey = 'order_item_id';

    // BaseModel'deki is_active default'unu devre dışı bırak (cart_order_items tablosunda yok)
    protected $attributes = [];

    protected $fillable = [
        'order_id',
        'orderable_type',
        'orderable_id',
        'item_title',
        'item_sku',
        'item_image',
        'item_description',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'total',
        'original_currency',
        'original_price',
        'conversion_rate',
        'status',
        'is_digital',
        'download_url',
        'download_count',
        'download_expires_at',
        'options',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'original_price' => 'decimal:2',
        'conversion_rate' => 'decimal:4',
        'is_digital' => 'boolean',
        'download_count' => 'integer',
        'download_expires_at' => 'datetime',
        'options' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Order relation
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Polymorphic relation to the orderable item
     */
    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Recalculate item totals
     */
    public function recalculate(): void
    {
        $this->subtotal = ($this->unit_price - $this->discount_amount) * $this->quantity;
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    /**
     * Create from cart item
     */
    public static function createFromCartItem(CartItem $cartItem, int $orderId): self
    {
        return self::create([
            'order_id' => $orderId,
            'orderable_type' => $cartItem->cartable_type,
            'orderable_id' => $cartItem->cartable_id,
            'item_title' => $cartItem->item_title ?? $cartItem->item_name,
            'item_sku' => $cartItem->item_sku,
            'item_image' => $cartItem->item_image,
            'quantity' => $cartItem->quantity,
            'unit_price' => $cartItem->unit_price,
            'discount_amount' => $cartItem->discount_amount ?? 0,
            'tax_rate' => $cartItem->tax_rate ?? 0,
            'tax_amount' => $cartItem->tax_amount ?? 0,
            'subtotal' => $cartItem->subtotal,
            'total' => $cartItem->total ?? $cartItem->subtotal,
            'original_currency' => $cartItem->original_currency,
            'original_price' => $cartItem->original_price,
            'conversion_rate' => $cartItem->conversion_rate ?? 1,
            'is_digital' => false, // TODO: Cartable'dan al
        ]);
    }

    /**
     * Increment download count
     */
    public function incrementDownload(): void
    {
        $this->download_count++;
        $this->save();
    }

    /**
     * Check if download is available
     */
    public function canDownload(): bool
    {
        if (!$this->is_digital || !$this->download_url) {
            return false;
        }

        if ($this->download_expires_at && $this->download_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Scopes
     */
    public function scopeDigital($query)
    {
        return $query->where('is_digital', true);
    }

    public function scopePhysical($query)
    {
        return $query->where('is_digital', false);
    }

    /**
     * Product accessor (alias for orderable - backward compatibility)
     */
    public function getProductAttribute()
    {
        return $this->orderable;
    }

    /**
     * Product name accessor
     */
    public function getProductNameAttribute(): string
    {
        return $this->item_title ?? $this->orderable?->getTranslated('title') ?? 'Ürün';
    }

    /**
     * Total price accessor
     */
    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->total ?? $this->subtotal ?? 0);
    }
}
