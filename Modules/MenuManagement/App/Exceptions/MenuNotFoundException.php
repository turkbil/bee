<?php

namespace Modules\MenuManagement\App\Exceptions;

class MenuNotFoundException extends MenuException
{
    public function getErrorType(): string
    {
        return 'menu_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Menu with ID {$id} not found",
            context: ['menu_id' => $id]
        );
    }

    public static function withSlug(string $slug): self
    {
        return new self(
            message: "Menu with slug '{$slug}' not found",
            context: ['slug' => $slug]
        );
    }

    public static function defaultMenu(): self
    {
        return new self(
            message: "Default menu not found for this tenant",
            context: ['type' => 'default_menu']
        );
    }
}