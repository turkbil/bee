<?php

declare(strict_types=1);

namespace Modules\Service\App\Exceptions;

use Exception;

/**
 * Service Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class ServiceProtectionException extends Exception
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
    public static function protectedPage(int $serviceId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$serviceId}"
        );
    }
}
