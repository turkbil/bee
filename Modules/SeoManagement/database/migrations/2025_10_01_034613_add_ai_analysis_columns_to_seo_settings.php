<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            // AI SEO Analysis Results - Missing columns
            if (!Schema::hasColumn('seo_settings', 'detailed_scores')) {
                $table->json('detailed_scores')->nullable()->after('analysis_date');
            }
            if (!Schema::hasColumn('seo_settings', 'strengths')) {
                $table->json('strengths')->nullable()->after('detailed_scores');
            }
            if (!Schema::hasColumn('seo_settings', 'improvements')) {
                $table->json('improvements')->nullable()->after('strengths');
            }
            if (!Schema::hasColumn('seo_settings', 'action_items')) {
                $table->json('action_items')->nullable()->after('improvements');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn(['detailed_scores', 'strengths', 'improvements', 'action_items']);
        });
    }
};
