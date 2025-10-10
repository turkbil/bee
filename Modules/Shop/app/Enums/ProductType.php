<?php

declare(strict_types=1);

namespace Modules\Shop\App\Enums;

enum ProductType: string
{
    case PHYSICAL = 'physical';
    case DIGITAL = 'digital';
    case SERVICE = 'service';
    case MEMBERSHIP = 'membership';
    case BUNDLE = 'bundle';

    public function label(): string
    {
        return match ($this) {
            self::PHYSICAL => __('shop::enums.product_type.physical'),
            self::DIGITAL => __('shop::enums.product_type.digital'),
            self::SERVICE => __('shop::enums.product_type.service'),
            self::MEMBERSHIP => __('shop::enums.product_type.membership'),
            self::BUNDLE => __('shop::enums.product_type.bundle'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
