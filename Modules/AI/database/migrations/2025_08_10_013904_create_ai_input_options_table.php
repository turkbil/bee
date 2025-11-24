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
        if (Schema::hasTable('ai_input_options')) {
            return;
        }

        Schema::create('ai_input_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('input_id')->constrained('ai_feature_inputs')->onDelete('cascade');
            $table->string('label');
            $table->string('value');
            $table->text('prompt_value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->json('conditions')->nullable();
            $table->timestamps();
            
            $table->index(['input_id', 'sort_order']);
            // ADD indexes from 2025_08_10_040000_add_universal_input_system_indexes.php
            $table->index(['input_id', 'value'], 'ai_input_options_input_value_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_input_options');
    }
};