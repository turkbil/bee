<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Playlistables - Polymorphic Pivot Table (Tenant)
     *
     * Playlist'lerin farklı entity'lere (sector, radio, corporate, mood vb.)
     * dağıtımını tek bir tabloda yönetir.
     *
     * Mevcut tablolar (playlist_sector, playlist_radio) bu tabloya migrate edilecek.
     */
    public function up(): void
    {
        Schema::create('muzibu_playlistables', function (Blueprint $table) {
            $table->id();

            // Playlist foreign key
            $table->foreignId('playlist_id')
                  ->constrained('muzibu_playlists', 'playlist_id')
                  ->cascadeOnDelete();

            // Polymorphic relation - Sector, Radio, Corporate, Mood vb.
            $table->string('playlistable_type', 50); // Kısa morph alias: sector, radio, corporate, mood
            $table->unsignedBigInteger('playlistable_id');

            // Ek alanlar
            $table->unsignedInteger('position')->default(0); // Sıralama
            $table->timestamps();

            // Indexes
            $table->index(['playlistable_type', 'playlistable_id'], 'playlistables_morph_index');
            $table->index('playlist_id');
            $table->index('position');

            // Unique constraint - Aynı playlist aynı entity'ye bir kez bağlanabilir
            $table->unique(
                ['playlist_id', 'playlistable_type', 'playlistable_id'],
                'playlistables_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_playlistables');
    }
};
