<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Universal Input System performance indexes
     */
    public function up(): void
    {
        Schema::table('ai_feature_inputs', function (Blueprint $table) {
            // Primary lookup indexes
            $table->index(['feature_id', 'is_primary'], 'ai_feature_inputs_feature_primary_idx');
            $table->index(['feature_id', 'sort_order'], 'ai_feature_inputs_feature_sort_idx');
            $table->index(['feature_id', 'slug'], 'ai_feature_inputs_feature_slug_idx');
            
            // Group and conditional indexes
            $table->index(['group_id', 'sort_order'], 'ai_feature_inputs_group_sort_idx');
            $table->index(['type', 'is_required'], 'ai_feature_inputs_type_required_idx');
            
            // Cache invalidation index
            $table->index(['feature_id', 'updated_at'], 'ai_feature_inputs_cache_invalidation_idx');
        });

        Schema::table('ai_input_options', function (Blueprint $table) {
            // Options lookup indexes
            $table->index(['input_id', 'value'], 'ai_input_options_input_value_idx');
            $table->index(['input_id', 'sort_order'], 'ai_input_options_input_sort_idx');
        });

        Schema::table('ai_input_groups', function (Blueprint $table) {
            // Group lookup indexes
            $table->index(['feature_id', 'sort_order'], 'ai_input_groups_feature_sort_idx');
            $table->index(['feature_id', 'is_collapsible'], 'ai_input_groups_feature_collapsible_idx');
        });

        Schema::table('ai_dynamic_data_sources', function (Blueprint $table) {
            // Data source lookup indexes
            $table->index(['source_type', 'is_active'], 'ai_dynamic_data_sources_type_active_idx');
            $table->index(['slug'], 'ai_dynamic_data_sources_slug_idx');
            $table->index(['cache_ttl', 'updated_at'], 'ai_dynamic_data_sources_cache_ttl_idx');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('ai_feature_inputs', function (Blueprint $table) {
            $table->dropIndex('ai_feature_inputs_feature_primary_idx');
            $table->dropIndex('ai_feature_inputs_feature_sort_idx');
            $table->dropIndex('ai_feature_inputs_feature_slug_idx');
            $table->dropIndex('ai_feature_inputs_group_sort_idx');
            $table->dropIndex('ai_feature_inputs_type_required_idx');
            $table->dropIndex('ai_feature_inputs_cache_invalidation_idx');
        });

        Schema::table('ai_input_options', function (Blueprint $table) {
            $table->dropIndex('ai_input_options_input_value_idx');
            $table->dropIndex('ai_input_options_input_sort_idx');
        });

        Schema::table('ai_input_groups', function (Blueprint $table) {
            $table->dropIndex('ai_input_groups_feature_sort_idx');
            $table->dropIndex('ai_input_groups_feature_collapsible_idx');
        });

        Schema::table('ai_dynamic_data_sources', function (Blueprint $table) {
            $table->dropIndex('ai_dynamic_data_sources_type_active_idx');
            $table->dropIndex('ai_dynamic_data_sources_slug_idx');
            $table->dropIndex('ai_dynamic_data_sources_cache_ttl_idx');
        });
    }
};