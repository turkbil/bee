<?php

declare(strict_types=1);

namespace Modules\Blog\App\Exceptions;

use Exception;

/**
 * Blog Protection Exception
 *
 * Korumalı sayfalar için kullanılan custom exception sınıfı.
 */
class BlogProtectionException extends Exception
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
    public static function protectedPage(int $blogId): self
    {
        return new self(
            "Bu sayfa sistem tarafından korunuyor: #{$blogId}"
        );
    }
}
