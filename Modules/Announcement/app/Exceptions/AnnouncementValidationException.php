<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Exceptions;

use Exception;

class AnnouncementValidationException extends Exception
{
    public function __construct(string $message = 'Announcement validation failed', int $code = 422, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }
    
    protected array $errors = [];
    
    public function getErrors(): array
    {
        return $this->errors;
    }
}