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
            $table->string('title', 255)->index();
            $table->string('slug')->unique();
            $table->text('body')->nullable();
            $table->text('css')->nullable();
            $table->text('js')->nullable();
            $table->string('metakey', 255)->nullable();
            $table->string('metadesc', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Yabancı anahtarlar
            $table->foreign('portfolio_category_id')->references('portfolio_category_id')->on('portfolio_categories')->onDelete('cascade');

            // Indexler
            $table->index('slug');
            $table->index('is_active');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at'], 'portfolios_active_deleted_idx');
            $table->index(['is_active', 'deleted_at', 'created_at'], 'portfolios_active_deleted_created_idx');
            $table->index(['portfolio_category_id', 'is_active', 'deleted_at', 'created_at'], 'portfolios_category_active_deleted_created_idx');
            $table->index(['slug', 'is_active', 'deleted_at'], 'portfolios_slug_active_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};