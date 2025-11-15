<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove status column - using is_active + published_at for blog state
     * Logic: is_active=false → Draft
     *        is_active=true + published_at=null → Published
     *        is_active=true + published_at>now → Scheduled
     */
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('blogs_status_active_published_idx');
            $table->dropIndex('blogs_featured_status_published_idx');

            // Drop status column
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Re-add status column
            $table->enum('status', ['draft', 'published', 'scheduled'])
                  ->default('draft')
                  ->after('is_featured')
                  ->index()
                  ->comment('Yazı durumu');

            // Re-create indexes
            $table->index(['status', 'is_active', 'published_at'], 'blogs_status_active_published_idx');
            $table->index(['is_featured', 'status', 'published_at'], 'blogs_featured_status_published_idx');
        });
    }
};
