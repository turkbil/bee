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
     * JSON field'larda hızlı arama için virtual generated columns + index ekler
     * 25,000+ şarkı için query optimizasyonu
     */
    public function up(): void
    {
        // SONGS: title (tr, en, ar) için virtual columns + indexes
        DB::statement("
            ALTER TABLE muzibu_songs
            ADD COLUMN title_tr_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')))) VIRTUAL,
            ADD INDEX idx_songs_title_tr_lower (title_tr_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_songs
            ADD COLUMN title_en_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')))) VIRTUAL,
            ADD INDEX idx_songs_title_en_lower (title_en_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_songs
            ADD COLUMN title_ar_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.ar')))) VIRTUAL,
            ADD INDEX idx_songs_title_ar_lower (title_ar_lower)
        ");

        // ARTISTS: title (tr, en, ar) için virtual columns + indexes
        DB::statement("
            ALTER TABLE muzibu_artists
            ADD COLUMN title_tr_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')))) VIRTUAL,
            ADD INDEX idx_artists_title_tr_lower (title_tr_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_artists
            ADD COLUMN title_en_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')))) VIRTUAL,
            ADD INDEX idx_artists_title_en_lower (title_en_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_artists
            ADD COLUMN title_ar_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.ar')))) VIRTUAL,
            ADD INDEX idx_artists_title_ar_lower (title_ar_lower)
        ");

        // ALBUMS: title (tr, en, ar) için virtual columns + indexes
        DB::statement("
            ALTER TABLE muzibu_albums
            ADD COLUMN title_tr_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')))) VIRTUAL,
            ADD INDEX idx_albums_title_tr_lower (title_tr_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_albums
            ADD COLUMN title_en_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')))) VIRTUAL,
            ADD INDEX idx_albums_title_en_lower (title_en_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_albums
            ADD COLUMN title_ar_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.ar')))) VIRTUAL,
            ADD INDEX idx_albums_title_ar_lower (title_ar_lower)
        ");

        // GENRES: title (tr, en, ar) için virtual columns + indexes
        DB::statement("
            ALTER TABLE muzibu_genres
            ADD COLUMN title_tr_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')))) VIRTUAL,
            ADD INDEX idx_genres_title_tr_lower (title_tr_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_genres
            ADD COLUMN title_en_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')))) VIRTUAL,
            ADD INDEX idx_genres_title_en_lower (title_en_lower)
        ");

        DB::statement("
            ALTER TABLE muzibu_genres
            ADD COLUMN title_ar_lower VARCHAR(255)
            GENERATED ALWAYS AS (LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.ar')))) VIRTUAL,
            ADD INDEX idx_genres_title_ar_lower (title_ar_lower)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Songs
        DB::statement("ALTER TABLE muzibu_songs DROP INDEX idx_songs_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_songs DROP INDEX idx_songs_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_songs DROP INDEX idx_songs_title_ar_lower, DROP COLUMN title_ar_lower");

        // Artists
        DB::statement("ALTER TABLE muzibu_artists DROP INDEX idx_artists_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_artists DROP INDEX idx_artists_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_artists DROP INDEX idx_artists_title_ar_lower, DROP COLUMN title_ar_lower");

        // Albums
        DB::statement("ALTER TABLE muzibu_albums DROP INDEX idx_albums_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_albums DROP INDEX idx_albums_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_albums DROP INDEX idx_albums_title_ar_lower, DROP COLUMN title_ar_lower");

        // Genres
        DB::statement("ALTER TABLE muzibu_genres DROP INDEX idx_genres_title_tr_lower, DROP COLUMN title_tr_lower");
        DB::statement("ALTER TABLE muzibu_genres DROP INDEX idx_genres_title_en_lower, DROP COLUMN title_en_lower");
        DB::statement("ALTER TABLE muzibu_genres DROP INDEX idx_genres_title_ar_lower, DROP COLUMN title_ar_lower");
    }
};
