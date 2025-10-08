<?php

namespace Modules\Blog\App\Exceptions;

class BlogNotFoundException extends BlogException
{
    public function getErrorType(): string
    {
        return 'blog_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Blog with ID {$id} not found",
            context: ['blog_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Blog with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
