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
        if (Schema::hasTable('shop_rental_contracts')) {
            return;
        }

        Schema::create('shop_rental_contracts', function (Blueprint $table) {
            // Primary Key
            $table->id('rental_contract_id');

            // Relations
            $table->foreignId('customer_id')->comment('Müşteri ID - shop_customers ilişkisi');
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('order_id')->nullable()->comment('Sipariş ID - shop_orders ilişkisi');

            // Contract Info
            $table->string('contract_number')->unique()->comment('Sözleşme numarası (RNT-2024-00001)');

            // Rental Period
            $table->enum('rental_period_type', ['hourly', 'daily', 'weekly', 'monthly', 'yearly', 'custom'])
                  ->default('daily')
                  ->comment('Kiralama periyodu: hourly=Saatlik, daily=Günlük, weekly=Haftalık, monthly=Aylık, yearly=Yıllık, custom=Özel');

            $table->integer('rental_duration')->comment('Kiralama süresi (periyod tipine göre)');

            // Important Dates
            $table->timestamp('start_date')->comment('Başlangıç tarihi');
            $table->timestamp('end_date')->comment('Bitiş tarihi');
            $table->timestamp('actual_return_date')->nullable()->comment('Gerçek iade tarihi');

            // Status
            $table->enum('status', [
                'pending',          // Beklemede
                'active',           // Aktif
                'extended',         // Uzatıldı
                'completed',        // Tamamlandı
                'overdue',          // Gecikmiş
                'cancelled',        // İptal edildi
                'terminated'        // Sonlandırıldı
            ])->default('pending')->comment('Durum');

            // Pricing
            $table->decimal('rental_price', 12, 2)->comment('Kiralama ücreti (₺)');
            $table->decimal('deposit_amount', 12, 2)->default(0)->comment('Depozito tutarı (₺)');
            $table->decimal('insurance_cost', 10, 2)->default(0)->comment('Sigorta bedeli (₺)');
            $table->decimal('delivery_cost', 10, 2)->default(0)->comment('Teslimat ücreti (₺)');
            $table->decimal('total_amount', 12, 2)->comment('Toplam tutar (₺)');

            // Deposit
            $table->boolean('deposit_paid')->default(false)->comment('Depozito ödendi mi?');
            $table->timestamp('deposit_paid_at')->nullable()->comment('Depozito ödeme tarihi');
            $table->boolean('deposit_refunded')->default(false)->comment('Depozito iade edildi mi?');
            $table->decimal('deposit_refund_amount', 12, 2)->default(0)->comment('İade edilen depozito (₺)');
            $table->text('deposit_deduction_reason')->nullable()->comment('Depozito kesinti nedeni');

            // Product Condition
            $table->enum('condition_at_start', ['excellent', 'good', 'fair', 'poor'])
                  ->default('excellent')
                  ->comment('Başlangıçtaki durum: excellent=Mükemmel, good=İyi, fair=Orta, poor=Kötü');

            $table->enum('condition_at_return', ['excellent', 'good', 'fair', 'poor', 'damaged'])
                  ->nullable()
                  ->comment('İade anındaki durum');

            $table->json('start_inspection_images')->nullable()->comment('Başlangıç muayene görselleri (JSON array)');
            $table->json('return_inspection_images')->nullable()->comment('İade muayene görselleri (JSON array)');
            $table->text('damage_notes')->nullable()->comment('Hasar notları');

            // Extensions
            $table->integer('extensions_count')->default(0)->comment('Uzatma sayısı');
            $table->json('extensions_history')->nullable()->comment('Uzatma geçmişi (JSON array)');

            // Late Fees
            $table->integer('overdue_days')->default(0)->comment('Gecikme günü');
            $table->decimal('late_fee_per_day', 10, 2)->default(0)->comment('Günlük gecikme ücreti (₺)');
            $table->decimal('total_late_fees', 12, 2)->default(0)->comment('Toplam gecikme ücreti (₺)');

            // Delivery & Return
            $table->json('delivery_address')->nullable()->comment('Teslimat adresi (JSON)');
            $table->json('return_address')->nullable()->comment('İade adresi (JSON)');
            $table->timestamp('delivered_at')->nullable()->comment('Teslim edilme tarihi');
            $table->timestamp('returned_at')->nullable()->comment('İade tarihi');

            // Terms & Conditions
            $table->text('terms')->nullable()->comment('Sözleşme şartları');
            $table->boolean('terms_accepted')->default(false)->comment('Şartlar kabul edildi mi?');
            $table->timestamp('terms_accepted_at')->nullable()->comment('Şartlar kabul tarihi');
            $table->string('signature')->nullable()->comment('İmza dosya yolu');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('product_id', 'idx_product');
            $table->index('order_id', 'idx_order');
            $table->index('contract_number', 'idx_number');
            $table->index('status', 'idx_status');
            $table->index('start_date', 'idx_start');
            $table->index('end_date', 'idx_end');
            $table->index(['status', 'end_date'], 'idx_status_end');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse sözleşmeleri de silinir');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün aktif kirada ise silinemez');

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur');
        })
        ->comment('Kiralama sözleşmeleri - Forklift ve ekipman kiralamalar');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_rental_contracts');
    }
};
