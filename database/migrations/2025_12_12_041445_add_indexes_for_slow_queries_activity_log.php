<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Slow query fix: activity_log count query taking 280ms
     * Query: SELECT COUNT(*) FROM activity_log WHERE created_at > ?
     * Solution: Add index on created_at column
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Index for created_at queries (pruning, reporting)
            $table->index('created_at', 'idx_activity_log_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('idx_activity_log_created_at');
        });
    }
};
