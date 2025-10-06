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
        Schema::connection('central')->create('ai_input_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('ai_features')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_collapsible')->default(true);
            $table->boolean('is_expanded')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['feature_id', 'slug']);
            $table->index(['feature_id', 'sort_order']);
            // ADD indexes from 2025_08_10_040000_add_universal_input_system_indexes.php
            $table->index(['feature_id', 'is_collapsible'], 'ai_input_groups_feature_collapsible_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_input_groups');
    }
};