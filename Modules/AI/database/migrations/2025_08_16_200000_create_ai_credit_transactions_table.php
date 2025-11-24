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
        if (Schema::hasTable('ai_credit_transactions')) {
            return;
        }

        Schema::create('ai_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('provider'); // openai, anthropic, deepseek, etc.
            $table->string('model'); // gpt-4o, claude-3-sonnet, deepseek-chat, etc.
            $table->integer('input_tokens');
            $table->integer('output_tokens');
            $table->integer('total_tokens');
            $table->decimal('credits_used', 10, 4);
            $table->decimal('cost_per_token', 10, 6)->nullable();
            $table->string('transaction_type')->default('ai_usage'); // ai_usage, translation, chat, content_generation
            $table->string('feature_name')->nullable(); // page_translation, ai_chat, seo_analysis
            $table->json('metadata')->nullable(); // Additional context data, prompt info, etc.
            $table->timestamp('processed_at');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['tenant_id', 'created_at']);
            $table->index(['provider', 'model']);
            $table->index('transaction_type');
            $table->index('feature_name');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credit_transactions');
    }
};