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
        Schema::create('ai_credit_usage', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id'); // String for tenant compatibility
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('conversation_id')->nullable(); // AI konuşma referansı
            $table->unsignedBigInteger('message_id')->nullable(); // Spesifik mesaj referansı
            
            // Provider tracking (AI-v2 plan)
            $table->unsignedBigInteger('ai_provider_id')->nullable(); // Provider referansı
            $table->string('provider_name')->nullable(); // Provider adı (OpenAI, Anthropic, etc)
            $table->string('model')->nullable(); // Kullanılan AI modeli (gpt-4, claude-3, etc)
            
            // Credit & Token tracking
            $table->decimal('credits_used', 10, 4)->default(0); // Ana kredi miktarı
            $table->integer('input_tokens')->default(0); // Gelen token sayısı (eski prompt_credits)
            $table->integer('output_tokens')->default(0); // Dönen token sayısı (eski completion_credits)
            $table->decimal('credit_cost', 10, 4)->default(0); // Gerçek maliyet
            $table->decimal('cost_multiplier', 8, 4)->default(1.0000); // Provider çarpanı
            
            // Usage classification  
            $table->string('usage_type')->default('chat'); // "chat", "feature", "api" vs.
            $table->string('feature_slug')->nullable(); // Hangi AI feature kullanıldı
            $table->string('purpose')->nullable(); // Amaç (chat, test, admin vs)
            $table->text('description')->nullable(); // Kullanım açıklaması
            $table->string('reference_id')->nullable(); // İlgili conversation/message ID
            
            // Enhanced metadata (AI-v2 plan)
            $table->json('response_metadata')->nullable(); // Provider response data
            $table->json('metadata')->nullable(); // Ek bilgiler
            
            $table->timestamp('used_at');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('set null');
            $table->foreign('message_id')->references('id')->on('ai_messages')->onDelete('set null');
            $table->foreign('ai_provider_id')->references('id')->on('ai_providers')->onDelete('set null');
            
            // Indexes for performance (AI-v2 plan)
            $table->index(['tenant_id', 'used_at'], 'idx_tenant_date');
            $table->index(['user_id', 'used_at'], 'idx_user_date');
            $table->index(['ai_provider_id', 'used_at'], 'idx_provider_date');
            $table->index(['provider_name', 'used_at'], 'idx_provider_name_date');
            $table->index(['feature_slug', 'used_at'], 'idx_feature_date');
            $table->index(['usage_type', 'used_at'], 'idx_type_date');
            $table->index(['conversation_id'], 'idx_conversation');
            $table->index(['used_at'], 'idx_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credit_usage');
    }
};
