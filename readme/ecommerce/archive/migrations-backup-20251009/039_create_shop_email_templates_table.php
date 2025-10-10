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
        if (Schema::hasTable('shop_email_templates')) {
            return;
        }

        Schema::create('shop_email_templates', function (Blueprint $table) {
            // Primary Key
            $table->id('email_template_id');

            // Basic Info
            $table->string('name')->comment('Şablon adı (Order Confirmation, Shipping Notification)');
            $table->string('code')->unique()->comment('Şablon kodu (order_confirmation, shipping_notification)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Template Type
            $table->enum('template_type', [
                'order_confirmation',       // Sipariş onayı
                'order_shipped',           // Kargoya verildi
                'order_delivered',         // Teslim edildi
                'order_cancelled',         // İptal edildi
                'payment_received',        // Ödeme alındı
                'refund_processed',        // İade işlendi
                'abandoned_cart',          // Terk edilmiş sepet
                'back_in_stock',           // Stokta
                'price_drop',              // Fiyat düştü
                'welcome',                 // Hoş geldiniz
                'password_reset',          // Şifre sıfırlama
                'review_request',          // Yorum isteği
                'newsletter',              // Bülten
                'custom'                   // Özel
            ])->comment('Şablon tipi');

            // Email Content
            $table->json('subject')->comment('E-posta konusu (JSON çoklu dil)');
            $table->json('preheader')->nullable()->comment('Ön başlık/Özet (JSON çoklu dil)');
            $table->json('body')->comment('E-posta içeriği (JSON çoklu dil - HTML)');
            $table->json('plain_text_body')->nullable()->comment('Düz metin içeriği (JSON çoklu dil)');

            // Sender Info
            $table->string('from_name')->nullable()->comment('Gönderen adı (varsayılan kullanılmazsa)');
            $table->string('from_email')->nullable()->comment('Gönderen e-posta (varsayılan kullanılmazsa)');
            $table->string('reply_to')->nullable()->comment('Yanıtlanacak e-posta');

            // BCC
            $table->string('bcc_emails')->nullable()->comment('BCC e-postaları (virgülle ayrılmış)');

            // Template Variables (Available placeholders)
            $table->json('available_variables')->nullable()->comment('Kullanılabilir değişkenler (JSON - {{order_number}}, {{customer_name}})');

            // Attachments
            $table->json('default_attachments')->nullable()->comment('Varsayılan ekler (JSON array - dosya yolları)');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_default')->default(false)->comment('Varsayılan şablon mu?');

            // Design
            $table->string('layout')->default('default')->comment('Layout/Tema (default, minimal, modern)');
            $table->json('design_settings')->nullable()->comment('Tasarım ayarları (JSON - renkler, fontlar, vb)');

            // Testing
            $table->string('test_email')->nullable()->comment('Test e-posta adresi');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('code', 'idx_code');
            $table->index('template_type', 'idx_type');
            $table->index('is_active', 'idx_active');
            $table->index('is_default', 'idx_default');
        })
        ->comment('E-posta şablonları - Otomatik e-posta gönderimlerinde kullanılacak şablonlar');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_email_templates');
    }
};
