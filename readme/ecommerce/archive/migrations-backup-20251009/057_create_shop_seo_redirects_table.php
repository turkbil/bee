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
        if (Schema::hasTable('shop_seo_redirects')) {
            return;
        }

        Schema::create('shop_seo_redirects', function (Blueprint $table) {
            // Primary Key
            $table->id('seo_redirect_id');

            // Redirect Info
            $table->string('from_path')->comment('Kaynak yol (/eski-urun-linki)');
            $table->string('to_path')->comment('Hedef yol (/yeni-urun-linki)');

            // Redirect Type
            $table->integer('status_code')->default(301)->comment('HTTP durum kodu (301=Kalıcı, 302=Geçici)');

            // Related Entity (Optional)
            $table->string('entity_type')->nullable()->comment('İlgili entity tipi (Product, Category)');
            $table->unsignedBigInteger('entity_id')->nullable()->comment('İlgili entity ID');

            // Statistics
            $table->integer('hits')->default(0)->comment('Kullanım sayısı');
            $table->timestamp('last_hit_at')->nullable()->comment('Son kullanım tarihi');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('from_path', 'idx_from');
            $table->index('to_path', 'idx_to');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->index('is_active', 'idx_active');
            $table->unique(['from_path', 'is_active'], 'unique_active_redirect');

        })
        ->comment('SEO Yönlendirmeleri - 301/302 redirect yönetimi');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_seo_redirects');
    }
};
