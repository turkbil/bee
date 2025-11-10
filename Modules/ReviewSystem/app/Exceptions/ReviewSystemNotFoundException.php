<?php

namespace Modules\ReviewSystem\App\Exceptions;

class ReviewSystemNotFoundException extends ReviewSystemException
{
    public function getErrorType(): string
    {
        return 'reviewsystem_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "ReviewSystem with ID {$id} not found",
            context: ['reviewsystem_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "ReviewSystem with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
