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
        if (Schema::hasTable('shop_product_documents')) {
            return;
        }

        Schema::create('shop_product_documents', function (Blueprint $table) {
            // Primary Key
            $table->id('product_document_id');

            // Relations
            $table->foreignId('product_id')->comment('Ürün ID - shop_products ilişkisi');

            // Document Type
            $table->enum('document_type', [
                'manual',           // Kullanım kılavuzu
                'brochure',         // Broşür
                'specification',    // Teknik şartname
                'certificate',      // Sertifika
                'warranty',         // Garanti belgesi
                'safety',           // Güvenlik belgesi
                'datasheet',        // Veri sayfası
                'video_guide',      // Video kılavuz
                'other'             // Diğer
            ])->default('manual')->comment('Döküman tipi');

            // File Info
            $table->string('file_path')->comment('Dosya yolu');
            $table->string('file_name')->comment('Dosya adı');
            $table->string('mime_type')->nullable()->comment('MIME tipi (application/pdf, image/jpeg)');
            $table->integer('file_size')->nullable()->comment('Dosya boyutu (bytes)');
            $table->string('file_extension', 10)->nullable()->comment('Dosya uzantısı (pdf, jpg, docx)');

            // Document Info
            $table->json('title')->comment('Döküman başlığı (JSON çoklu dil)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Language
            $table->string('language_code', 5)->default('tr')->comment('Dil kodu (tr, en, de)');

            // Version
            $table->string('version')->nullable()->comment('Versiyon (v1.0, 2024-Q1)');
            $table->date('published_date')->nullable()->comment('Yayın tarihi');

            // Access Control
            $table->boolean('requires_login')->default(false)->comment('İndirmek için giriş gerekli mi?');
            $table->boolean('requires_purchase')->default(false)->comment('İndirmek için satın alma gerekli mi?');
            $table->json('customer_group_ids')->nullable()->comment('Sadece belirli müşteri gruplarına özel (JSON array)');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->boolean('is_featured')->default(false)->comment('Öne çıkan döküman mı?');
            $table->boolean('is_visible')->default(true)->comment('Görünür mü?');

            // Statistics
            $table->integer('download_count')->default(0)->comment('İndirilme sayısı');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('product_id', 'idx_product');
            $table->index('document_type', 'idx_type');
            $table->index('language_code', 'idx_language');
            $table->index('sort_order', 'idx_sort');
            $table->index('is_featured', 'idx_featured');

            // Foreign Keys
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('shop_products')
                  ->onDelete('cascade')
                  ->comment('Ürün silinirse dökümanları da silinir');
        })
        ->comment('Ürün dökümanları - Katalog, kılavuz, sertifika vb. dosyalar');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_product_documents');
    }
};
