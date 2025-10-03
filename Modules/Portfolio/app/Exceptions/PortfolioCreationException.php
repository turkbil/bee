<?php

namespace Modules\Portfolio\App\Exceptions;

class PortfolioCreationException extends PageException
{
    public function getErrorType(): string
    {
        return 'page_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'Portfolio creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'Portfolio creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}