<?php

namespace Modules\Payment\App\Exceptions;

class PaymentCreationException extends PaymentException
{
    public function getErrorType(): string
    {
        return 'payment_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'Payment creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'Payment creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}
