<?php

namespace Modules\Muzibu\App\Exceptions;

class MuzibuCreationException extends MuzibuException
{
    public function getErrorType(): string
    {
        return 'muzibu_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'Muzibu creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'Muzibu creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}
