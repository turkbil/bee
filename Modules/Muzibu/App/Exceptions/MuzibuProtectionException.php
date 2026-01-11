<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Exceptions;

use Exception;

/**
 * Muzibu Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class MuzibuProtectionException extends Exception
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
    public static function protectedPage(int $muzibuId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$muzibuId}"
        );
    }
}
