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
        if (Schema::hasTable('shop_product_attributes')) {
            return;
        }

        Schema::create('shop_product_attributes', function (Blueprint $table) {
            // Primary Key
            $table->id('product_attribute_id');

            // Relations
            $table->unsignedBigInteger('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->unsignedBigInteger('attribute_id')->comment('Özellik ID - shop_attributes ilişkisi');

            // Attribute Value
            $table->json('value')->comment('Özellik değeri: {"tr":"Değer","en":"Value","vs.":"..."} veya basit string/number');
            $table->text('value_text')->nullable()->comment('Metin değeri (arama için)');
            $table->decimal('value_numeric', 12, 2)->nullable()->comment('Sayısal değer (filtreleme ve sıralama için)');

            // Display
            $table->integer('sort_order')->default(0)->index()->comment('Sıralama düzeni');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('attribute_id');
            $table->index('value_numeric');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index(['product_id', 'attribute_id'], 'shop_product_attributes_prod_attr_idx');
            $table->unique(['product_id', 'attribute_id'], 'shop_product_attributes_unique_prod_attr');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade');

            $table->foreign('attribute_id')
                  ->references('attribute_id')
                  ->on('shop_attributes')
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
        Schema::dropIfExists('shop_product_attributes');
    }
};
