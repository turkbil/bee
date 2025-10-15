<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductChatPlaceholder extends Model
{
    protected $table = 'product_chat_placeholders';

    // Tenant-aware: Hangi tenant context'teyse o connection'ı kullan
    public function getConnectionName()
    {
        // Tenancy initialized değilse default connection
        if (!tenancy()->initialized) {
            return config('database.default');
        }

        // Tenant context varsa tenant'ın kendi connection'ını kullan
        // Stancl/Tenancy paketi otomatik olarak tenant DB'ye yönlendirir
        return 'tenant';
    }

    protected $fillable = [
        'product_id',
        'conversation',
        'generated_at',
    ];

    protected $casts = [
        'conversation' => 'array',
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
                'conversation' => $conversation,
                'generated_at' => now(),
            ]
        );
    }
}
