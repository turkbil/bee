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
        if (Schema::hasTable('shop_returns')) {
            return;
        }

        Schema::create('shop_returns', function (Blueprint $table) {
            // Primary Key
            $table->id('return_id');

            // Relations
            $table->foreignId('order_id')->comment('Sipariş ID - shop_orders ilişkisi');
            $table->foreignId('customer_id')->comment('Müşteri ID - shop_customers ilişkisi');

            // Return Info
            $table->string('return_number')->unique()->comment('İade numarası (RTN-2024-00001)');

            // Status
            $table->enum('status', [
                'requested',        // Talep edildi
                'approved',         // Onaylandı
                'rejected',         // Reddedildi
                'received',         // Teslim alındı
                'inspecting',       // İnceleniyor
                'approved_refund',  // İade onaylandı
                'rejected_refund',  // İade reddedildi
                'completed',        // Tamamlandı
                'cancelled'         // İptal edildi
            ])->default('requested')->comment('Durum');

            // Return Type
            $table->enum('return_type', ['refund', 'replacement', 'repair', 'store_credit'])
                  ->default('refund')
                  ->comment('İade tipi: refund=Para iadesi, replacement=Değişim, repair=Onarım, store_credit=Mağaza kredisi');

            // Reason
            $table->enum('return_reason', [
                'defective',        // Kusurlu
                'damaged',          // Hasarlı
                'wrong_item',       // Yanlış ürün
                'not_as_described', // Açıklamaya uymuyor
                'changed_mind',     // Fikir değişikliği
                'better_price',     // Daha iyi fiyat buldum
                'arrived_late',     // Geç geldi
                'other'             // Diğer
            ])->comment('İade nedeni');

            $table->text('reason_details')->nullable()->comment('Neden detayları');

            // Return Request
            $table->text('customer_notes')->nullable()->comment('Müşteri notları');
            $table->json('images')->nullable()->comment('Ürün görselleri (JSON array)');
            $table->json('videos')->nullable()->comment('Ürün videoları (JSON array)');

            // Shipping
            $table->string('return_shipping_carrier')->nullable()->comment('İade kargo firması');
            $table->string('return_tracking_number')->nullable()->comment('İade takip numarası');
            $table->decimal('return_shipping_cost', 10, 2)->default(0)->comment('İade kargo ücreti (₺)');
            $table->enum('shipping_paid_by', ['customer', 'merchant', 'shared'])
                  ->default('customer')
                  ->comment('Kargo ödeyen: customer=Müşteri, merchant=Satıcı, shared=Paylaşımlı');

            // Inspection
            $table->enum('inspection_status', ['pending', 'passed', 'failed', 'partial'])
                  ->nullable()
                  ->comment('Muayene durumu: pending=Beklemede, passed=Geçti, failed=Geçmedi, partial=Kısmi');

            $table->text('inspection_notes')->nullable()->comment('Muayene notları');
            $table->foreignId('inspected_by_user_id')->nullable()->comment('Muayeneyi yapan kullanıcı ID');
            $table->timestamp('inspected_at')->nullable()->comment('Muayene tarihi');

            // Refund Info
            $table->decimal('refund_amount', 12, 2)->default(0)->comment('İade tutarı (₺)');
            $table->decimal('restocking_fee', 10, 2)->default(0)->comment('Yeniden stoklama ücreti (₺)');
            $table->decimal('deduction_amount', 10, 2)->default(0)->comment('Kesinti tutarı (₺)');
            $table->text('deduction_reason')->nullable()->comment('Kesinti nedeni');

            // Important Dates
            $table->timestamp('requested_at')->nullable()->comment('Talep tarihi');
            $table->timestamp('approved_at')->nullable()->comment('Onay tarihi');
            $table->timestamp('received_at')->nullable()->comment('Teslim alma tarihi');
            $table->timestamp('refunded_at')->nullable()->comment('İade işlem tarihi');
            $table->timestamp('completed_at')->nullable()->comment('Tamamlanma tarihi');

            // Additional Info
            $table->text('admin_notes')->nullable()->comment('Admin notları');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('order_id', 'idx_order');
            $table->index('customer_id', 'idx_customer');
            $table->index('return_number', 'idx_number');
            $table->index('status', 'idx_status');
            $table->index('return_type', 'idx_type');
            $table->index('return_reason', 'idx_reason');
            $table->index('requested_at', 'idx_requested');

            // Foreign Keys
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse iade de silinir');

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse iade de silinir');
        })
        ->comment('İadeler - Ürün iade talepleri ve işlemleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_returns');
    }
};
