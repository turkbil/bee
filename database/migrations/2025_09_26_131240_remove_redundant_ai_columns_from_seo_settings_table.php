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
            // Remove redundant columns - data already exists in analysis_results
            $table->dropColumn([
                'detailed_scores',  // Duplicated in analysis_results
                'overall_score'     // Duplicated in analysis_results
            ]);
        });

        \Log::info('🗑️ Redundant AI columns removed from seo_settings', [
            'removed_columns' => ['detailed_scores', 'overall_score'],
            'reason' => 'Data duplicated in analysis_results column'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            // Restore removed columns
            $table->integer('overall_score')->nullable();
            $table->json('detailed_scores')->nullable();
        });
    }
};