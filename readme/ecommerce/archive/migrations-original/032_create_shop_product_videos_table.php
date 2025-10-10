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
        if (Schema::hasTable('shop_product_videos')) {
            return;
        }

        Schema::create('shop_product_videos', function (Blueprint $table) {
            // Primary Key
            $table->id('product_video_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');

            // Video Type
            $table->enum('video_type', ['upload', 'youtube', 'vimeo', 'external'])
                  ->default('youtube')
                  ->comment('Video tipi: upload=Yüklenen, youtube=YouTube, vimeo=Vimeo, external=Harici link');

            // Video URL/Path
            $table->string('video_url')->nullable()->comment('Video URL (YouTube/Vimeo için)');
            $table->string('video_id')->nullable()->comment('Video ID (YouTube/Vimeo için)');
            $table->string('file_path')->nullable()->comment('Dosya yolu (upload için)');

            // Video Info
            $table->json('title')->nullable()->comment('Video başlığı (JSON çoklu dil)');
            $table->json('description')->nullable()->comment('Video açıklaması (JSON çoklu dil)');
            $table->integer('duration')->nullable()->comment('Süre (saniye)');

            // Thumbnail
            $table->string('thumbnail_url')->nullable()->comment('Önizleme görseli URL');
            $table->string('thumbnail_path')->nullable()->comment('Önizleme görseli yolu');

            // Embed Settings
            $table->boolean('autoplay')->default(false)->comment('Otomatik oynat');
            $table->boolean('show_controls')->default(true)->comment('Kontrolleri göster');
            $table->boolean('loop')->default(false)->comment('Döngü');
            $table->boolean('mute')->default(false)->comment('Sessiz');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->boolean('is_featured')->default(false)->comment('Öne çıkan video mu?');
            $table->boolean('is_visible')->default(true)->comment('Görünür mü?');

            // Statistics
            $table->integer('view_count')->default(0)->comment('İzlenme sayısı');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('video_type', 'idx_type');
            $table->index('sort_order', 'idx_sort');
            $table->index('is_featured', 'idx_featured');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse videoları da silinir');
        })
        ->comment('Ürün videoları - Tanıtım ve demo videoları');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_videos');
    }
};
