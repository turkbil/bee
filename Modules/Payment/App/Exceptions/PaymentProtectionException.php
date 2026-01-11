<?php

declare(strict_types=1);

namespace Modules\Payment\App\Exceptions;

use Exception;

/**
 * Payment Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class PaymentProtectionException extends Exception
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
    public static function protectedPage(int $paymentId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$paymentId}"
        );
    }
}
