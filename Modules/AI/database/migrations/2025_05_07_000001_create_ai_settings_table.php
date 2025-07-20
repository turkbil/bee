<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->text('api_key')->nullable();
            $table->string('model')->default('deepseek-chat');
            $table->integer('max_tokens')->default(4096);
            $table->float('temperature')->default(0.7);
            $table->boolean('enabled')->default(true);
            $table->json('providers')->nullable(); // AI Provider'lar listesi
            $table->string('active_provider')->default('deepseek'); // Aktif provider
            
            // Limit Alanı
            $table->integer('max_question_length')->default(2000);
            $table->integer('max_daily_questions')->default(50);
            $table->integer('max_monthly_questions')->default(1000);
            $table->integer('question_token_limit')->default(500);
            $table->integer('free_question_tokens_daily')->default(1000);
            $table->boolean('charge_question_tokens')->default(false);
            
            // Sistem Davranışı
            $table->string('default_language', 5)->default('tr');
            $table->string('response_format', 20)->default('markdown');
            
            // Performans Ayarları
            $table->integer('cache_duration')->default(60); // dakika
            $table->integer('concurrent_requests')->default(5);
            
            // Güvenlik Ayarları
            $table->boolean('content_filtering')->default(true);
            $table->boolean('rate_limiting')->default(true);
            
            // Loglama & İzleme
            $table->boolean('detailed_logging')->default(false);
            $table->boolean('performance_monitoring')->default(true);
            
            // Token Kampanya Ayarları
            $table->decimal('token_campaign_multiplier', 5, 2)->default(1.00);
            $table->string('campaign_name')->nullable();
            $table->text('campaign_description')->nullable();
            $table->timestamp('campaign_start_date')->nullable();
            $table->timestamp('campaign_end_date')->nullable();
            $table->boolean('campaign_active')->default(false);
            
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('enabled');
            $table->index('campaign_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};