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
        Schema::create('ai_token_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('conversation_id')->nullable(); // AI konuşma referansı
            $table->unsignedBigInteger('message_id')->nullable(); // Spesifik mesaj referansı
            $table->integer('tokens_used'); // Kullanılan token miktarı
            $table->integer('prompt_tokens')->default(0); // Gelen token sayısı
            $table->integer('completion_tokens')->default(0); // Dönen token sayısı
            $table->string('usage_type')->default('chat'); // "chat", "image", "text" vs.
            $table->string('model')->nullable(); // Kullanılan AI modeli
            $table->string('purpose')->nullable(); // Amaç (chat, test, admin vs)
            $table->text('description')->nullable(); // Kullanım açıklaması
            $table->string('reference_id')->nullable(); // İlgili conversation/message ID
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->timestamp('used_at');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('set null');
            $table->foreign('message_id')->references('id')->on('ai_messages')->onDelete('set null');
            
            $table->index(['tenant_id', 'used_at']);
            $table->index(['user_id', 'used_at']);
            $table->index(['conversation_id']);
            $table->index(['usage_type']);
            $table->index(['used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_token_usage');
    }
};