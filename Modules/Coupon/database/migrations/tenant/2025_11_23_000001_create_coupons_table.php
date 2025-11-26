<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coupons')) {
            return;
        }
        Schema::create('coupons', function (Blueprint $table) {
            $table->id('coupon_id');
            $table->json('title')->nullable();
            $table->string('code')->unique();
            $table->json('description')->nullable();
            $table->enum('coupon_type', ['percentage', 'fixed_amount', 'free_shipping', 'buy_x_get_y'])->default('percentage');
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->integer('buy_quantity')->nullable();
            $table->integer('get_quantity')->nullable();
            $table->json('applicable_product_ids')->nullable();
            $table->integer('usage_limit_total')->nullable();
            $table->integer('usage_limit_per_customer')->default(1);
            $table->integer('used_count')->default(0);
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->decimal('maximum_order_amount', 10, 2)->nullable();
            $table->integer('minimum_items')->nullable();
            $table->enum('applies_to', ['all', 'specific_products', 'specific_categories', 'specific_brands'])->default('all');
            $table->json('category_ids')->nullable();
            $table->json('product_ids')->nullable();
            $table->json('brand_ids')->nullable();
            $table->json('excluded_category_ids')->nullable();
            $table->json('excluded_product_ids')->nullable();
            $table->enum('customer_eligibility', ['all', 'specific_customers', 'specific_groups'])->default('all');
            $table->json('customer_group_ids')->nullable();
            $table->json('customer_ids')->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('can_combine_with_other_coupons')->default(false);
            $table->boolean('can_combine_with_sales')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->json('banner_text')->nullable();
            $table->string('banner_color')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
