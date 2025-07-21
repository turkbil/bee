<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI Settings tablosunu kaldır - tek tablo yaklaşımına geçiş tamamlandı
     */
    public function up(): void
    {
        // ai_settings tablosunu kaldır (artık config-based sistem kullanıyoruz)
        Schema::dropIfExists('ai_settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma - ai_settings tablosunu yeniden oluştur
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->text('api_key')->nullable();
            $table->string('model')->default('deepseek-chat');
            $table->integer('max_tokens')->default(4096);
            $table->decimal('temperature', 3, 2)->default(0.7);
            $table->boolean('enabled')->default(true);
            $table->integer('max_question_length')->default(2000);
            $table->integer('max_daily_questions')->default(50);
            $table->integer('max_monthly_questions')->default(1000);
            $table->integer('question_token_limit')->default(500);
            $table->integer('free_question_tokens_daily')->default(1000);
            $table->boolean('charge_question_tokens')->default(false);
            $table->string('default_language', 5)->default('tr');
            $table->string('response_format')->default('markdown');
            $table->integer('cache_duration')->default(60);
            $table->integer('concurrent_requests')->default(5);
            $table->boolean('content_filtering')->default(true);
            $table->boolean('rate_limiting')->default(true);
            $table->boolean('detailed_logging')->default(false);
            $table->boolean('performance_monitoring')->default(true);
            $table->json('providers')->nullable();
            $table->string('active_provider')->default('deepseek');
            $table->timestamps();
        });
    }
};