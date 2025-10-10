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
        if (Schema::hasTable('shop_comparisons')) {
            return;
        }

        Schema::create('shop_comparisons', function (Blueprint $table) {
            // Primary Key
            $table->id('comparison_id');

            // Relations
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi (null ise misafir)');
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');

            // Guest Session (for non-logged users)
            $table->string('session_id')->nullable()->comment('Oturum ID (misafir kullanıcılar için)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('product_id', 'idx_product');
            $table->index('session_id', 'idx_session');
            $table->index(['customer_id', 'product_id'], 'idx_customer_product');
            $table->index(['session_id', 'product_id'], 'idx_session_product');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse karşılaştırma listesi de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse karşılaştırmadan da silinir');
        })
        ->comment('Ürün karşılaştırma - Müşterilerin yan yana karşılaştırdığı ürünler');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_comparisons');
    }
};
