<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muzibu_playlist_radio', function (Blueprint $table) {
            $table->foreignId('playlist_id')->constrained('muzibu_playlists', 'playlist_id')->cascadeOnDelete();
            $table->foreignId('radio_id')->constrained('muzibu_radios', 'radio_id')->cascadeOnDelete();

            // Primary key
            $table->primary(['playlist_id', 'radio_id']);

            // İlave indeksler
            $table->index('radio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_playlist_radio');
    }
};
