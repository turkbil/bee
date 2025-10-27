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
        if (Schema::hasTable('shop_reviews')) {
            return;
        }

        Schema::create('shop_reviews', function (Blueprint $table) {
            // Primary Key
            $table->id('review_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');
            $table->foreignId('order_id')->nullable()->comment('Sipariş ID - shop_orders ilişkisi (doğrulanmış alıcı)');

            // Reviewer Info (Snapshot - customer silinse bile kalacak)
            $table->string('reviewer_name')->comment('Yorumcu adı');
            $table->string('reviewer_email')->comment('Yorumcu e-posta');

            // Review Content
            $table->string('title')->nullable()->comment('Yorum başlığı');
            $table->text('comment')->comment('Yorum metni');
            $table->integer('rating')->comment('Puan (1-5 arası)');

            // Detailed Ratings (Opsiyonel alt puanlar)
            $table->integer('rating_quality')->nullable()->comment('Kalite puanı (1-5)');
            $table->integer('rating_value')->nullable()->comment('Fiyat/Performans puanı (1-5)');
            $table->integer('rating_delivery')->nullable()->comment('Teslimat puanı (1-5)');

            // Images/Media
            $table->json('images')->nullable()->comment('Yorum görselleri (JSON array - dosya yolları)');

            // Verification
            $table->boolean('is_verified_purchase')->default(false)->comment('Doğrulanmış alıcı mı?');

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])
                  ->default('pending')
                  ->comment('Durum: pending=Onay bekliyor, approved=Onaylandı, rejected=Reddedildi, spam=Spam');

            $table->foreignId('moderated_by_user_id')->nullable()->comment('Onaylayan/Reddeden admin ID');
            $table->timestamp('moderated_at')->nullable()->comment('Onay/Red tarihi');
            $table->text('moderation_notes')->nullable()->comment('Moderasyon notları');

            // Helpfulness
            $table->integer('helpful_count')->default(0)->comment('Yardımcı oldu sayısı');
            $table->integer('not_helpful_count')->default(0)->comment('Yardımcı olmadı sayısı');

            // Admin Reply
            $table->text('admin_reply')->nullable()->comment('Admin yanıtı');
            $table->timestamp('admin_replied_at')->nullable()->comment('Admin yanıt tarihi');

            // Additional Info
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');
            $table->text('user_agent')->nullable()->comment('Tarayıcı bilgisi');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('product_id', 'idx_product');
            $table->index('customer_id', 'idx_customer');
            $table->index('order_id', 'idx_order');
            $table->index('status', 'idx_status');
            $table->index('rating', 'idx_rating');
            $table->index('is_verified_purchase', 'idx_verified');
            $table->index('created_at', 'idx_created');
            $table->index(['product_id', 'status'], 'idx_product_status');
            $table->index(['product_id', 'rating'], 'idx_product_rating');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse yorumları da silinir');

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama yorum kalır');

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse ID null olur ama yorum kalır');
        })
        ->comment('Ürün yorumları ve değerlendirmeleri - Müşteri görüşleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_reviews');
    }
};
