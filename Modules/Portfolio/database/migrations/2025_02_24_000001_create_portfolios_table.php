<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id('portfolio_id');
            $table->unsignedBigInteger('portfolio_category_id')->index();
            $table->json('title'); // Çok dilli destek
            $table->json('slug'); // Çok dilli destek
            $table->json('body')->nullable(); // Çok dilli destek
            $table->string('image')->nullable(); // Resim alanı
            $table->text('css')->nullable();
            $table->text('js')->nullable();
            $table->json('metakey')->nullable(); // Çok dilli destek
            $table->json('metadesc')->nullable(); // Çok dilli destek
            $table->json('seo')->nullable(); // SEO JSON column
            $table->string('client')->nullable();
            $table->string('date')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

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