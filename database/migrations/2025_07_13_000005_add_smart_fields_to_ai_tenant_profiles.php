<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SMART AI PROFILE SYSTEM - EXTEND EXISTING TABLE
 * 
 * Mevcut ai_tenant_profiles tablosuna smart fields ekler:
 * - Dynamic field scoring
 * - Advanced JSON structure  
 * - Smart calculation metadata
 * - Performance analytics
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            // SMART SCORING SYSTEM
            $table->json('smart_field_scores')->nullable()->after('additional_info');
            $table->json('field_calculation_metadata')->nullable()->after('smart_field_scores');
            $table->decimal('profile_completeness_score', 5, 2)->default(0.0)->after('field_calculation_metadata');
            $table->string('profile_quality_grade', 5)->default('F')->after('profile_completeness_score');
            
            // CONTEXT-AWARE SYSTEM
            $table->string('last_calculation_context', 50)->default('normal')->after('profile_quality_grade');
            $table->timestamp('scores_calculated_at')->nullable()->after('last_calculation_context');
            $table->json('context_performance')->nullable()->after('scores_calculated_at'); // Different context scores
            
            // SMART RECOMMENDATIONS
            $table->json('ai_recommendations')->nullable()->after('context_performance');
            $table->integer('missing_critical_fields')->default(0)->after('ai_recommendations');
            $table->json('field_quality_analysis')->nullable()->after('missing_critical_fields');
            
            // ADVANCED ANALYTICS
            $table->json('usage_analytics')->nullable()->after('field_quality_analysis');
            $table->integer('ai_interactions_count')->default(0)->after('usage_analytics');
            $table->timestamp('last_ai_interaction_at')->nullable()->after('ai_interactions_count');
            $table->decimal('avg_ai_response_quality', 3, 2)->default(0.0)->after('last_ai_interaction_at');
            
            // SMART VERSIONING
            $table->integer('profile_version')->default(1)->after('avg_ai_response_quality');
            $table->json('version_history')->nullable()->after('profile_version');
            $table->boolean('auto_optimization_enabled')->default(true)->after('version_history');
            
            // PERFORMANCE INDEXES
            $table->index(['tenant_id', 'profile_completeness_score'], 'idx_tenant_completeness');
            $table->index(['profile_quality_grade', 'is_completed'], 'idx_quality_completed');
            $table->index(['last_calculation_context', 'scores_calculated_at'], 'idx_context_timing');
            $table->index(['missing_critical_fields', 'is_active'], 'idx_critical_fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_tenant_completeness');
            $table->dropIndex('idx_quality_completed');
            $table->dropIndex('idx_context_timing');
            $table->dropIndex('idx_critical_fields');
            
            // Drop columns
            $table->dropColumn([
                'smart_field_scores',
                'field_calculation_metadata',
                'profile_completeness_score',
                'profile_quality_grade',
                'last_calculation_context',
                'scores_calculated_at',
                'context_performance',
                'ai_recommendations',
                'missing_critical_fields',
                'field_quality_analysis',
                'usage_analytics',
                'ai_interactions_count',
                'last_ai_interaction_at',
                'avg_ai_response_quality',
                'profile_version',
                'version_history',
                'auto_optimization_enabled'
            ]);
        });
    }
};