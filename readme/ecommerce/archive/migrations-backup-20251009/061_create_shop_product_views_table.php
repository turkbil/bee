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
        if (Schema::hasTable('shop_product_views')) {
            return;
        }

        Schema::create('shop_product_views', function (Blueprint $table) {
            // Primary Key
            $table->id('product_view_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi (null ise misafir)');

            // Session Info
            $table->string('session_id')->nullable()->comment('Oturum ID (misafir için)');

            // View Info
            $table->integer('view_duration')->default(0)->comment('Görüntüleme süresi (saniye)');
            $table->boolean('converted_to_cart')->default(false)->comment('Sepete eklendi mi?');
            $table->boolean('converted_to_purchase')->default(false)->comment('Satın alındı mı?');

            // Device & Location
            $table->string('device_type')->nullable()->comment('Cihaz tipi (desktop, mobile, tablet)');
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');

            // Referrer
            $table->string('referrer_url')->nullable()->comment('Yönlendiren URL');
            $table->string('utm_source')->nullable()->comment('UTM kaynak');
            $table->string('utm_medium')->nullable()->comment('UTM medyum');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('customer_id', 'idx_customer');
            $table->index('session_id', 'idx_session');
            $table->index('created_at', 'idx_created');
            $table->index(['product_id', 'created_at'], 'idx_product_date');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse görüntüleme kayıtları da silinir');

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur');
        })
        ->comment('Ürün görüntülenmeleri - Ürün detay sayfası görüntüleme istatistikleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_views');
    }
};
