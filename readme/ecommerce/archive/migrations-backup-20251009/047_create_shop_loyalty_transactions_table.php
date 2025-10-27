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
        if (Schema::hasTable('shop_loyalty_transactions')) {
            return;
        }

        Schema::create('shop_loyalty_transactions', function (Blueprint $table) {
            // Primary Key
            $table->id('loyalty_transaction_id');

            // Relations
            $table->foreignId('customer_id')->comment('Müşteri ID - shop_customers ilişkisi');
            $table->foreignId('order_id')->nullable()->comment('Sipariş ID - shop_orders ilişkisi');

            // Transaction Type
            $table->enum('transaction_type', [
                'earned',           // Kazanıldı
                'spent',            // Harcandı
                'expired',          // Süresi doldu
                'refunded',         // İade edildi
                'adjusted',         // Düzeltme
                'bonus',            // Bonus
                'welcome',          // Hoş geldin bonusu
                'birthday',         // Doğum günü bonusu
                'referral'          // Referans bonusu
            ])->comment('İşlem tipi');

            // Points
            $table->integer('points')->comment('Puan miktarı (+ veya -)');
            $table->integer('balance_before')->default(0)->comment('İşlem öncesi bakiye');
            $table->integer('balance_after')->default(0)->comment('İşlem sonrası bakiye');

            // Conversion (Puan değeri)
            $table->decimal('points_value', 10, 2)->nullable()->comment('Puan değeri (₺) - 1 puan = 0.10₺ gibi');

            // Reason
            $table->text('reason')->nullable()->comment('İşlem nedeni');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Expiry
            $table->timestamp('expires_at')->nullable()->comment('Son kullanma tarihi (kazanılan puanlar için)');

            // Status
            $table->enum('status', ['pending', 'completed', 'cancelled', 'expired'])
                  ->default('completed')
                  ->comment('Durum: pending=Beklemede, completed=Tamamlandı, cancelled=İptal, expired=Süresi doldu');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('order_id', 'idx_order');
            $table->index('transaction_type', 'idx_type');
            $table->index('status', 'idx_status');
            $table->index('expires_at', 'idx_expires');
            $table->index('created_at', 'idx_created');
            $table->index(['customer_id', 'created_at'], 'idx_customer_date');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse işlemleri de silinir');

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur');
        })
        ->comment('Puan işlemleri - Tüm puan kazanma/harcama hareketleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_loyalty_transactions');
    }
};
