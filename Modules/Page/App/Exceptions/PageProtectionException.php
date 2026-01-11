<?php

declare(strict_types=1);

namespace Modules\Page\App\Exceptions;

use Exception;

/**
 * Page Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class PageProtectionException extends Exception
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
