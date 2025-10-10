<?php

declare(strict_types=1);

namespace Modules\Shop\App\Exceptions;

use Exception;

/**
 * Shop Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class ShopProtectionException extends Exception
{
    /**
     * Korumalı slug exception
     */
    public static function protectedSlug(string $slug): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor ve silinemez: {$slug}"
        );
    }

    /**
     * Korumalı sayfa exception
     */
    public static function protectedPage(int $shopId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$shopId}"
        );
    }

    public static function cannotDeleteFeatured(int $productId): self
    {
        return new self(
            "Öne çıkan ürünler silinemez: #{$productId}"
        );
    }
}
