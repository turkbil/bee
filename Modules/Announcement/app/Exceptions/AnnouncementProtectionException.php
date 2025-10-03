<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Exceptions;

use Exception;

/**
 * Announcement Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class AnnouncementProtectionException extends Exception
{
    /**
     * Korumalı slug exception
     */
    public static function protectedSlug(string $slug): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor ve silinemez: {$slug}"
        );
    }

    /**
     * Korumalı sayfa exception
     */
    public static function protectedPage(int $pageId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$pageId}"
        );
    }
}
