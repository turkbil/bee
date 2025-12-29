<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - AI Credit Usage & Debug Tables for Tenant Database
     */
    public function up(): void
    {
        // 1. AI Credit Usage table
        if (!Schema::hasTable('ai_credit_usage')) {
            Schema::create('ai_credit_usage', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('conversation_id')->nullable()->index();
                $table->unsignedBigInteger('message_id')->nullable()->index();
                $table->unsignedBigInteger('ai_provider_id')->nullable()->index();

                $table->string('provider_name')->nullable()->index();
                $table->string('model')->nullable();

                $table->decimal('credits_used', 10, 4)->default(0);
                $table->integer('input_tokens')->default(0);
                $table->integer('output_tokens')->default(0);
                $table->decimal('credit_cost', 10, 4)->default(0);
                $table->decimal('cost_multiplier', 8, 4)->default(1.0);

                $table->string('usage_type')->default('chat')->index();
                $table->string('feature_slug')->nullable()->index();
                $table->string('purpose')->nullable();
                $table->text('description')->nullable();
                $table->string('reference_id')->nullable();

                $table->json('response_metadata')->nullable();
                $table->json('metadata')->nullable();

                $table->timestamp('used_at')->index();
                $table->timestamps();

                // Composite indexes for performance
                $table->index(['tenant_id', 'used_at'], 'idx_tenant_date');
                $table->index(['user_id', 'used_at'], 'idx_user_date');
                $table->index(['ai_provider_id', 'used_at'], 'idx_provider_date');
                $table->index(['provider_name', 'used_at'], 'idx_provider_name_date');
                $table->index(['feature_slug', 'used_at'], 'idx_feature_date');
                $table->index(['usage_type', 'used_at'], 'idx_type_date');
                $table->index(['conversation_id'], 'idx_conversation');
            });
        }

        // 2. AI Tenant Debug Logs table
        if (!Schema::hasTable('ai_tenant_debug_logs')) {
            Schema::create('ai_tenant_debug_logs', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('session_id', 100)->nullable();

                $table->string('feature_slug', 100)->index();
                $table->string('request_type', 100)->index();
                $table->string('context_type', 50)->default('normal');

                $table->json('prompts_analysis');
                $table->json('scoring_summary');

                $table->integer('threshold_used')->index();
                $table->tinyInteger('total_available_prompts');
                $table->tinyInteger('actually_used_prompts')->index();
                $table->tinyInteger('filtered_prompts');

                $table->integer('highest_score');
                $table->integer('lowest_used_score');
                $table->integer('execution_time_ms')->index();

                $table->integer('response_length')->nullable();
                $table->integer('token_usage')->nullable();
                $table->decimal('cost_estimate', 8, 4)->nullable();

                $table->string('input_hash', 64)->nullable();
                $table->text('input_preview')->nullable();
                $table->text('response_preview')->nullable();
                $table->enum('response_quality', ['excellent', 'good', 'average', 'poor'])->nullable();

                $table->string('ai_model', 50)->default('deepseek-chat');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('request_headers')->nullable();

                $table->boolean('has_error')->default(false)->index();
                $table->text('error_message')->nullable();
                $table->json('error_details')->nullable();

                $table->timestamps();

                // Composite indexes for performance
                $table->index(['tenant_id', 'created_at'], 'idx_tenant_timeline');
                $table->index(['feature_slug', 'created_at'], 'idx_feature_timeline');
                $table->index(['request_type', 'execution_time_ms'], 'idx_performance');
                $table->index(['actually_used_prompts', 'highest_score'], 'idx_prompt_efficiency');
                $table->index(['has_error', 'created_at'], 'idx_error_tracking');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tenant_debug_logs');
        Schema::dropIfExists('ai_credit_usage');
    }
};
