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
        if (Schema::hasTable('shop_product_variants')) {
            return;
        }

        Schema::create('shop_product_variants', function (Blueprint $table) {
            // Primary Key
            $table->id('variant_id');

            // Relations
            $table->unsignedBigInteger('product_id')->comment('Ana ürün ID - shop_products ilişkisi');

            // Variant Identifiers
            $table->string('sku')->unique()->comment('Varyant SKU');
            $table->string('barcode')->nullable()->comment('Varyant barkod');

            // Variant Info - JSON çoklu dil
            $table->json('title')->comment('Varyant adı: {"tr": "Varyant Adı", "en": "Variant Name", "vs.": "..."}');
            $table->json('option_values')->comment('Seçenek değerleri: {"option1":"value1","option2":"value2", "vs.": "..."}');

            // Pricing
            $table->decimal('price_modifier', 12, 2)->default(0)->comment('Fiyat değişimi (+ veya - tutar) - Ana ürüne eklenir');
            $table->decimal('cost_price', 12, 2)->nullable()->comment('Varyant maliyet fiyatı');

            // Stock
            $table->integer('stock_quantity')->default(0)->comment('Varyant stok miktarı');
            $table->integer('reserved_quantity')->default(0)->comment('Rezerve edilen miktar');

            // Physical Properties
            $table->decimal('weight', 10, 2)->nullable()->comment('Varyant ağırlığı (kg) - Ana üründen farklıysa');
            $table->json('dimensions')->nullable()->comment('Varyant boyutları - Ana üründen farklıysa: {"length":100,"width":50,"height":30}');

            // Media
            $table->string('image_url')->nullable()->comment('Varyant görseli - Ana üründen farklıysa');
            $table->json('images')->nullable()->comment('Varyant görselleri (JSON array)');

            // Display Options
            $table->boolean('is_default')->default(false)->index()->comment('Varsayılan varyant mı?');
            $table->boolean('is_active')->default(true)->index()->comment('Aktif/Pasif durumu');
            $table->integer('sort_order')->default(0)->index()->comment('Sıralama düzeni');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('product_id');
            $table->index('sku');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['product_id', 'is_active'], 'shop_product_variants_product_active_idx');
            $table->index(['product_id', 'is_default'], 'shop_product_variants_product_default_idx');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_variants');
    }
};
