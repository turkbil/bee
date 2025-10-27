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
        if (Schema::hasTable('shop_subscriptions')) {
            return;
        }

        Schema::create('shop_subscriptions', function (Blueprint $table) {
            // Primary Key
            $table->id('subscription_id');

            // Relations
            $table->foreignId('customer_id')->comment('Müşteri ID - shop_customers ilişkisi');
            $table->foreignId('plan_id')->comment('Plan ID - shop_subscription_plans ilişkisi');

            // Subscription Info
            $table->string('subscription_number')->unique()->comment('Abonelik numarası (SUB-2024-00001)');

            // Status
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired', 'trial', 'pending_payment'])
                  ->default('pending_payment')
                  ->comment('Durum: active=Aktif, paused=Duraklatıldı, cancelled=İptal edildi, expired=Süresi doldu, trial=Deneme, pending_payment=Ödeme bekliyor');

            // Billing
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])
                  ->default('monthly')
                  ->comment('Faturalama döngüsü: daily=Günlük, weekly=Haftalık, monthly=Aylık, quarterly=3 aylık, yearly=Yıllık');

            $table->decimal('price_per_cycle', 12, 2)->comment('Döngü başına fiyat (₺)');
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');

            // Trial Period
            $table->boolean('has_trial')->default(false)->comment('Deneme süresi var mı?');
            $table->integer('trial_days')->default(0)->comment('Deneme süresi (gün)');
            $table->timestamp('trial_ends_at')->nullable()->comment('Deneme bitiş tarihi');

            // Important Dates
            $table->timestamp('started_at')->nullable()->comment('Başlangıç tarihi');
            $table->timestamp('current_period_start')->nullable()->comment('Mevcut dönem başlangıcı');
            $table->timestamp('current_period_end')->nullable()->comment('Mevcut dönem bitişi');
            $table->timestamp('next_billing_date')->nullable()->comment('Sonraki faturalama tarihi');
            $table->timestamp('cancelled_at')->nullable()->comment('İptal tarihi');
            $table->timestamp('expires_at')->nullable()->comment('Son kullanma tarihi');

            // Payment Info
            $table->foreignId('payment_method_id')->nullable()->comment('Ödeme yöntemi ID');
            $table->boolean('auto_renew')->default(true)->comment('Otomatik yenilensin mi?');

            // Statistics
            $table->integer('billing_cycles_completed')->default(0)->comment('Tamamlanan faturalama döngüsü sayısı');
            $table->decimal('total_paid', 14, 2)->default(0)->comment('Toplam ödenen (₺)');

            // Cancellation
            $table->text('cancellation_reason')->nullable()->comment('İptal nedeni');
            $table->json('cancellation_feedback')->nullable()->comment('İptal geri bildirimi (JSON)');

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
            $table->index('plan_id', 'idx_plan');
            $table->index('subscription_number', 'idx_number');
            $table->index('status', 'idx_status');
            $table->index('next_billing_date', 'idx_next_billing');
            $table->index('expires_at', 'idx_expires');
            $table->index(['customer_id', 'status'], 'idx_customer_status');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse aboneliği de silinir');

            $table->foreign('plan_id')
                  ->references('subscription_plan_id')
                  ->on('shop_subscription_plans')
                  ->onDelete('cascade')
                  ->comment('Plan silinirse abonelik silinemez (önce abonelik iptal edilmeli)');

            $table->foreign('payment_method_id')
                  ->references('payment_method_id')
                  ->on('shop_payment_methods')
                  ->onDelete('cascade')
                  ->comment('Ödeme yöntemi silinirse ID null olur');
        })
        ->comment('Abonelikler - Müşteri abonelik kayıtları');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_subscriptions');
    }
};
