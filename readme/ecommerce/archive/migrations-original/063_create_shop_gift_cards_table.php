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
        if (Schema::hasTable('shop_gift_cards')) {
            return;
        }

        Schema::create('shop_gift_cards', function (Blueprint $table) {
            // Primary Key
            $table->id('gift_card_id');

            // Gift Card Info
            $table->string('code')->unique()->comment('Hediye kartı kodu (GIFT-XXXX-XXXX-XXXX)');
            $table->string('pin', 4)->nullable()->comment('PIN kodu (güvenlik için)');

            // Relations
            $table->foreignId('purchased_by_customer_id')->nullable()->comment('Satın alan müşteri ID');
            $table->foreignId('purchased_order_id')->nullable()->comment('Satın alındığı sipariş ID');

            // Amounts
            $table->decimal('initial_amount', 12, 2)->comment('İlk yüklenen tutar (₺)');
            $table->decimal('current_balance', 12, 2)->comment('Güncel bakiye (₺)');
            $table->decimal('used_amount', 12, 2)->default(0)->comment('Kullanılan tutar (₺)');

            // Currency
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (TRY, USD, EUR)');

            // Recipient Info
            $table->string('recipient_name')->nullable()->comment('Alıcı adı');
            $table->string('recipient_email')->nullable()->comment('Alıcı e-posta');
            $table->text('personal_message')->nullable()->comment('Kişisel mesaj');

            // Status
            $table->enum('status', ['pending', 'active', 'used', 'expired', 'cancelled'])
                  ->default('pending')
                  ->comment('Durum: pending=Beklemede, active=Aktif, used=Kullanıldı, expired=Süresi doldu, cancelled=İptal');

            // Validity
            $table->timestamp('activated_at')->nullable()->comment('Aktifleştirilme tarihi');
            $table->timestamp('expires_at')->nullable()->comment('Son kullanma tarihi');

            // Usage
            $table->integer('usage_count')->default(0)->comment('Kullanım sayısı');
            $table->timestamp('last_used_at')->nullable()->comment('Son kullanım tarihi');

            // Design
            $table->string('template')->nullable()->comment('Şablon (birthday, christmas, generic)');
            $table->string('image')->nullable()->comment('Görsel dosya yolu');

            // Delivery
            $table->enum('delivery_method', ['email', 'physical', 'instant'])
                  ->default('email')
                  ->comment('Teslimat yöntemi: email=E-posta, physical=Fiziksel, instant=Anında');

            $table->timestamp('delivered_at')->nullable()->comment('Teslim edilme tarihi');
            $table->boolean('is_delivered')->default(false)->comment('Teslim edildi mi?');

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
            $table->index('code', 'idx_code');
            $table->index('purchased_by_customer_id', 'idx_purchased_by');
            $table->index('purchased_order_id', 'idx_order');
            $table->index('status', 'idx_status');
            $table->index('recipient_email', 'idx_recipient_email');
            $table->index('expires_at', 'idx_expires');

            // Foreign Keys
            $table->foreign('purchased_by_customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur');

            $table->foreign('purchased_order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur');
        })
        ->comment('Hediye kartları - Hediye çekleri ve kartları');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_gift_cards');
    }
};
