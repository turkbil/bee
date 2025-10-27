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
        if (Schema::hasTable('shop_activity_logs')) {
            return;
        }

        Schema::create('shop_activity_logs', function (Blueprint $table) {
            // Primary Key
            $table->id('activity_log_id');

            // User Info
            $table->foreignId('user_id')->nullable()->comment('Kullanıcı ID (admin/satıcı)');
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID');
            $table->string('actor_type')->nullable()->comment('Yapan tipi (Admin, Customer, System)');
            $table->string('actor_name')->nullable()->comment('Yapan adı (snapshot)');

            // Activity Type
            $table->enum('activity_type', [
                'order_created',        // Sipariş oluşturuldu
                'order_updated',        // Sipariş güncellendi
                'order_cancelled',      // Sipariş iptal edildi
                'payment_received',     // Ödeme alındı
                'shipment_created',     // Sevkiyat oluşturuldu
                'product_created',      // Ürün oluşturuldu
                'product_updated',      // Ürün güncellendi
                'stock_updated',        // Stok güncellendi
                'price_updated',        // Fiyat güncellendi
                'customer_created',     // Müşteri oluşturuldu
                'customer_updated',     // Müşteri güncellendi
                'review_submitted',     // Yorum gönderildi
                'login',                // Giriş yapıldı
                'logout',               // Çıkış yapıldı
                'custom'                // Özel
            ])->comment('Aktivite tipi');

            // Related Entity
            $table->string('entity_type')->nullable()->comment('İlgili entity tipi (Order, Product, Customer)');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('İlgili entity ID');

            // Activity Details
            $table->string('action')->comment('Yapılan aksiyon (created, updated, deleted, viewed)');
            $table->text('description')->nullable()->comment('Aktivite açıklaması');

            // Changes (for updates)
            $table->json('old_values')->nullable()->comment('Eski değerler (JSON)');
            $table->json('new_values')->nullable()->comment('Yeni değerler (JSON)');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Request Info
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');
            $table->text('user_agent')->nullable()->comment('Tarayıcı bilgisi');
            $table->string('url')->nullable()->comment('URL');
            $table->string('method', 10)->nullable()->comment('HTTP method (GET, POST, PUT, DELETE)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('user_id', 'idx_user');
            $table->index('customer_id', 'idx_customer');
            $table->index('activity_type', 'idx_type');
            $table->index('action', 'idx_action');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->index('created_at', 'idx_created');
            $table->index(['user_id', 'created_at'], 'idx_user_date');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama log kalır');
        })
        ->comment('Aktivite logları - Tüm sistem aktivitelerinin audit trail kaydı');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_activity_logs');
    }
};
