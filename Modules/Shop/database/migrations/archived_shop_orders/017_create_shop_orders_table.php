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
        if (Schema::hasTable('shop_orders')) {
            return;
        }

        Schema::create('shop_orders', function (Blueprint $table) {
            $table->comment('Siparişler - Tüm sipariş tipleri (satış, teklif, kiralama, servis)');

            // Primary Key
            $table->id('order_id');

            // Order Number
            $table->string('order_number')->unique()->comment('Sipariş numarası (ORD-2024-00001)');

            // Customer Relation
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');

            // Order Type & Source
            $table->enum('order_type', ['sale', 'quote', 'rental', 'service'])
                  ->default('sale')
                  ->comment('Sipariş tipi: sale=Satış, quote=Teklif, rental=Kiralama, service=Servis');

            $table->enum('order_source', ['web', 'admin', 'mobile', 'api'])
                  ->default('web')
                  ->comment('Sipariş kaynağı');

            // Status
            $table->enum('status', [
                'pending',        // Beklemede
                'confirmed',      // Onaylandı
                'processing',     // İşleniyor
                'deposit_paid',   // Kapora ödendi
                'ready',          // Hazır
                'shipped',        // Kargoya verildi
                'delivered',      // Teslim edildi
                'completed',      // Tamamlandı
                'cancelled',      // İptal edildi
                'refunded'        // İade edildi
            ])->default('pending')->comment('Sipariş durumu');

            // Pricing
            $table->decimal('subtotal', 14, 2)->comment('Ara toplam (₺) - İndirim ve vergiler hariç');
            $table->decimal('discount_amount', 12, 2)->default(0)->comment('İndirim tutarı (₺)');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Vergi tutarı (₺)');
            $table->decimal('shipping_cost', 10, 2)->default(0)->comment('Kargo ücreti (₺)');
            $table->decimal('total_amount', 14, 2)->comment('Toplam tutar (₺)');
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');

            // Deposit (B2B)
            $table->boolean('deposit_required')->default(false)->comment('Kapora gerekli mi?');
            $table->decimal('deposit_amount', 12, 2)->default(0)->comment('Kapora tutarı (₺)');
            $table->boolean('deposit_paid')->default(false)->comment('Kapora ödendi mi?');
            $table->timestamp('deposit_paid_at')->nullable()->comment('Kapora ödeme tarihi');

            // Payment Info
            $table->enum('payment_status', ['pending', 'partially_paid', 'paid', 'refunded', 'failed'])
                  ->default('pending')
                  ->comment('Ödeme durumu');

            $table->decimal('paid_amount', 12, 2)->default(0)->comment('Ödenen tutar (₺)');
            $table->decimal('remaining_amount', 12, 2)->default(0)->comment('Kalan tutar (₺)');
            $table->foreignId('payment_method_id')->nullable()->comment('Ödeme yöntemi ID');

            // Shipping Info
            $table->foreignId('shipping_method_id')->nullable()->comment('Kargo yöntemi ID');
            $table->string('tracking_number')->nullable()->comment('Kargo takip numarası');
            $table->timestamp('shipped_at')->nullable()->comment('Kargoya verilme tarihi');
            $table->timestamp('delivered_at')->nullable()->comment('Teslim tarihi');

            // Coupon
            $table->string('coupon_code')->nullable()->comment('Kullanılan kupon kodu');
            $table->decimal('coupon_discount', 12, 2)->default(0)->comment('Kupon indirimi (₺)');

            // Customer Info (Snapshot)
            $table->string('customer_email')->comment('Müşteri e-posta (snapshot)');
            $table->string('customer_phone')->nullable()->comment('Müşteri telefon (snapshot)');
            $table->json('billing_address')->nullable()->comment('Fatura adresi (JSON snapshot)');
            $table->json('shipping_address')->nullable()->comment('Teslimat adresi (JSON snapshot)');

            // Additional Info
            $table->text('customer_notes')->nullable()->comment('Müşteri notu');
            $table->text('admin_notes')->nullable()->comment('Admin notu');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // IP & Browser
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');
            $table->text('user_agent')->nullable()->comment('Tarayıcı bilgisi');

            // Important Dates
            $table->timestamp('confirmed_at')->nullable()->comment('Onaylanma tarihi');
            $table->timestamp('completed_at')->nullable()->comment('Tamamlanma tarihi');
            $table->timestamp('cancelled_at')->nullable()->comment('İptal edilme tarihi');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('order_number');
            $table->index('customer_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_type');
            $table->index('total_amount');
            $table->index(['customer_id', 'status']);
            $table->index(['status', 'payment_status']);

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('set null');

            $table->foreign('payment_method_id')
                  ->references('payment_method_id')
                  ->on('shop_payment_methods')
                  ->onDelete('set null');

            // NOT: shop_shipping_methods tablosu henüz oluşturulmadı
            // Foreign key daha sonra eklenecek
            // $table->foreign('shipping_method_id')
            //       ->references('shipping_method_id')
            //       ->on('shop_shipping_methods')
            //       ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_orders');
    }
};
