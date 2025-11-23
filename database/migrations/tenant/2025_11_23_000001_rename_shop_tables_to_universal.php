<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Subscription plans
        if (Schema::hasTable('shop_subscription_plans') && !Schema::hasTable('subscription_plans')) {
            Schema::rename('shop_subscription_plans', 'subscription_plans');
        }

        // Subscriptions
        if (Schema::hasTable('shop_subscriptions') && !Schema::hasTable('subscriptions')) {
            Schema::rename('shop_subscriptions', 'subscriptions');
        }

        // Coupons
        if (Schema::hasTable('shop_coupons') && !Schema::hasTable('coupons')) {
            Schema::rename('shop_coupons', 'coupons');
        }

        // Coupon usages
        if (Schema::hasTable('shop_coupon_usages') && !Schema::hasTable('coupon_usages')) {
            Schema::rename('shop_coupon_usages', 'coupon_usages');
        }

        // Customer addresses
        if (Schema::hasTable('shop_customer_addresses') && !Schema::hasTable('customer_addresses')) {
            Schema::rename('shop_customer_addresses', 'customer_addresses');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Subscription plans
        if (Schema::hasTable('subscription_plans') && !Schema::hasTable('shop_subscription_plans')) {
            Schema::rename('subscription_plans', 'shop_subscription_plans');
        }

        // Subscriptions
        if (Schema::hasTable('subscriptions') && !Schema::hasTable('shop_subscriptions')) {
            Schema::rename('subscriptions', 'shop_subscriptions');
        }

        // Coupons
        if (Schema::hasTable('coupons') && !Schema::hasTable('shop_coupons')) {
            Schema::rename('coupons', 'shop_coupons');
        }

        // Coupon usages
        if (Schema::hasTable('coupon_usages') && !Schema::hasTable('shop_coupon_usages')) {
            Schema::rename('coupon_usages', 'shop_coupon_usages');
        }

        // Customer addresses
        if (Schema::hasTable('customer_addresses') && !Schema::hasTable('shop_customer_addresses')) {
            Schema::rename('customer_addresses', 'shop_customer_addresses');
        }
    }
};
