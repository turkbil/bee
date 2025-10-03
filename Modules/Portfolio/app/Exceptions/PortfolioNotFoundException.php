<?php

namespace Modules\Portfolio\App\Exceptions;

class PortfolioNotFoundException extends PageException
{
    public function getErrorType(): string
    {
        return 'page_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Portfolio with ID {$id} not found",
            context: ['portfolio_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Portfolio with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}