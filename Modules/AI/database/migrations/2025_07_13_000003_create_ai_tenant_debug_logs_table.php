<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AI TENANT DEBUG LOGS - Gelişmiş Analytics
 * 
 * Bu tablo tüm AI kullanımlarını detaylı şekilde kaydeder:
 * - Hangi tenant hangi prompt'ları kullandı
 * - Priority sistemi nasıl çalıştı
 * - Hangi prompt'lar filtrelendi ve neden
 * - Performance metrikleri
 * - Interactive dashboard için detaylı bilgiler
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('central')->create('ai_tenant_debug_logs', function (Blueprint $table) {
            $table->id();
            
            // Tenant & User Info
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id', 100)->nullable();
            
            // Request Context
            $table->string('feature_slug', 100)->index();
            $table->string('request_type', 100)->index();
            $table->string('context_type', 50)->default('normal'); // minimal, normal, detailed
            
            // Prompt Analysis (Ana veri - JSON)
            $table->json('prompts_analysis'); // Detaylı prompt analizi
            $table->json('scoring_summary'); // Score özeti
            
            // Quick Stats (Hızlı query için)
            $table->integer('threshold_used')->index();
            $table->tinyInteger('total_available_prompts');
            $table->tinyInteger('actually_used_prompts')->index();
            $table->tinyInteger('filtered_prompts');
            $table->integer('highest_score');
            $table->integer('lowest_used_score');
            
            // Performance Metrics
            $table->integer('execution_time_ms')->index();
            $table->integer('response_length')->nullable();
            $table->integer('token_usage')->nullable();
            $table->decimal('cost_estimate', 8, 4)->nullable();
            
            // Input/Output (Privacy-safe)
            $table->string('input_hash', 64)->nullable(); // MD5 hash
            $table->text('input_preview')->nullable(); // İlk 100 karakter
            $table->text('response_preview')->nullable(); // İlk 200 karakter
            $table->enum('response_quality', ['excellent', 'good', 'average', 'poor'])->nullable();
            
            // Technical Info
            $table->string('ai_model', 50)->default('deepseek-chat');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('request_headers')->nullable();
            
            // Error Tracking
            $table->boolean('has_error')->default(false)->index();
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable();
            
            $table->timestamps();
            
            // Composite Indexes for Dashboard Queries
            $table->index(['tenant_id', 'created_at'], 'idx_tenant_timeline');
            $table->index(['feature_slug', 'created_at'], 'idx_feature_timeline');
            $table->index(['request_type', 'execution_time_ms'], 'idx_performance');
            $table->index(['actually_used_prompts', 'highest_score'], 'idx_prompt_efficiency');
            $table->index(['has_error', 'created_at'], 'idx_error_tracking');
            
            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_tenant_debug_logs');
    }
};