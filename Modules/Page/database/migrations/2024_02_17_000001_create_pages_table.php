<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id('page_id');
            $table->string('title')->index();
            $table->string('slug')->unique();
            $table->longText('body')->nullable();
            $table->text('css')->nullable();
            $table->text('js')->nullable();
            $table->string('metakey')->nullable();
            $table->string('metadesc')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_homepage')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // Composite index'ler - Performans optimizasyonu
            // Ana sayfa sorgusu için ultra-optimize edilmiş index
            $table->index(['is_homepage', 'is_active', 'deleted_at'], 'pages_homepage_active_deleted_idx');
            // Alternatif homepage index
            $table->index(['is_homepage', 'deleted_at', 'is_active'], 'pages_homepage_deleted_active_idx');
            $table->index(['is_active', 'deleted_at', 'created_at'], 'pages_active_deleted_created_idx');
            $table->index(['slug', 'is_active', 'deleted_at'], 'pages_slug_active_deleted_idx');
            $table->index(['is_active', 'deleted_at'], 'pages_active_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};