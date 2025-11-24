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
        if (Schema::hasTable('ai_usage_analytics')) {
            return;
        }

        Schema::create('ai_usage_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_id');
            $table->string('module_name', 50)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('action_type', 50);
            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->json('prompt_chain_used')->nullable();
            $table->integer('tokens_used')->default(0);
            $table->integer('response_time_ms')->default(0);
            $table->boolean('cache_hit')->default(false);
            $table->boolean('success')->default(true);
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Index'ler
            $table->index(['feature_id', 'user_id'], 'idx_feature_user');
            $table->index(['module_name', 'action_type'], 'idx_module_action');
            $table->index(['created_at'], 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_analytics');
    }
};