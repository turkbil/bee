<?php

namespace Modules\MenuManagement\App\Exceptions;

class MenuItemNotFoundException extends MenuException
{
    public function getErrorType(): string
    {
        return 'menu_item_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Menu item with ID {$id} not found",
            context: ['item_id' => $id]
        );
    }

    public static function inMenu(int $menuId, int $itemId): self
    {
        return new self(
            message: "Menu item with ID {$itemId} not found in menu {$menuId}",
            context: ['menu_id' => $menuId, 'item_id' => $itemId]
        );
    }
}