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
        if (Schema::hasTable('shop_coupon_usages')) {
            return;
        }

        Schema::create('shop_coupon_usages', function (Blueprint $table) {
            // Primary Key
            $table->id('coupon_usage_id');

            // Relations
            $table->foreignId('coupon_id')->comment('Kupon ID - shop_coupons ilişkisi');
            $table->foreignId('order_id')->nullable()->comment('Sipariş ID - shop_orders ilişkisi');
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');

            // Usage Info
            $table->string('coupon_code')->comment('Kullanılan kupon kodu (snapshot)');
            $table->decimal('discount_amount', 12, 2)->comment('İndirim tutarı (₺)');
            $table->decimal('order_amount', 14, 2)->nullable()->comment('Sipariş tutarı (₺)');

            // Status
            $table->enum('status', ['applied', 'used', 'refunded', 'cancelled'])
                  ->default('applied')
                  ->comment('Kullanım durumu: applied=Uygulandı (henüz sipariş tamamlanmadı), used=Kullanıldı, refunded=İade edildi, cancelled=İptal edildi');

            // Timestamps
            $table->timestamp('used_at')->nullable()->comment('Kullanım tarihi');
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('coupon_id', 'idx_coupon');
            $table->index('order_id', 'idx_order');
            $table->index('customer_id', 'idx_customer');
            $table->index('coupon_code', 'idx_code');
            $table->index('status', 'idx_status');
            $table->index('used_at', 'idx_used_at');
            $table->index(['customer_id', 'coupon_id'], 'idx_customer_coupon');

            // Foreign Keys
            $table->foreign('coupon_id')
                  ->references('coupon_id')
                  ->on('shop_coupons')
                  ->onDelete('cascade')
                  ->comment('Kupon silinirse kullanım kayıtları da silinir');

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur ama kayıt kalır');

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama kayıt kalır');
        })
        ->comment('Kupon kullanımları - Kuponların kullanım geçmişi ve limitleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_coupon_usages');
    }
};
