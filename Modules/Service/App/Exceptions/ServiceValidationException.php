<?php

declare(strict_types=1);

namespace Modules\Service\App\Exceptions;

use Exception;

/**
 * Service Validation Exception
 *
 * Sayfa validasyon hataları için kullanılan custom exception sınıfı.
 */
class ServiceValidationException extends Exception
{
    /**
     * CSS boyutu aşıldı exception
     */
    public static function cssSizeExceeded(int $maxSize): self
    {
        return new self(
            "CSS içeriği maksimum boyutu ({$maxSize} karakter) aşıyor."
        );
    }

    /**
     * JavaScript boyutu aşıldı exception
     */
    public static function jsSizeExceeded(int $maxSize): self
    {
        return new self(
            "JavaScript içeriği maksimum boyutu ({$maxSize} karakter) aşıyor."
        );
    }

    /**
     * Başlık çok kısa exception
     */
    public static function titleTooShort(string $locale, int $minLength): self
    {
        return new self(
            "Başlık minimum {$minLength} karakter olmalıdır. ({$locale})"
        );
    }

    /**
     * Geçersiz slug exception
     */
    public static function invalidSlug(string $slug, string $locale): self
    {
        return new self(
            "Geçersiz slug formatı: '{$slug}' ({$locale})"
        );
    }

    /**
     * Slug zaten kullanımda exception
     */
    public static function slugTaken(string $slug, string $locale): self
    {
        return new self(
            "Bu slug zaten kullanımda: '{$slug}' ({$locale})"
        );
    }
}
