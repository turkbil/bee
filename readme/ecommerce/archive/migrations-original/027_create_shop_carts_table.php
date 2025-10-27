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
        if (Schema::hasTable('shop_carts')) {
            return;
        }

        Schema::create('shop_carts', function (Blueprint $table) {
            // Primary Key
            $table->id('cart_id');

            // Relations
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi (null ise misafir)');

            // Session Info (for guest users)
            $table->string('session_id')->nullable()->comment('Oturum ID (misafir kullanıcılar için)');
            $table->string('device_id')->nullable()->comment('Cihaz ID (cross-device tracking için)');

            // Cart Status
            $table->enum('status', ['active', 'abandoned', 'converted', 'merged'])
                  ->default('active')
                  ->comment('Sepet durumu: active=Aktif, abandoned=Terk edilmiş, converted=Siparişe dönüştürüldü, merged=Birleştirilmiş');

            // Totals (Cache - hesaplanmış değerler)
            $table->integer('items_count')->default(0)->comment('Toplam ürün sayısı (adet)');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Ara toplam (₺)');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('İndirim tutarı (₺)');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Vergi tutarı (₺)');
            $table->decimal('shipping_cost', 10, 2)->default(0)->comment('Kargo ücreti (₺)');
            $table->decimal('total', 12, 2)->default(0)->comment('Toplam tutar (₺)');

            // Coupon
            $table->string('coupon_code')->nullable()->comment('Uygulanan kupon kodu');
            $table->decimal('coupon_discount', 12, 2)->default(0)->comment('Kupon indirimi (₺)');

            // Conversion Tracking
            $table->foreignId('converted_to_order_id')->nullable()->comment('Dönüştürülen sipariş ID');
            $table->timestamp('converted_at')->nullable()->comment('Siparişe dönüşme tarihi');
            $table->timestamp('abandoned_at')->nullable()->comment('Terk edilme tarihi (son aktiviteden 24 saat sonra)');

            // Recovery
            $table->string('recovery_token')->nullable()->unique()->comment('Kurtarma token (e-posta linkinde kullanılır)');
            $table->timestamp('recovery_email_sent_at')->nullable()->comment('Kurtarma e-postası gönderilme tarihi');
            $table->integer('recovery_email_count')->default(0)->comment('Kaç kez kurtarma e-postası gönderildi');

            // IP & Browser
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');
            $table->text('user_agent')->nullable()->comment('Tarayıcı bilgisi');

            // Currency
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');

            // Additional Info
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON - utm params, referrer, vb)');

            // Timestamps
            $table->timestamps();
            $table->timestamp('last_activity_at')->nullable()->comment('Son aktivite tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('session_id', 'idx_session');
            $table->index('status', 'idx_status');
            $table->index('converted_to_order_id', 'idx_converted_order');
            $table->index('recovery_token', 'idx_recovery_token');
            $table->index('abandoned_at', 'idx_abandoned');
            $table->index('last_activity_at', 'idx_last_activity');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse sepeti de silinir');

            $table->foreign('converted_to_order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur');
        })
        ->comment('Alışveriş sepetleri - Aktif ve terk edilmiş sepetler');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_carts');
    }
};
