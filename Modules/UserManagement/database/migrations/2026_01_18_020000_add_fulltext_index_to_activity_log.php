<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists
        if (!Schema::hasTable('activity_log')) {
            return;
        }

        // Check if fulltext index already exists
        $indexExists = DB::select("SHOW INDEX FROM activity_log WHERE Key_name = 'activity_log_description_fulltext'");

        if (empty($indexExists)) {
            DB::statement('CREATE FULLTEXT INDEX activity_log_description_fulltext ON activity_log (description)');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('activity_log')) {
            return;
        }

        $indexExists = DB::select("SHOW INDEX FROM activity_log WHERE Key_name = 'activity_log_description_fulltext'");

        if (!empty($indexExists)) {
            DB::statement('DROP INDEX activity_log_description_fulltext ON activity_log');
        }
    }
};
