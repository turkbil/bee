<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('central')->create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('chat'); // chat, feature_test, admin_chat
            $table->string('feature_name')->nullable(); // test edilen özellik adı
            $table->boolean('is_demo')->default(false); // demo test mi gerçek mi
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tenant_id')->nullable(); // hangi tenant
            $table->unsignedBigInteger('prompt_id')->nullable();
            $table->string('session_id')->nullable(); // Frontend session ID (hash)
            $table->integer('total_tokens_used')->default(0); // toplam token kullanımı
            $table->json('metadata')->nullable(); // ek bilgiler
            $table->string('status')->default('active'); // active, archived, deleted
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('prompt_id');
            $table->index('type');
            $table->index('feature_name');
            $table->index('tenant_id');
            $table->index('session_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['user_id', 'created_at'], 'ai_conversations_user_created_idx');
            $table->index(['prompt_id', 'created_at'], 'ai_conversations_prompt_created_idx');
            $table->index(['type', 'created_at'], 'ai_conversations_type_created_idx');
            $table->index(['tenant_id', 'created_at'], 'ai_conversations_tenant_created_idx');
        });
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_conversations');
    }
};