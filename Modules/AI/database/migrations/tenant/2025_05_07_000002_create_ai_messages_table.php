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
            $table->timestamps();
            
            $table->index('conversation_id');
            $table->index('role');
            $table->index('created_at');
            $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('cascade');
            
            // Composite index'ler - Performans optimizasyonu
            $table->index(['conversation_id', 'created_at'], 'ai_messages_conversation_created_idx');
            $table->index(['conversation_id', 'role'], 'ai_messages_conversation_role_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
    }
};