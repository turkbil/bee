<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add slug_tr generated column if not exists
        if (!Schema::hasColumn('muzibu_songs', 'slug_tr')) {
            DB::statement("ALTER TABLE muzibu_songs ADD COLUMN slug_tr VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(slug, '\$.tr'))) STORED");
        }

        // Add slug_en generated column if not exists
        if (!Schema::hasColumn('muzibu_songs', 'slug_en')) {
            DB::statement("ALTER TABLE muzibu_songs ADD COLUMN slug_en VARCHAR(255) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(slug, '\$.en'))) STORED");
        }

        // Add indexes if not exist
        $indexTr = DB::select("SHOW INDEX FROM muzibu_songs WHERE Key_name = 'idx_songs_slug_tr'");
        if (empty($indexTr)) {
            DB::statement('CREATE INDEX idx_songs_slug_tr ON muzibu_songs (slug_tr)');
        }

        $indexEn = DB::select("SHOW INDEX FROM muzibu_songs WHERE Key_name = 'idx_songs_slug_en'");
        if (empty($indexEn)) {
            DB::statement('CREATE INDEX idx_songs_slug_en ON muzibu_songs (slug_en)');
        }
    }

    public function down(): void
    {
        // Drop indexes
        $indexTr = DB::select("SHOW INDEX FROM muzibu_songs WHERE Key_name = 'idx_songs_slug_tr'");
        if (!empty($indexTr)) {
            DB::statement('DROP INDEX idx_songs_slug_tr ON muzibu_songs');
        }

        $indexEn = DB::select("SHOW INDEX FROM muzibu_songs WHERE Key_name = 'idx_songs_slug_en'");
        if (!empty($indexEn)) {
            DB::statement('DROP INDEX idx_songs_slug_en ON muzibu_songs');
        }

        // Drop columns
        if (Schema::hasColumn('muzibu_songs', 'slug_tr')) {
            Schema::table('muzibu_songs', function ($table) {
                $table->dropColumn('slug_tr');
            });
        }

        if (Schema::hasColumn('muzibu_songs', 'slug_en')) {
            Schema::table('muzibu_songs', function ($table) {
                $table->dropColumn('slug_en');
            });
        }
    }
};
