<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Exceptions;

use Exception;

/**
 * ReviewSystem Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class ReviewSystemProtectionException extends Exception
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
    public static function protectedPage(int $reviewsystemId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$reviewsystemId}"
        );
    }
}
