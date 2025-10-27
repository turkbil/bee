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
        if (Schema::hasTable('shop_gift_card_transactions')) {
            return;
        }

        Schema::create('shop_gift_card_transactions', function (Blueprint $table) {
            // Primary Key
            $table->id('gift_card_transaction_id');

            // Relations
            $table->foreignId('gift_card_id')->comment('Hediye kartı ID - shop_gift_cards ilişkisi');
            $table->foreignId('order_id')->nullable()->comment('Sipariş ID - shop_orders ilişkisi');
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');

            // Transaction Type
            $table->enum('transaction_type', ['issued', 'used', 'refunded', 'expired', 'cancelled'])
                  ->comment('İşlem tipi: issued=Yayınlandı, used=Kullanıldı, refunded=İade edildi, expired=Süresi doldu, cancelled=İptal edildi');

            // Amounts
            $table->decimal('amount', 12, 2)->comment('İşlem tutarı (₺)');
            $table->decimal('balance_before', 12, 2)->comment('İşlem öncesi bakiye (₺)');
            $table->decimal('balance_after', 12, 2)->comment('İşlem sonrası bakiye (₺)');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('gift_card_id', 'idx_gift_card');
            $table->index('order_id', 'idx_order');
            $table->index('customer_id', 'idx_customer');
            $table->index('transaction_type', 'idx_type');
            $table->index('created_at', 'idx_created');

            // Foreign Keys
            $table->foreign('gift_card_id')
                  ->references('gift_card_id')
                  ->on('shop_gift_cards')
                  ->onDelete('cascade')
                  ->comment('Hediye kartı silinirse işlemleri de silinir');

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur');

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur');
        })
        ->comment('Hediye kartı işlemleri - Hediye kartı kullanım geçmişi');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_gift_card_transactions');
    }
};
