<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Exceptions;

use Exception;

class AnnouncementNotFoundException extends Exception
{
    public function __construct(string $identifier = '', int $code = 404)
    {
        $message = $identifier 
            ? "Announcement not found with identifier: {$identifier}"
            : "Announcement not found";
            
        parent::__construct($message, $code);
    }
}