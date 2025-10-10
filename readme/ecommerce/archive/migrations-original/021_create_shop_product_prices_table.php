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
        if (Schema::hasTable('shop_product_prices')) {
            return;
        }

        Schema::create('shop_product_prices', function (Blueprint $table) {
            // Primary Key
            $table->id('product_price_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi');
            $table->foreignId('price_list_id')->nullable()->comment('Fiyat listesi ID - shop_price_lists ilişkisi (null ise base price)');

            // Pricing
            $table->decimal('price', 12, 2)->comment('Fiyat (₺)');
            $table->decimal('compare_at_price', 12, 2)->nullable()->comment('Karşılaştırma fiyatı (₺) - İndirim öncesi fiyat');
            $table->decimal('cost_price', 12, 2)->nullable()->comment('Maliyet fiyatı (₺) - Kar hesabı için');

            // Currency
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');

            // Quantity-based Pricing (Basamaklı fiyatlandırma)
            $table->integer('min_quantity')->default(1)->comment('Minimum miktar (bu fiyat için)');
            $table->integer('max_quantity')->nullable()->comment('Maximum miktar (null ise sınırsız)');

            // Tax
            $table->boolean('price_includes_tax')->default(false)->comment('Fiyat vergi dahil mi?');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Vergi oranı (%)');

            // Validity Period
            $table->timestamp('valid_from')->nullable()->comment('Geçerlilik başlangıç tarihi');
            $table->timestamp('valid_until')->nullable()->comment('Geçerlilik bitiş tarihi');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('product_variant_id', 'idx_variant');
            $table->index('price_list_id', 'idx_price_list');
            $table->index('is_active', 'idx_active');
            $table->index(['product_id', 'price_list_id'], 'idx_product_pricelist');
            $table->index(['product_id', 'min_quantity'], 'idx_product_quantity');
            $table->index(['valid_from', 'valid_until'], 'idx_validity');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse fiyatları da silinir');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('cascade')
                  ->comment('Varyant silinirse fiyatları da silinir');

            $table->foreign('price_list_id')
                  ->references('id')
                  ->on('shop_price_lists')
                  ->onDelete('cascade')
                  ->comment('Fiyat listesi silinirse fiyatlar da silinir');
        })
        ->comment('Ürün fiyatları - Ürün/varyant bazlı fiyatlar ve basamaklı fiyatlandırma');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_prices');
    }
};
