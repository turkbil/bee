<?php

namespace Modules\ReviewSystem\App\Exceptions;

class ReviewSystemCreationException extends ReviewSystemException
{
    public function getErrorType(): string
    {
        return 'reviewsystem_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'ReviewSystem creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'ReviewSystem creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}
