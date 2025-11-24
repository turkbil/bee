<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('muzibu_playlist_sector')) {
            return;
        }

        Schema::create('muzibu_playlist_sector', function (Blueprint $table) {
            $table->foreignId('playlist_id')->constrained('muzibu_playlists', 'playlist_id')->cascadeOnDelete();
            $table->foreignId('sector_id')->constrained('muzibu_sectors', 'sector_id')->cascadeOnDelete();

            // Primary key
            $table->primary(['playlist_id', 'sector_id']);

            // İlave indeksler
            $table->index('sector_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_playlist_sector');
    }
};
