<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id('portfolio_id');                       // Özel primary key
            $table->unsignedBigInteger('portfolio_category_id'); // Kategori ID
            $table->json('title');                            // Sayfa başlığı (JSON) - Çok dilli
            $table->json('slug');                             // URL slug (JSON) - Çok dilli
            $table->json('body')->nullable();                 // Sayfa içeriği (JSON) - Çok dilli
            $table->string('image')->nullable();              // Resim alanı
            $table->string('client')->nullable();             // Müşteri adı
            $table->string('date')->nullable();               // Proje tarihi
            $table->string('url')->nullable();                // Proje URL'si
            $table->boolean('is_active')->default(true);      // Aktiflik durumu
            $table->timestamps();                             // created_at ve updated_at
            $table->softDeletes();                            // deleted_at

            // Yabancı anahtarlar
            $table->foreign('portfolio_category_id')->references('portfolio_category_id')->on('portfolio_categories')->onDelete('cascade');

            // Indexler
            $table->index('is_active');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at'], 'portfolios_active_deleted_idx');
            $table->index(['is_active', 'deleted_at', 'created_at'], 'portfolios_active_deleted_created_idx');
            $table->index(['portfolio_category_id', 'is_active', 'deleted_at', 'created_at'], 'portfolios_category_active_deleted_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};