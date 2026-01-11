<?php

namespace Modules\Page\App\Exceptions;

class PageNotFoundException extends PageException
{
    public function getErrorType(): string
    {
        return 'page_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Page with ID {$id} not found",
            context: ['page_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Page with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}