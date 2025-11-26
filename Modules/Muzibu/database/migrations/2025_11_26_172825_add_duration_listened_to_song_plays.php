<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('muzibu_song_plays')) {
            return;
        }

        Schema::table('muzibu_song_plays', function (Blueprint $table) {
            if (!Schema::hasColumn('muzibu_song_plays', 'duration_listened')) {
                $table->integer('duration_listened')->default(0)
                      ->after('user_id')
                      ->comment('Kaç saniye dinlendi (60+ = tam dinleme)');
            }
        });

        // Performans için composite index ekle
        if (!Schema::hasColumn('muzibu_song_plays', 'duration_listened')) {
            Schema::table('muzibu_song_plays', function (Blueprint $table) {
                $table->index(['user_id', 'created_at', 'duration_listened'], 'user_daily_plays_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('muzibu_song_plays')) {
            Schema::table('muzibu_song_plays', function (Blueprint $table) {
                $table->dropIndex('user_daily_plays_idx');
                $table->dropColumn('duration_listened');
            });
        }
    }
};
