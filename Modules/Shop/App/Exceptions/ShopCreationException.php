<?php

namespace Modules\Shop\App\Exceptions;

class ShopCreationException extends ShopException
{
    public function getErrorType(): string
    {
        return 'shop_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'Shop creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'Shop creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}
