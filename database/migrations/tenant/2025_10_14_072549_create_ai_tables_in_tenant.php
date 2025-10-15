<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - AI tables for tenant database
     */
    public function up(): void
    {
        // 1. AI Conversations table
        if (!Schema::hasTable('ai_conversations')) {
            Schema::create('ai_conversations', function (Blueprint $table) {
                $table->id();
                $table->string('session_id')->nullable()->index(); // Frontend session ID (IP-based hash)
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
                $table->string('feature_slug')->nullable()->index(); // shop-assistant, support-chat, etc.

                $table->string('type')->default('chat')->index(); // chat, feature_test, admin_chat
                $table->string('status')->default('active')->index(); // active, archived, deleted
                $table->boolean('is_demo')->default(false);
                $table->boolean('is_active')->default(true);

                $table->integer('total_tokens_used')->default(0);
                $table->integer('message_count')->default(0);

                $table->json('context_data')->nullable(); // IP, user_agent, device, etc.
                $table->json('metadata')->nullable(); // Additional metadata

                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();

                // Composite indexes for performance
                $table->index(['tenant_id', 'session_id'], 'ai_conv_tenant_session_idx');
                $table->index(['user_id', 'created_at'], 'ai_conv_user_created_idx');
                $table->index(['feature_slug', 'created_at'], 'ai_conv_feature_created_idx');
            });
        }

        // 2. AI Messages table
        if (!Schema::hasTable('ai_messages')) {
            Schema::create('ai_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('ai_conversations')->onDelete('cascade');

                $table->enum('role', ['user', 'assistant', 'system'])->index();
                $table->text('content');

                $table->string('model')->nullable(); // gpt-5, gpt-4o-mini, claude-haiku, deepseek
                $table->integer('tokens_used')->default(0);
                $table->integer('prompt_tokens')->default(0);
                $table->integer('completion_tokens')->default(0);

                $table->json('context_data')->nullable(); // product_id, category_id, page_slug
                $table->json('metadata')->nullable();

                $table->timestamps();

                // Indexes
                $table->index(['conversation_id', 'created_at'], 'ai_msg_conv_created_idx');
                $table->index('role');
            });
        }

        // 3. Product Chat Placeholders table
        if (!Schema::hasTable('product_chat_placeholders')) {
            Schema::create('product_chat_placeholders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('tenant_id')->nullable()->index();

                $table->json('conversation'); // [{role: user, content: ...}, {role: assistant, content: ...}]
                $table->string('language', 5)->default('tr');
                $table->boolean('is_active')->default(true);

                $table->timestamp('generated_at')->nullable();
                $table->timestamps();

                // Unique constraint - one placeholder per product per language
                $table->unique(['product_id', 'language'], 'product_chat_placeholder_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('product_chat_placeholders');
    }
};
