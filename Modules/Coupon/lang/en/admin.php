<?php

return [
    // Menu
    'coupon_management' => 'Coupon Management',
    'menu' => 'Menu',

    // Coupons
    'coupons' => 'Coupons',
    'new_coupon' => 'New Coupon',
    'create' => 'New Coupon',
    'edit' => 'Edit Coupon',
    'code' => 'Coupon Code',
    'discount' => 'Discount',
    'usage' => 'Usage',
    'valid_until' => 'Valid Until',
    'no_coupons' => 'No coupons yet',

    // Stats
    'total_coupons' => 'Total Coupons',
    'active_coupons' => 'Active Coupons',
    'total_usage' => 'Total Usage',

    // Filters
    'all_types' => 'All Types',

    // Types
    'percentage' => 'Percentage',
    'fixed_amount' => 'Fixed Amount',
    'free_shipping' => 'Free Shipping',
    'buy_x_get_y' => 'Buy X Get Y',

    // Scope
    'applies_to' => 'Applies To',
    'scope_all' => 'All',
    'scope_shop' => 'Shop (E-commerce)',
    'scope_subscription' => 'Subscription',

    // Status
    'active' => 'Active',
    'inactive' => 'Inactive',
    'expired' => 'Expired',
    'limit_reached' => 'Limit Reached',
    'scheduled' => 'Scheduled',

    // Form
    'basic_info' => 'Basic Info',
    'generate' => 'Generate',
    'search_code' => 'Search coupon code',
    'discount_type' => 'Discount Type',
    'discount_value' => 'Discount Value',
    'limits' => 'Limits',
    'usage_limit' => 'Usage Limit',
    'usage_per_user' => 'Per User Limit',
    'used_count' => 'Used Count',
    'validity' => 'Validity',
    'starts_at' => 'Start Date',
    'expires_at' => 'End Date',
    'min_amount' => 'Min. Amount',
    'max_discount' => 'Max. Discount',
    'optional' => 'Optional',
    'unlimited' => 'Unlimited',
    'no_expiry' => 'No Expiry',

    // Step titles (UX)
    'step_code' => 'Create Coupon Code',
    'step_discount' => 'Select Discount Type',
    'step_scope' => 'Where Will It Be Used?',

    // Hints (UX)
    'code_hint' => 'Enter a memorable code or generate one automatically',
    'hint_percentage' => 'e.g. 20% off',
    'hint_fixed' => 'e.g. $50 off',
    'hint_shipping' => 'Free shipping',
    'hint_bogo' => 'Buy 2 Get 1',
    'hint_scope_all' => 'Valid everywhere',
    'hint_scope_shop' => 'Products only',
    'hint_scope_sub' => 'Subscriptions only',
    'example' => 'Example',
    'hint_min_amount' => 'Minimum cart amount (optional)',
    'hint_max_discount' => 'Maximum discount amount (for percentage)',
    'hint_usage_limit' => 'Total times it can be used',
    'hint_usage_per_user' => 'Times each user can use',
    'hint_starts_at' => 'Leave empty to start immediately',
    'hint_expires_at' => 'Leave empty for no expiry',

    // Errors
    'errors' => [
        'not_found' => 'Coupon not found',
        'invalid' => 'Invalid coupon',
        'inactive' => 'This coupon is not active',
        'expired' => 'This coupon has expired',
        'limit_reached' => 'This coupon has reached its usage limit',
        'not_started' => 'This coupon has not started yet',
        'user_limit_reached' => 'You have reached your usage limit for this coupon',
        'min_amount' => 'Minimum cart amount must be :amount',
    ],
];
