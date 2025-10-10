<?php

namespace Modules\Shop\App\Exceptions;

class ShopNotFoundException extends ShopException
{
    public function getErrorType(): string
    {
        return 'shop_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Shop with ID {$id} not found",
            context: ['shop_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Shop with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
