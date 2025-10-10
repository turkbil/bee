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
        if (Schema::hasTable('shop_product_questions')) {
            return;
        }

        Schema::create('shop_product_questions', function (Blueprint $table) {
            // Primary Key
            $table->id('question_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');

            // Questioner Info (Snapshot)
            $table->string('questioner_name')->comment('Soran kişi adı');
            $table->string('questioner_email')->comment('Soran kişi e-posta');

            // Question
            $table->text('question')->comment('Soru metni');

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])
                  ->default('pending')
                  ->comment('Durum: pending=Onay bekliyor, approved=Onaylandı, rejected=Reddedildi, spam=Spam');

            $table->foreignId('moderated_by_user_id')->nullable()->comment('Onaylayan/Reddeden admin ID');
            $table->timestamp('moderated_at')->nullable()->comment('Onay/Red tarihi');

            // Statistics
            $table->integer('answers_count')->default(0)->comment('Cevap sayısı');
            $table->integer('helpful_count')->default(0)->comment('Yardımcı oldu sayısı');

            // IP & Browser
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('product_id', 'idx_product');
            $table->index('customer_id', 'idx_customer');
            $table->index('status', 'idx_status');
            $table->index('created_at', 'idx_created');
            $table->index(['product_id', 'status'], 'idx_product_status');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse soruları da silinir');

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama soru kalır');
        })
        ->comment('Ürün soruları - Müşterilerin ürün hakkında sorduğu sorular (Q&A)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_questions');
    }
};
