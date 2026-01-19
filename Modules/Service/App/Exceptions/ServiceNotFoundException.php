<?php

namespace Modules\Service\App\Exceptions;

class ServiceNotFoundException extends ServiceException
{
    public function getErrorType(): string
    {
        return 'service_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Service with ID {$id} not found",
            context: ['service_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Service with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
