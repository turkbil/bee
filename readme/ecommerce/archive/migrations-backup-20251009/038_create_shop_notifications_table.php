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
        if (Schema::hasTable('shop_notifications')) {
            return;
        }

        Schema::create('shop_notifications', function (Blueprint $table) {
            // Primary Key
            $table->id('notification_id');

            // Relations
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');

            // Notification Type
            $table->enum('notification_type', [
                'order_status',         // Sipariş durumu
                'payment_received',     // Ödeme alındı
                'shipment_update',      // Kargo güncellemesi
                'product_back_in_stock',// Stokta
                'price_drop',           // Fiyat düştü
                'abandoned_cart',       // Terk edilmiş sepet
                'review_reminder',      // Yorum hatırlatması
                'wishlist_sale',        // Favorilerdeki ürün indirimde
                'new_product',          // Yeni ürün
                'promotion',            // Promosyon
                'newsletter',           // Bülten
                'custom'                // Özel
            ])->comment('Bildirim tipi');

            // Notification Content
            $table->json('title')->comment('Bildirim başlığı (JSON çoklu dil)');
            $table->json('message')->comment('Bildirim mesajı (JSON çoklu dil)');
            $table->json('action_url')->nullable()->comment('Aksiyon URL (JSON çoklu dil)');
            $table->string('action_text')->nullable()->comment('Aksiyon butonu metni');

            // Related Entity
            $table->string('related_type')->nullable()->comment('İlgili entity tipi (Order, Product, Shipment)');
            $table->unsignedBigInteger('related_id')->nullable()->comment('İlgili entity ID');

            // Delivery Channels
            $table->boolean('send_email')->default(true)->comment('E-posta gönderilsin mi?');
            $table->boolean('send_sms')->default(false)->comment('SMS gönderilsin mi?');
            $table->boolean('send_push')->default(false)->comment('Push notification gönderilsin mi?');
            $table->boolean('show_in_app')->default(true)->comment('Uygulama içinde gösterilsin mi?');

            // Status
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])
                  ->default('pending')
                  ->comment('Durum: pending=Beklemede, sent=Gönderildi, failed=Başarısız, cancelled=İptal');

            // Read Status
            $table->boolean('is_read')->default(false)->comment('Okundu mu?');
            $table->timestamp('read_at')->nullable()->comment('Okunma tarihi');

            // Delivery Status
            $table->timestamp('email_sent_at')->nullable()->comment('E-posta gönderilme tarihi');
            $table->timestamp('sms_sent_at')->nullable()->comment('SMS gönderilme tarihi');
            $table->timestamp('push_sent_at')->nullable()->comment('Push gönderilme tarihi');

            // Scheduled
            $table->timestamp('scheduled_at')->nullable()->comment('Planlanmış gönderim tarihi');

            // Priority
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                  ->default('normal')
                  ->comment('Öncelik: low=Düşük, normal=Normal, high=Yüksek, urgent=Acil');

            // Additional Info
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('customer_id', 'idx_customer');
            $table->index('notification_type', 'idx_type');
            $table->index('status', 'idx_status');
            $table->index('is_read', 'idx_read');
            $table->index('priority', 'idx_priority');
            $table->index('scheduled_at', 'idx_scheduled');
            $table->index(['related_type', 'related_id'], 'idx_related');
            $table->index(['customer_id', 'is_read'], 'idx_customer_read');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse bildirimleri de silinir');
        })
        ->comment('Bildirimler - E-posta, SMS, push notification yönetimi');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_notifications');
    }
};
