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
        Schema::create('ai_feature_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('ai_features')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->enum('type', ['text', 'textarea', 'select', 'radio', 'checkbox', 'number', 'range', 'file']);
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedBigInteger('group_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->json('validation_rules')->nullable();
            $table->text('default_value')->nullable();
            $table->string('prompt_placeholder')->nullable();
            $table->json('config')->nullable();
            $table->json('conditional_logic')->nullable();
            $table->unsignedBigInteger('dynamic_data_source_id')->nullable();
            $table->timestamps();
            
            $table->unique(['feature_id', 'slug']);
            $table->index(['feature_id', 'sort_order']);
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feature_inputs');
    }
};