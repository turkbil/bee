<?php

namespace Modules\Blog\App\Exceptions;

class BlogCreationException extends BlogException
{
    public function getErrorType(): string
    {
        return 'blog_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'Blog creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'Blog creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}
