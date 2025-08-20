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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // openai, claude, deepseek
            $table->string('display_name'); // OpenAI, Claude, DeepSeek
            $table->string('service_class'); // OpenAIService, ClaudeService, DeepSeekService
            $table->string('default_model')->nullable(); // gpt-4o-mini, claude-3-haiku-20240307, deepseek-chat
            $table->json('available_models')->nullable(); // Liste of available models
            $table->json('default_settings')->nullable(); // Default temperature, max_tokens, etc.
            $table->text('api_key')->nullable(); // API anahtarı (encrypted olarak saklanır)
            $table->string('base_url')->nullable(); // API base URL
            $table->boolean('is_active')->default(true); // Aktif mi?
            $table->boolean('is_default')->default(false); // Varsayılan provider mi?
            $table->integer('priority')->default(0); // Öncelik sırası
            $table->decimal('average_response_time', 8, 2)->nullable(); // Ortalama yanıt süresi (ms)
            $table->text('description')->nullable(); // Açıklama
            $table->decimal('token_cost_multiplier', 8, 4)->default(1.0000); // Token maliyet çarpanı
            $table->integer('tokens_per_request_estimate')->default(1000); // İstek başına tahmini token
            $table->json('cost_structure')->nullable(); // Maliyet yapısı detayları
            $table->boolean('tracks_usage')->default(true); // Kullanım takibi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};