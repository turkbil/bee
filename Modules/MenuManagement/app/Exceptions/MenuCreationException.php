<?php

namespace Modules\MenuManagement\App\Exceptions;

class MenuCreationException extends MenuException
{
    public function getErrorType(): string
    {
        return 'menu_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: "Menu creation failed due to validation errors",
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: "Menu creation failed due to database error: {$error}",
            context: ['database_error' => $error]
        );
    }

    public static function duplicateDefaultMenu(): self
    {
        return new self(
            message: "Cannot create multiple default menus for the same tenant",
            context: ['type' => 'duplicate_default']
        );
    }
}