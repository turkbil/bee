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
        Schema::connection('central')->create('ai_dynamic_data_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('source_type', ['static', 'database', 'api', 'cache']);
            $table->json('source_config');
            $table->integer('cache_ttl')->default(3600);
            $table->timestamp('last_updated')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('source_type');
            $table->index('is_active');
            // ADD indexes from 2025_08_10_040000_add_universal_input_system_indexes.php
            $table->index(['source_type', 'is_active'], 'ai_dynamic_data_sources_type_active_idx');
            $table->index(['slug'], 'ai_dynamic_data_sources_slug_idx');
            $table->index(['cache_ttl', 'updated_at'], 'ai_dynamic_data_sources_cache_ttl_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_dynamic_data_sources');
    }
};