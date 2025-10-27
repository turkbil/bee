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
        if (Schema::hasTable('shop_quote_items')) {
            return;
        }

        Schema::create('shop_quote_items', function (Blueprint $table) {
            // Primary Key
            $table->id('quote_item_id');

            // Relations
            $table->foreignId('quote_id')->comment('Teklif ID - shop_quotes ilişkisi');
            $table->foreignId('product_id')->nullable()->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi');

            // Product Info (Snapshot)
            $table->string('sku')->nullable()->comment('Ürün SKU (snapshot)');
            $table->json('product_name')->comment('Ürün adı (JSON snapshot)');
            $table->json('product_description')->nullable()->comment('Ürün açıklaması (JSON)');

            // Pricing
            $table->decimal('unit_price', 12, 2)->comment('Birim fiyat (₺)');
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('İndirim yüzdesi (%)');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('İndirim tutarı (₺)');
            $table->decimal('final_price', 12, 2)->comment('İndirimli fiyat (₺)');

            // Quantity
            $table->integer('quantity')->default(1)->comment('Adet');

            // Line Total
            $table->decimal('subtotal', 12, 2)->comment('Ara toplam (₺)');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Vergi tutarı (₺)');
            $table->decimal('total', 12, 2)->comment('Satır toplamı (₺)');

            // Tax
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Vergi oranı (%)');

            // Customization
            $table->json('options')->nullable()->comment('Seçenekler/Özelleştirme (JSON)');
            $table->text('special_notes')->nullable()->comment('Özel notlar');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('quote_id', 'idx_quote');
            $table->index('product_id', 'idx_product');
            $table->index('product_variant_id', 'idx_variant');

            // Foreign Keys
            $table->foreign('quote_id')
                  ->references('quote_id')
                  ->on('shop_quotes')
                  ->onDelete('cascade')
                  ->comment('Teklif silinirse ürünleri de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse ID null olur ama snapshot kalır');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('set null')
                  ->comment('Varyant silinirse ID null olur ama snapshot kalır');
        })
        ->comment('Teklif ürünleri - Teklifte yer alan her bir ürün');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_quote_items');
    }
};
