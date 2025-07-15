<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->enum('role', ['user', 'assistant']);
            $table->text('content');
            $table->integer('tokens')->default(0);
            $table->integer('prompt_tokens')->default(0); // gelen token sayısı
            $table->integer('completion_tokens')->default(0); // dönen token sayısı
            $table->string('model_used')->nullable(); // kullanılan AI modeli
            $table->integer('processing_time_ms')->default(0); // işlem süresi
            $table->json('metadata')->nullable(); // ek bilgiler (istek/yanıt detayları)
            $table->string('message_type')->default('normal'); // normal, test, system
            $table->timestamps();
            
            $table->index('conversation_id');
            $table->index('role');
            $table->index('model_used');
            $table->index('message_type');
            $table->index('created_at');
            $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('cascade');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['conversation_id', 'created_at'], 'ai_messages_conversation_created_idx');
            $table->index(['conversation_id', 'role'], 'ai_messages_conversation_role_idx');
            $table->index(['conversation_id', 'message_type'], 'ai_messages_conversation_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};