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
        if (Schema::hasTable('shop_product_tags')) {
            return;
        }

        Schema::create('shop_product_tags', function (Blueprint $table) {
            // Primary Key
            $table->id('product_tag_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('tag_id')->comment('Etiket ID - shop_tags ilişkisi');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('tag_id', 'idx_tag');
            $table->unique(['product_id', 'tag_id'], 'unique_product_tag');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse etiket ilişkileri de silinir');

            $table->foreign('tag_id')
                  ->references('tag_id')
                  ->on('shop_tags')
                  ->onDelete('cascade')
                  ->comment('Etiket silinirse ilişkiler de silinir');
        })
        ->comment('Ürün-Etiket ilişkileri - Pivot tablo');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_tags');
    }
};
