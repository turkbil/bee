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
        if (Schema::hasTable('shop_product_cross_sells')) {
            return;
        }

        Schema::create('shop_product_cross_sells', function (Blueprint $table) {
            // Primary Key
            $table->id('product_cross_sell_id');

            // Relations
            $table->foreignId('product_id')->comment('Ana ürün ID - shop_products ilişkisi');
            $table->foreignId('cross_sell_product_id')->comment('Çapraz satış ürün ID - shop_products ilişkisi');

            // Type
            $table->enum('type', ['cross_sell', 'up_sell', 'related'])
                  ->default('cross_sell')
                  ->comment('Tip: cross_sell=Çapraz satış, up_sell=Üst satış, related=İlgili ürün');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('cross_sell_product_id', 'idx_cross_sell');
            $table->index('type', 'idx_type');
            $table->unique(['product_id', 'cross_sell_product_id', 'type'], 'unique_cross_sell');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ana ürün silinirse ilişki de silinir');

            $table->foreign('cross_sell_product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Çapraz satış ürünü silinirse ilişki de silinir');
        })
        ->comment('Çapraz satış - İlgili ürünler, upsell, cross-sell');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_cross_sells');
    }
};
