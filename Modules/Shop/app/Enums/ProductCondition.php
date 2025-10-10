<?php

declare(strict_types=1);

namespace Modules\Shop\App\Enums;

enum ProductCondition: string
{
    case NEW = 'new';
    case USED = 'used';
    case REFURBISHED = 'refurbished';

    public function label(): string
    {
        return match ($this) {
            self::NEW => __('shop::enums.product_condition.new'),
            self::USED => __('shop::enums.product_condition.used'),
            self::REFURBISHED => __('shop::enums.product_condition.refurbished'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
