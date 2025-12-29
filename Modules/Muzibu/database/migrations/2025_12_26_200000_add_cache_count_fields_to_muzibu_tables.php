<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cache count fields for performance optimization
     *
     * Bu migration şu tabloları günceller:
     * - muzibu_albums: songs_count, total_duration
     * - muzibu_playlists: songs_count, total_duration
     * - muzibu_genres: songs_count, total_duration
     * - muzibu_artists: albums_count, songs_count, total_duration
     *
     * NULL = henüz hesaplanmamış (lazy calculation ile doldurulacak)
     * 0 = hesaplandı, gerçekten 0
     */
    public function up(): void
    {
        // Albums
        if (Schema::hasTable('muzibu_albums')) {
            Schema::table('muzibu_albums', function (Blueprint $table) {
                if (!Schema::hasColumn('muzibu_albums', 'songs_count')) {
                    $table->unsignedInteger('songs_count')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('muzibu_albums', 'total_duration')) {
                    $table->unsignedInteger('total_duration')->nullable()->after('songs_count')
                        ->comment('Total duration in seconds');
                }
            });
        }

        // Playlists
        if (Schema::hasTable('muzibu_playlists')) {
            Schema::table('muzibu_playlists', function (Blueprint $table) {
                if (!Schema::hasColumn('muzibu_playlists', 'songs_count')) {
                    $table->unsignedInteger('songs_count')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('muzibu_playlists', 'total_duration')) {
                    $table->unsignedInteger('total_duration')->nullable()->after('songs_count')
                        ->comment('Total duration in seconds');
                }
            });
        }

        // Genres
        if (Schema::hasTable('muzibu_genres')) {
            Schema::table('muzibu_genres', function (Blueprint $table) {
                if (!Schema::hasColumn('muzibu_genres', 'songs_count')) {
                    $table->unsignedInteger('songs_count')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('muzibu_genres', 'total_duration')) {
                    $table->unsignedInteger('total_duration')->nullable()->after('songs_count')
                        ->comment('Total duration in seconds');
                }
            });
        }

        // Artists
        if (Schema::hasTable('muzibu_artists')) {
            Schema::table('muzibu_artists', function (Blueprint $table) {
                if (!Schema::hasColumn('muzibu_artists', 'albums_count')) {
                    $table->unsignedInteger('albums_count')->nullable()->after('is_active');
                }
                if (!Schema::hasColumn('muzibu_artists', 'songs_count')) {
                    $table->unsignedInteger('songs_count')->nullable()->after('albums_count');
                }
                if (!Schema::hasColumn('muzibu_artists', 'total_duration')) {
                    $table->unsignedInteger('total_duration')->nullable()->after('songs_count')
                        ->comment('Total duration in seconds');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('muzibu_albums')) {
            Schema::table('muzibu_albums', function (Blueprint $table) {
                $table->dropColumn(['songs_count', 'total_duration']);
            });
        }

        if (Schema::hasTable('muzibu_playlists')) {
            Schema::table('muzibu_playlists', function (Blueprint $table) {
                $table->dropColumn(['songs_count', 'total_duration']);
            });
        }

        if (Schema::hasTable('muzibu_genres')) {
            Schema::table('muzibu_genres', function (Blueprint $table) {
                $table->dropColumn(['songs_count', 'total_duration']);
            });
        }

        if (Schema::hasTable('muzibu_artists')) {
            Schema::table('muzibu_artists', function (Blueprint $table) {
                $table->dropColumn(['albums_count', 'songs_count', 'total_duration']);
            });
        }
    }
};
