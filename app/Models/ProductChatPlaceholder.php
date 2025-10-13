<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductChatPlaceholder extends Model
{
    protected $fillable = [
        'product_id',
        'conversation_json',
        'generated_at',
    ];

    protected $casts = [
        'conversation_json' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get placeholder by product ID
     */
    public static function getByProductId(string $productId): ?self
    {
        return self::where('product_id', $productId)->first();
    }

    /**
     * Create or update placeholder
     */
    public static function updateOrCreatePlaceholder(string $productId, array $conversation): self
    {
        return self::updateOrCreate(
            ['product_id' => $productId],
            [
                'conversation_json' => $conversation,
                'generated_at' => now(),
            ]
        );
    }
}
