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
        if (Schema::hasTable('shop_product_images')) {
            return;
        }

        Schema::create('shop_product_images', function (Blueprint $table) {
            // Primary Key
            $table->id('product_image_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');
            $table->foreignId('product_variant_id')->nullable()->comment('Varyant ID - shop_product_variants ilişkisi (varyanta özel görsel)');

            // File Info
            $table->string('file_path')->comment('Dosya yolu');
            $table->string('file_name')->comment('Dosya adı');
            $table->string('mime_type')->nullable()->comment('MIME tipi (image/jpeg, image/png)');
            $table->integer('file_size')->nullable()->comment('Dosya boyutu (bytes)');

            // Image Dimensions
            $table->integer('width')->nullable()->comment('Genişlik (px)');
            $table->integer('height')->nullable()->comment('Yükseklik (px)');

            // Image Type
            $table->enum('image_type', ['main', 'gallery', 'thumbnail', 'variant', 'zoom'])
                  ->default('gallery')
                  ->comment('Görsel tipi: main=Ana görsel, gallery=Galeri, thumbnail=Küçük resim, variant=Varyant, zoom=Yakınlaştırma');

            // Alt Text (SEO & Accessibility)
            $table->json('alt_text')->nullable()->comment('Alt metni (JSON çoklu dil - {"tr":"Forklift ön görünüm"})');
            $table->json('title')->nullable()->comment('Başlık (JSON çoklu dil)');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->boolean('is_featured')->default(false)->comment('Öne çıkan görsel mi?');
            $table->boolean('is_visible')->default(true)->comment('Görünür mü?');

            // Color Tag (for variant selection)
            $table->string('color_code', 7)->nullable()->comment('Renk kodu (#FF5733 - varyant seçimi için)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('product_variant_id', 'idx_variant');
            $table->index('image_type', 'idx_type');
            $table->index('sort_order', 'idx_sort');
            $table->index('is_featured', 'idx_featured');
            $table->index(['product_id', 'sort_order'], 'idx_product_sort');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse görselleri de silinir');

            $table->foreign('product_variant_id')
                  ->references('variant_id')
                  ->on('shop_product_variants')
                  ->onDelete('cascade')
                  ->comment('Varyant silinirse görselleri de silinir');
        })
        ->comment('Ürün görselleri - Ana görsel, galeri, varyant görselleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_images');
    }
};
