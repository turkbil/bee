<?php

namespace Modules\Page\App\Exceptions;

class HomepageProtectionException extends PageException
{
    public function getErrorType(): string
    {
        return 'homepage_protection_violation';
    }

    public static function cannotDeactivate(int $pageId): self
    {
        return new self(
            message: 'Homepage cannot be deactivated',
            context: ['page_id' => $pageId, 'action' => 'deactivate']
        );
    }

    public static function cannotDelete(int $pageId): self
    {
        return new self(
            message: 'Homepage cannot be deleted',
            context: ['page_id' => $pageId, 'action' => 'delete']
        );
    }
}