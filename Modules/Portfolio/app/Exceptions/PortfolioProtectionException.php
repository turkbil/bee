<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Exceptions;

use Exception;

/**
 * Portfolio Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class PortfolioProtectionException extends Exception
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
    public static function protectedPage(int $portfoliod): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$portfoliod}"
        );
    }
}
