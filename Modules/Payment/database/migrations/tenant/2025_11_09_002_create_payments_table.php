<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * GLOBAL PAYMENTS - Polymorphic ilişki ile tüm modüller için ödemeler
     * ShopOrder, Subscription, Reservation, Invoice vb. herhangi bir model ödeme alabilir
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');

            // ⭐ POLYMORPHIC İLİŞKİ (Hangi modelden ödeme?)
            $table->morphs('payable'); // payable_id, payable_type
            // Örnekler:
            // - payable_type: "Modules\Shop\App\Models\ShopOrder", payable_id: 123
            // - payable_type: "Modules\Membership\App\Models\Subscription", payable_id: 45
            // - payable_type: "Modules\Booking\App\Models\Reservation", payable_id: 67

            // Payment Method
            $table->foreignId('payment_method_id')->nullable()
                  ->constrained('payment_methods', 'payment_method_id')
                  ->onDelete('set null');

            // Payment Info
            $table->string('payment_number')->unique()->comment('PAY-2024-00001');
            $table->enum('payment_type', ['purchase', 'subscription', 'donation', 'refund', 'deposit'])
                  ->default('purchase');

            // Amount
            $table->decimal('amount', 12, 2)->comment('Ödeme tutarı');
            $table->string('currency', 3)->default('TRY');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('amount_in_base_currency', 12, 2);

            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])
                  ->default('pending');

            // Gateway Info
            $table->enum('gateway', ['paytr', 'stripe', 'iyzico', 'paypal', 'manual'])
                  ->comment('Kullanılan gateway');
            $table->string('gateway_transaction_id')->nullable()->comment('Gateway merchant_oid');
            $table->string('gateway_payment_id')->nullable()->comment('Gateway token');
            $table->json('gateway_response')->nullable()->comment('Tüm gateway response');

            // Card Info (masked)
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_holder_name')->nullable();

            // Installment
            $table->integer('installment_count')->default(1);
            $table->decimal('installment_fee', 8, 2)->default(0);

            // Refund
            $table->foreignId('refund_for_payment_id')->nullable()
                  ->constrained('payments', 'payment_id')
                  ->onDelete('set null');
            $table->text('refund_reason')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by_user_id')->nullable();
            $table->timestamp('verified_at')->nullable();

            // Important Dates
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            // Additional Info
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // IP & Browser
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            // NOT: morphs() zaten payable_type + payable_id için index oluşturur // ⭐ Polymorphic index
            $table->index('payment_number');
            $table->index('status');
            $table->index('gateway');
            $table->index('gateway_transaction_id');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
