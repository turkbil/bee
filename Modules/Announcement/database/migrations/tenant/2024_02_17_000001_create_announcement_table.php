<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id');
            $table->text('title');
            $table->text('slug');
            $table->longText('body')->nullable();
            $table->text('metakey')->nullable();
            $table->text('metadesc')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at'], 'announcements_active_deleted_idx');
            $table->index(['is_active', 'deleted_at', 'created_at'], 'announcements_active_deleted_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};