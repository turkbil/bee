<?php

declare(strict_types=1);

namespace Modules\Favorite\App\Exceptions;

use Exception;

/**
 * Favorite Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class FavoriteProtectionException extends Exception
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
    public static function protectedPage(int $favoriteId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$favoriteId}"
        );
    }
}
