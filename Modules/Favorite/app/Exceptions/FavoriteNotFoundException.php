<?php

namespace Modules\Favorite\App\Exceptions;

class FavoriteNotFoundException extends FavoriteException
{
    public function getErrorType(): string
    {
        return 'favorite_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Favorite with ID {$id} not found",
            context: ['favorite_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Favorite with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
