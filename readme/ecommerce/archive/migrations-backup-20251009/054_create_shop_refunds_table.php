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
        if (Schema::hasTable('shop_refunds')) {
            return;
        }

        Schema::create('shop_refunds', function (Blueprint $table) {
            // Primary Key
            $table->id('refund_id');

            // Relations
            $table->foreignId('order_id')->comment('Sipariş ID - shop_orders ilişkisi');
            $table->foreignId('return_id')->nullable()->comment('İade ID - shop_returns ilişkisi');
            $table->foreignId('payment_id')->nullable()->comment('Orijinal ödeme ID - shop_payments ilişkisi');

            // Refund Info
            $table->string('refund_number')->unique()->comment('İade numarası (RFD-2024-00001)');

            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])
                  ->default('pending')
                  ->comment('Durum: pending=Beklemede, processing=İşleniyor, completed=Tamamlandı, failed=Başarısız, cancelled=İptal');

            // Refund Type
            $table->enum('refund_type', ['full', 'partial'])
                  ->default('partial')
                  ->comment('İade tipi: full=Tam, partial=Kısmi');

            // Amounts
            $table->decimal('refund_amount', 12, 2)->comment('İade tutarı (₺)');
            $table->decimal('shipping_refund', 10, 2)->default(0)->comment('Kargo iadesi (₺)');
            $table->decimal('tax_refund', 10, 2)->default(0)->comment('Vergi iadesi (₺)');
            $table->decimal('total_refund', 12, 2)->comment('Toplam iade (₺)');

            // Deductions
            $table->decimal('restocking_fee', 10, 2)->default(0)->comment('Yeniden stoklama ücreti (₺)');
            $table->decimal('other_deductions', 10, 2)->default(0)->comment('Diğer kesintiler (₺)');
            $table->text('deduction_notes')->nullable()->comment('Kesinti notları');

            // Refund Method
            $table->enum('refund_method', ['original', 'bank_transfer', 'store_credit', 'check', 'cash'])
                  ->default('original')
                  ->comment('İade yöntemi: original=Orijinal ödeme yöntemi, bank_transfer=Banka havalesi, store_credit=Mağaza kredisi, check=Çek, cash=Nakit');

            // Bank Info (for bank transfer)
            $table->string('bank_name')->nullable()->comment('Banka adı');
            $table->string('account_holder')->nullable()->comment('Hesap sahibi');
            $table->string('iban')->nullable()->comment('IBAN');

            // Gateway Info
            $table->string('gateway_name')->nullable()->comment('Ödeme gateway adı');
            $table->string('gateway_transaction_id')->nullable()->comment('Gateway işlem ID');
            $table->json('gateway_response')->nullable()->comment('Gateway yanıtı (JSON)');

            // Important Dates
            $table->timestamp('requested_at')->nullable()->comment('Talep tarihi');
            $table->timestamp('approved_at')->nullable()->comment('Onay tarihi');
            $table->timestamp('processed_at')->nullable()->comment('İşlem tarihi');
            $table->timestamp('completed_at')->nullable()->comment('Tamamlanma tarihi');

            // Approval
            $table->foreignId('approved_by_user_id')->nullable()->comment('Onaylayan kullanıcı ID');

            // Reason
            $table->text('reason')->nullable()->comment('İade nedeni');

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
            $table->index('order_id', 'idx_order');
            $table->index('return_id', 'idx_return');
            $table->index('payment_id', 'idx_payment');
            $table->index('refund_number', 'idx_number');
            $table->index('status', 'idx_status');
            $table->index('refund_method', 'idx_method');
            $table->index('processed_at', 'idx_processed');

            // Foreign Keys
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse iade de silinir');

            $table->foreign('return_id')
                  ->references('return_id')
                  ->on('shop_returns')
                  ->onDelete('cascade')
                  ->comment('İade silinirse ID null olur');

            $table->foreign('payment_id')
                  ->references('id')
                  ->on('shop_payments')
                  ->onDelete('set null')
                  ->comment('Ödeme silinirse ID null olur');
        })
        ->comment('Para iadeleri - Müşteriye yapılan para iade işlemleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_refunds');
    }
};
