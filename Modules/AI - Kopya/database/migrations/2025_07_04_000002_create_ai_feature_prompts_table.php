<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI Feature Prompts Pivot Table - Çoktan Çoğa İlişki
     * 
     * Bu migration Feature → Expert Prompt çoktan çoğa ilişkisini sağlar:
     * 
     * KULLANIM AMACI:
     * - Bir feature birden fazla expert prompt'a bağlanabilir
     * - Priority sırası ile hangi prompt'ın önce kullanılacağı belirlenir
     * - Role ile prompt'ın rolü tanımlanır (primary, secondary, supportive)
     * 
     * ÖRNEK KULLANIM:
     * Çeviri Feature:
     * 1. İçerik Üretim Uzmanı (primary, priority: 1)
     * 2. Çeviri Uzmanı (primary, priority: 2) 
     * 3. Yerel SEO Uzmanı (supportive, priority: 3)
     */
    public function up(): void
    {
        Schema::create('ai_feature_prompts', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->unsignedBigInteger('feature_id');
            $table->unsignedBigInteger('prompt_id');
            
            // İlişki Detayları
            $table->integer('priority')->default(1); // Kullanım sırası (1 = en öncelikli)
            $table->enum('role', [
                'primary',      // Ana prompt (temel işlevi yerine getirir)
                'secondary',    // İkincil prompt (ek bilgi sağlar)
                'supportive'    // Destekleyici prompt (kalite artırır)
            ])->default('primary');
            
            // Durum ve Ayarlar
            $table->boolean('is_active')->default(true); // Bu ilişki aktif mi?
            $table->json('conditions')->nullable(); // Hangi koşullarda kullanılsın
            $table->text('notes')->nullable(); // İlişki hakkında notlar
            
            $table->timestamps();
            
            // İndexler
            $table->index(['feature_id', 'priority']); // Feature'ın prompt'larını priority ile getir
            $table->index(['prompt_id', 'role']); // Prompt'ın kullanıldığı roller
            $table->index(['is_active', 'priority']); // Aktif prompt'ları sırası ile
            
            // Unique Constraint - Aynı feature'da aynı prompt aynı priority'de olamaz
            $table->unique(['feature_id', 'prompt_id', 'priority'], 'unique_feature_prompt_priority');
            
            // Foreign Key Constraints
            $table->foreign('feature_id')
                  ->references('id')
                  ->on('ai_features')
                  ->onDelete('cascade');
                  
            $table->foreign('prompt_id')
                  ->references('id')
                  ->on('ai_prompts')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feature_prompts');
    }
};