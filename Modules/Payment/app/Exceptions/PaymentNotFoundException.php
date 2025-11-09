<?php

namespace Modules\Payment\App\Exceptions;

class PaymentNotFoundException extends PaymentException
{
    public function getErrorType(): string
    {
        return 'payment_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Payment with ID {$id} not found",
            context: ['payment_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Payment with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
