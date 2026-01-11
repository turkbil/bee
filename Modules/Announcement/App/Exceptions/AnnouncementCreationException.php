<?php

namespace Modules\Announcement\App\Exceptions;

class AnnouncementCreationException extends AnnouncementException
{
    public function getErrorType(): string
    {
        return 'announcement_creation_failed';
    }

    public static function withValidationErrors(array $errors): self
    {
        return new self(
            message: 'Announcement creation failed due to validation errors',
            context: ['validation_errors' => $errors]
        );
    }

    public static function withDatabaseError(string $error): self
    {
        return new self(
            message: 'Announcement creation failed due to database error',
            context: ['database_error' => $error]
        );
    }
}
