<?php

namespace Modules\Muzibu\App\Exceptions;

class MuzibuNotFoundException extends MuzibuException
{
    public function getErrorType(): string
    {
        return 'muzibu_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Muzibu with ID {$id} not found",
            context: ['muzibu_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Muzibu with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
