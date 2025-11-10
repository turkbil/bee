<?php

namespace Modules\Favorite\App\Exceptions;

class FavoriteCreationException extends FavoriteException
{
    public function getErrorType(): string
    {
        return 'favorite_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'Favorite creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'Favorite creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}
