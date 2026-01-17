<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if index already exists
        $indexExists = DB::select("SHOW INDEX FROM muzibu_song_plays WHERE Key_name = 'song_plays_created_user_duration_idx'");

        if (empty($indexExists)) {
            DB::statement('CREATE INDEX song_plays_created_user_duration_idx ON muzibu_song_plays (created_at, user_id, listened_duration)');
        }
    }

    public function down(): void
    {
        $indexExists = DB::select("SHOW INDEX FROM muzibu_song_plays WHERE Key_name = 'song_plays_created_user_duration_idx'");

        if (!empty($indexExists)) {
            DB::statement('DROP INDEX song_plays_created_user_duration_idx ON muzibu_song_plays');
        }
    }
};
