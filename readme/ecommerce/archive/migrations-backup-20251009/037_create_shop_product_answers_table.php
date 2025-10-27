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
        if (Schema::hasTable('shop_product_answers')) {
            return;
        }

        Schema::create('shop_product_answers', function (Blueprint $table) {
            // Primary Key
            $table->id('answer_id');

            // Relations
            $table->foreignId('question_id')->comment('Soru ID - shop_product_questions ilişkisi');
            $table->foreignId('user_id')->nullable()->comment('Cevaplayan kullanıcı ID (admin/satıcı)');
            $table->foreignId('customer_id')->nullable()->comment('Cevaplayan müşteri ID');

            // Answerer Info (Snapshot)
            $table->string('answerer_name')->comment('Cevaplayan kişi adı');
            $table->string('answerer_email')->nullable()->comment('Cevaplayan kişi e-posta');
            $table->enum('answerer_type', ['admin', 'seller', 'customer', 'verified_buyer'])
                  ->default('customer')
                  ->comment('Cevaplayan tipi: admin=Admin, seller=Satıcı, customer=Müşteri, verified_buyer=Doğrulanmış alıcı');

            // Answer
            $table->text('answer')->comment('Cevap metni');

            // Verification
            $table->boolean('is_official')->default(false)->comment('Resmi cevap mı? (satıcı/admin)');
            $table->boolean('is_verified_buyer')->default(false)->comment('Doğrulanmış alıcı mı?');

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])
                  ->default('approved')
                  ->comment('Durum: pending=Onay bekliyor, approved=Onaylandı, rejected=Reddedildi, spam=Spam');

            // Statistics
            $table->integer('helpful_count')->default(0)->comment('Yardımcı oldu sayısı');
            $table->integer('not_helpful_count')->default(0)->comment('Yardımcı olmadı sayısı');

            // IP & Browser
            $table->string('ip_address', 45)->nullable()->comment('IP adresi');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('question_id', 'idx_question');
            $table->index('user_id', 'idx_user');
            $table->index('customer_id', 'idx_customer');
            $table->index('status', 'idx_status');
            $table->index('is_official', 'idx_official');
            $table->index('created_at', 'idx_created');

            // Foreign Keys
            $table->foreign('question_id')
                  ->references('id')
                  ->on('shop_product_questions')
                  ->onDelete('cascade')
                  ->comment('Soru silinirse cevapları da silinir');

            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama cevap kalır');
        })
        ->comment('Ürün cevapları - Ürün sorularına verilen cevaplar (Q&A)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_answers');
    }
};
