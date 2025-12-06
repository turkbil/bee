<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop virtual search columns - Meilisearch will be used
     */
    public function up(): void
    {
        // SONGS: Drop virtual columns + indexes
        DB::statement("ALTER TABLE muzibu_songs DROP INDEX idx_songs_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_songs DROP INDEX idx_songs_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_songs DROP INDEX idx_songs_title_ar_lower, DROP COLUMN title_ar_lower");

        // ARTISTS: Drop virtual columns + indexes
        DB::statement("ALTER TABLE muzibu_artists DROP INDEX idx_artists_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_artists DROP INDEX idx_artists_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_artists DROP INDEX idx_artists_title_ar_lower, DROP COLUMN title_ar_lower");

        // ALBUMS: Drop virtual columns + indexes
        DB::statement("ALTER TABLE muzibu_albums DROP INDEX idx_albums_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_albums DROP INDEX idx_albums_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_albums DROP INDEX idx_albums_title_ar_lower, DROP COLUMN title_ar_lower");

        // GENRES: Drop virtual columns + indexes
        DB::statement("ALTER TABLE muzibu_genres DROP INDEX idx_genres_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_genres DROP INDEX idx_genres_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_genres DROP INDEX idx_genres_title_ar_lower, DROP COLUMN title_ar_lower");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible - Meilisearch migration
    }
};
