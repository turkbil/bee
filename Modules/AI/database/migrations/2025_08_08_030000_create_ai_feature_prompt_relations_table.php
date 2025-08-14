<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * AI Feature Prompt Relations Table - Feature ↔ Feature Prompt İlişkileri
     * 
     * Bu migration Feature'ları Feature Prompt'larla eşleştiren relation tablosudur:
     * 
     * KULLANIM AMACI:
     * - Bir feature birden fazla feature prompt'a bağlanabilir
     * - Priority sırası ile hangi prompt'ın önce kullanılacağı belirlenir
     * - Role ile prompt'ın rolü tanımlanır (primary, secondary, supportive)
     * 
     * ÖRNEK KULLANIM:
     * Blog Yazısı Feature:
     * 1. İçerik Üretim Uzmanı (primary, priority: 1)
     * 2. SEO İçerik Uzmanı (supportive, priority: 2) 
     * 3. Yaratıcı İçerik Uzmanı (secondary, priority: 3)
     */
    public function up(): void
    {
        // Central veritabanında tablo oluştur
        TenantHelpers::central(function() {
            Schema::create('ai_feature_prompt_relations', function (Blueprint $table) {
                $table->id();
                
                // Foreign Keys
                $table->unsignedBigInteger('feature_id');
                $table->unsignedBigInteger('prompt_id')->nullable(); // ai_prompts.prompt_id için (sistem promptları)  
                $table->unsignedBigInteger('feature_prompt_id')->nullable(); // ai_feature_prompts.id için (expert promptlar)
                
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
                
                // Category Context & Filtering (Static Category ID ile uyumlu)
                $table->json('category_context')->nullable(); // Hangi kategorilerde bu ilişki aktif (1,2,3... static ID'ler)
                $table->enum('feature_type_filter', ['all', 'specific', 'category_based'])->default('all'); // Hangi feature'lara uygulanır
                $table->json('business_rules')->nullable(); // İş kuralları JSON formatında
                
                $table->timestamps();
                
                // İndexler  
                $table->index(['feature_id', 'priority']); // Feature'ın prompt'larını priority ile getir
                $table->index(['prompt_id', 'role']); // Prompt'ın kullanıldığı roller
                $table->index(['is_active', 'priority']); // Aktif prompt'ları sırası ile
                $table->index(['feature_type_filter', 'is_active']); // Feature type filtreleme için
                $table->index(['role', 'priority', 'is_active']); // Composite index performance için
                
                // İndeks'ler için hem prompt_id hem feature_prompt_id
                $table->index(['feature_prompt_id', 'role']); // Expert prompt'ların kullanıldığı roller
                
                // Foreign Key Constraints
                $table->foreign('feature_id')
                      ->references('id')
                      ->on('ai_features')
                      ->onDelete('cascade');
                      
                // AI_PROMPTS tablosuna foreign key (sistem promptları için)
                $table->foreign('prompt_id')
                      ->references('prompt_id')
                      ->on('ai_prompts')
                      ->onDelete('cascade');
                      
                // AI_FEATURE_PROMPTS tablosuna foreign key (expert promptlar için)  
                $table->foreign('feature_prompt_id')
                      ->references('id')
                      ->on('ai_feature_prompts')
                      ->onDelete('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantHelpers::central(function() {
            Schema::dropIfExists('ai_feature_prompt_relations');
        });
    }
};