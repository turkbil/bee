<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muzibu_playlist_song', function (Blueprint $table) {
            $table->foreignId('playlist_id')->constrained('muzibu_playlists', 'playlist_id')->cascadeOnDelete();
            $table->foreignId('song_id')->constrained('muzibu_songs', 'song_id')->cascadeOnDelete();
            $table->integer('position')->default(0)->comment('Sort order in playlist');
            $table->timestamps();

            // Primary key
            $table->primary(['playlist_id', 'song_id']);

            // İlave indeksler
            $table->index('song_id');
            $table->index('position');
            $table->index(['playlist_id', 'position'], 'muzibu_playlist_song_playlist_position_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_playlist_song');
    }
};
