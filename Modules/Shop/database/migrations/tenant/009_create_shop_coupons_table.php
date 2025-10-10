<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('shop_coupons')) {
            return;
        }

        Schema::create('shop_coupons', function (Blueprint $table) {
            $table->comment('Kuponlar - İndirim kuponları ve promosyon kodları');

            // Primary Key
            $table->id('coupon_id');

            // Basic Info
            $table->json('title')->comment('Kupon adı ({"tr":"Yaz İndirimi","en":"Summer Sale"})');
            $table->string('code')->unique()->comment('Kupon kodu (SUMMER2024)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Coupon Type
            $table->enum('coupon_type', ['percentage', 'fixed_amount', 'free_shipping', 'buy_x_get_y'])
                  ->default('percentage')
                  ->comment('Kupon tipi: percentage=Yüzde indirim, fixed_amount=Sabit tutar, free_shipping=Ücretsiz kargo, buy_x_get_y=X al Y öde');

            // Discount Values
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('İndirim yüzdesi (%) - percentage tipinde');
            $table->decimal('discount_amount', 12, 2)->nullable()->comment('İndirim tutarı (₺) - fixed_amount tipinde');
            $table->decimal('max_discount_amount', 12, 2)->nullable()->comment('Maksimum indirim tutarı (₺) - percentage için');

            // Buy X Get Y Settings
            $table->integer('buy_quantity')->nullable()->comment('Alınması gereken miktar (X)');
            $table->integer('get_quantity')->nullable()->comment('Hediye miktar (Y)');
            $table->json('applicable_product_ids')->nullable()->comment('Geçerli ürün ID\'leri (JSON array)');

            // Usage Limits
            $table->integer('usage_limit_total')->nullable()->comment('Toplam kullanım limiti (null ise sınırsız)');
            $table->integer('usage_limit_per_customer')->default(1)->comment('Müşteri başına kullanım limiti');
            $table->integer('used_count')->default(0)->comment('Kullanım sayısı');

            // Conditions
            $table->decimal('minimum_order_amount', 12, 2)->nullable()->comment('Minimum sipariş tutarı (₺)');
            $table->decimal('maximum_order_amount', 14, 2)->nullable()->comment('Maximum sipariş tutarı (₺)');
            $table->integer('minimum_items')->nullable()->comment('Minimum ürün adedi');

            // Scope (Nerelerde geçerli)
            $table->enum('applies_to', ['all', 'categories', 'products', 'brands'])
                  ->default('all')
                  ->comment('Nerelerde geçerli: all=Tüm ürünler, categories=Kategoriler, products=Belirli ürünler, brands=Markalar');

            $table->json('category_ids')->nullable()->comment('Kategori ID\'leri (JSON array)');
            $table->json('product_ids')->nullable()->comment('Ürün ID\'leri (JSON array)');
            $table->json('brand_ids')->nullable()->comment('Marka ID\'leri (JSON array)');

            // Exclusions
            $table->json('excluded_category_ids')->nullable()->comment('Hariç tutulan kategori ID\'leri (JSON array)');
            $table->json('excluded_product_ids')->nullable()->comment('Hariç tutulan ürün ID\'leri (JSON array)');

            // Customer Restrictions
            $table->enum('customer_eligibility', ['all', 'specific_groups', 'specific_customers', 'new_customers', 'returning_customers'])
                  ->default('all')
                  ->comment('Müşteri yeterliliği: all=Herkes, specific_groups=Belirli gruplar, specific_customers=Belirli müşteriler, new_customers=Yeni müşteriler, returning_customers=Eski müşteriler');

            $table->json('customer_group_ids')->nullable()->comment('Müşteri grubu ID\'leri (JSON array)');
            $table->json('customer_ids')->nullable()->comment('Müşteri ID\'leri (JSON array)');

            // Validity Period
            $table->timestamp('valid_from')->nullable()->comment('Geçerlilik başlangıç tarihi');
            $table->timestamp('valid_until')->nullable()->comment('Geçerlilik bitiş tarihi');

            // Combination Rules
            $table->boolean('can_combine_with_other_coupons')->default(false)->comment('Diğer kuponlarla birlikte kullanılabilir mi?');
            $table->boolean('can_combine_with_sales')->default(true)->comment('İndirimli ürünlerde kullanılabilir mi?');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_public')->default(true)->comment('Herkese açık mı? (false ise sadece link ile)');

            // Display
            $table->json('banner_text')->nullable()->comment('Banner metni (JSON çoklu dil)');
            $table->string('banner_color')->nullable()->comment('Banner rengi (#FF5733)');

            // Additional Info
            $table->text('terms')->nullable()->comment('Kullanım şartları');
            $table->text('notes')->nullable()->comment('Admin notları');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('code');
            $table->index('coupon_type');
            $table->index('is_active');
            $table->index('is_public');
            $table->index(['valid_from', 'valid_until']);
            $table->index('used_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_coupons');
    }
};
