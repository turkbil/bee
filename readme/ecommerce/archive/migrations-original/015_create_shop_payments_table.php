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
        if (Schema::hasTable('shop_payments')) {
            return;
        }

        Schema::create('shop_payments', function (Blueprint $table) {
            // Primary Key
            $table->id('payment_id');

            // Relations
            $table->foreignId('order_id')->comment('Sipariş ID - shop_orders ilişkisi');
            $table->foreignId('payment_method_id')->nullable()->comment('Ödeme yöntemi ID - shop_payment_methods ilişkisi');

            // Payment Info
            $table->string('payment_number')->unique()->comment('Ödeme numarası (PAY-2024-00001)');
            $table->enum('payment_type', ['full', 'partial', 'deposit', 'installment', 'refund'])
                  ->default('full')
                  ->comment('Ödeme tipi: full=Tam ödeme, partial=Kısmi ödeme, deposit=Kapora, installment=Taksit, refund=İade');

            // Amount
            $table->decimal('amount', 12, 2)->comment('Ödeme tutarı (₺)');
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');
            $table->decimal('exchange_rate', 10, 4)->default(1)->comment('Döviz kuru (TRY dışı ödemeler için)');
            $table->decimal('amount_in_base_currency', 12, 2)->comment('Ana para biriminde tutar (₺)');

            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])
                  ->default('pending')
                  ->comment('Ödeme durumu');

            // Gateway Info
            $table->string('gateway_name')->nullable()->comment('Ödeme gateway adı (iyzico, paytr, stripe)');
            $table->string('gateway_transaction_id')->nullable()->comment('Gateway işlem numarası');
            $table->string('gateway_payment_id')->nullable()->comment('Gateway ödeme ID');
            $table->json('gateway_response')->nullable()->comment('Gateway yanıtı (JSON - tüm response)');

            // Card Info (if applicable - hashed/masked)
            $table->string('card_brand')->nullable()->comment('Kart markası (Visa, MasterCard, vb)');
            $table->string('card_last_four', 4)->nullable()->comment('Kart son 4 hanesi');
            $table->string('card_holder_name')->nullable()->comment('Kart sahibi adı');

            // Installment Info
            $table->integer('installment_count')->default(1)->comment('Taksit sayısı (1=Tek çekim)');
            $table->decimal('installment_fee', 8, 2)->default(0)->comment('Taksit komisyonu (₺)');

            // Bank/Transfer Info (for wire transfers)
            $table->string('bank_name')->nullable()->comment('Banka adı (havale için)');
            $table->string('bank_account_name')->nullable()->comment('Hesap sahibi');
            $table->string('bank_reference')->nullable()->comment('Banka dekontu referans no');
            $table->string('receipt_file')->nullable()->comment('Dekont dosya yolu');

            // Refund Info
            $table->foreignId('refund_for_payment_id')->nullable()->comment('İade edilen ödeme ID (refund ise)');
            $table->text('refund_reason')->nullable()->comment('İade nedeni');

            // Verification
            $table->boolean('is_verified')->default(false)->comment('Doğrulandı mı? (manuel ödemeler için)');
            $table->foreignId('verified_by_user_id')->nullable()->comment('Doğrulayan kullanıcı ID');
            $table->timestamp('verified_at')->nullable()->comment('Doğrulama tarihi');

            // Important Dates
            $table->timestamp('paid_at')->nullable()->comment('Ödeme tarihi');
            $table->timestamp('failed_at')->nullable()->comment('Başarısız olma tarihi');
            $table->timestamp('refunded_at')->nullable()->comment('İade tarihi');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // IP & Browser
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');
            $table->text('user_agent')->nullable()->comment('Tarayıcı bilgisi');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('order_id', 'idx_order');
            $table->index('payment_method_id', 'idx_payment_method');
            $table->index('payment_number', 'idx_payment_number');
            $table->index('status', 'idx_status');
            $table->index('payment_type', 'idx_type');
            $table->index('gateway_transaction_id', 'idx_gateway_transaction');
            $table->index('is_verified', 'idx_verified');
            $table->index('paid_at', 'idx_paid_at');
            $table->index(['order_id', 'status'], 'idx_order_status');

            // Foreign Keys
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ödemeler de silinir');

            $table->foreign('payment_method_id')
                  ->references('payment_method_id')
                  ->on('shop_payment_methods')
                  ->onDelete('cascade')
                  ->comment('Ödeme yöntemi silinirse ID null olur');

            $table->foreign('refund_for_payment_id')
                  ->references('id')
                  ->on('shop_payments')
                  ->onDelete('set null')
                  ->comment('İade edilen ödeme silinirse ID null olur');
        })
        ->comment('Ödemeler - Sipariş ödemelerinin detaylı kayıtları');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_payments');
    }
};
