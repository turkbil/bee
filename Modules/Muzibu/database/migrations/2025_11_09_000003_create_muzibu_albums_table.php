<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('muzibu_albums')) {
            return;
        }

        Schema::create('muzibu_albums', function (Blueprint $table) {
            $table->id('album_id');
            $table->foreignId('artist_id')->nullable()->constrained('muzibu_artists', 'artist_id')->nullOnDelete();
            $table->json('title')->comment('Çoklu dil albüm adı: {"tr": "Albüm", "en": "Album"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "album", "en": "album"}');
            $table->json('description')->nullable()->comment('Çoklu dil açıklama: {"tr": "Açıklama", "en": "Description"}');
            $table->unsignedBigInteger('media_id')->nullable()->comment('Thumbmaker media ID (album cover)');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('media_id')->references('id')->on('media')->nullOnDelete();

            // İlave indeksler
            $table->index('artist_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');

            // Composite index'ler
            $table->index(['is_active', 'deleted_at', 'created_at'], 'muzibu_albums_active_deleted_created_idx');
            $table->index(['is_active', 'deleted_at'], 'muzibu_albums_active_deleted_idx');
            $table->index(['artist_id', 'is_active', 'deleted_at'], 'muzibu_albums_artist_active_deleted_idx');
        });

        // JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Disabled for compatibility
        // Note: JSON functional indexes disabled for broader database compatibility
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_albums');
    }
};
