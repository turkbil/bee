<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muzibu_songs', function (Blueprint $table) {
            $table->id('song_id');
            $table->foreignId('album_id')->nullable()->constrained('muzibu_albums', 'album_id')->nullOnDelete();
            $table->foreignId('genre_id')->constrained('muzibu_genres', 'genre_id')->cascadeOnDelete();
            $table->json('title')->comment('Çoklu dil şarkı adı: {"tr": "Şarkı", "en": "Song"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "sarki", "en": "song"}');
            $table->json('lyrics')->nullable()->comment('Çoklu dil şarkı sözleri: {"tr": "Sözler", "en": "Lyrics"}');
            $table->integer('duration')->default(0)->comment('Duration in seconds');
            $table->string('file_path')->nullable()->comment('Audio file path (storage/muzibu/songs/)');
            $table->unsignedBigInteger('media_id')->nullable()->comment('Thumbmaker media ID (song cover)');
            $table->boolean('is_featured')->default(false)->index()->comment('Featured/öne çıkan şarkılar');
            $table->unsignedInteger('play_count')->default(0)->index()->comment('Total play count');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('media_id')->references('id')->on('media')->nullOnDelete();

            // İlave indeksler
            $table->index('album_id');
            $table->index('genre_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('duration');

            // Composite index'ler - Performans optimizasyonu
            $table->index(['is_active', 'deleted_at', 'created_at'], 'muzibu_songs_active_deleted_created_idx');
            $table->index(['is_active', 'deleted_at'], 'muzibu_songs_active_deleted_idx');
            $table->index(['is_featured', 'is_active', 'deleted_at'], 'muzibu_songs_featured_active_deleted_idx');
            $table->index(['genre_id', 'is_active', 'deleted_at'], 'muzibu_songs_genre_active_deleted_idx');
            $table->index(['album_id', 'is_active', 'deleted_at'], 'muzibu_songs_album_active_deleted_idx');
            $table->index(['play_count', 'is_active', 'deleted_at'], 'muzibu_songs_playcount_active_deleted_idx');
        });

        // JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Disabled for compatibility
        // Note: JSON functional indexes disabled for broader database compatibility
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_songs');
    }
};
