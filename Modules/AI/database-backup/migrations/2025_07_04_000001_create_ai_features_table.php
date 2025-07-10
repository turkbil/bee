<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI Features Sistemi - İki Katmanlı Prompt Yapısı
     * 
     * Bu migration AI Feature sisteminin temelini oluşturur:
     * 
     * 1. QUICK PROMPT (quick_prompt): 
     *    - Feature'ın NE yapacağını kısa ve net söyler
     *    - Örnek: "Sen bir çeviri uzmanısın. Verilen metni hedef dile çevir."
     * 
     * 2. EXPERT PROMPT (expert_prompt_id referans):
     *    - Feature'ın NASIL yapacağının detaylı teknik bilgileri
     *    - ai_prompts tablosundaki prompt_type='feature' kayıtlarına referans
     *    - Örnek: "İçerik Üretim Uzmanı" (SEO, E-E-A-T, teknik detaylar)
     * 
     * 3. RESPONSE TEMPLATE (response_template JSON):
     *    - Her feature'ın sabit yanıt formatı/şablonu
     *    - Kullanıcı her seferinde aynı düzende sonuç alır
     *    - Örnek: {"sections": ["Analiz", "Puan", "Öneriler"], "format": "structured"}
     */
    public function up(): void
    {
        Schema::create('ai_features', function (Blueprint $table) {
            $table->id();
            
            // Temel Bilgiler
            $table->string('name'); // "İçerik Üretimi", "Code Generation"
            $table->string('slug')->unique(); // "content-generation", "code-generation"
            $table->text('description')->nullable(); // Özellik açıklaması
            $table->string('emoji', 10)->nullable(); // 📝, 💻, ✍️
            $table->string('icon', 50)->nullable(); // FontAwesome class: "fas fa-edit"
            
            // Kategoriler ve Sınıflandırma - STRING OLARAK DEĞİŞTİRİLDİ (enum değil)
            $table->string('category', 50)->default('other');
            
            // Helper function name ve detayları
            $table->string('helper_function')->nullable();
            $table->json('helper_examples')->nullable(); // Helper kullanım örnekleri
            $table->json('helper_parameters')->nullable(); // Helper parametreleri
            $table->text('helper_description')->nullable(); // Helper açıklaması
            $table->json('helper_returns')->nullable(); // Helper return formatı
            $table->string('hybrid_system_type', 50)->default('basic'); // basic, advanced, expert
            $table->boolean('has_custom_prompt')->default(false); // Custom prompt var mı?
            $table->boolean('has_related_prompts')->default(false); // İlişkili prompts var mı?
            
            // YENİ PROMPT SİSTEMİ - İki Katmanlı Prompt Yapısı
            $table->text('quick_prompt')->nullable(); // Feature'ın kısa, hızlı prompt'u (NE yapacağını söyler)
            $table->json('response_template')->nullable(); // Sabit yanıt formatı/şablonu
            // NOT: Expert prompt ilişkisi ai_feature_prompts pivot table'da yönetiliyor
            
            // Legacy - Geriye uyumluluk için korunuyor
            $table->text('custom_prompt')->nullable(); // Eski sistem uyumluluğu
            $table->json('additional_config')->nullable();
            
            // Usage examples and validation 
            $table->json('usage_examples')->nullable();
            $table->json('input_validation')->nullable();
            $table->json('settings')->nullable();
            $table->json('error_messages')->nullable();
            $table->json('success_messages')->nullable();
            $table->json('token_cost')->nullable();
            
            // Yanıt Özellikleri
            $table->enum('response_length', ['short', 'medium', 'long', 'variable'])->default('medium');
            $table->enum('response_format', ['text', 'markdown', 'structured', 'code', 'list', 'json'])->default('markdown');
            $table->enum('complexity_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            
            // Durum ve Görünürlük
            $table->enum('status', ['active', 'inactive', 'planned', 'beta'])->default('active');
            $table->boolean('is_system')->default(false); // Sistem özelliği (silinemez)
            $table->boolean('is_featured')->default(false); // Öne çıkan özellik
            $table->boolean('show_in_examples')->default(true); // Examples sayfasında göster
            
            // UI Özellikleri
            $table->integer('sort_order')->default(0); // Sıralama
            $table->integer('order')->default(0); // EKLENEN YENİ ALAN - sıralama için
            $table->string('badge_color', 20)->default('success'); // Bootstrap color
            $table->boolean('requires_input')->default(true); // Input alanı gerekir mi
            $table->string('input_placeholder', 500)->nullable(); // Input placeholder
            $table->string('button_text', 50)->default('Canlı Test Et'); // Test butonu metni
            
            // Hızlı Örnekler
            $table->json('example_inputs')->nullable(); // [{"text": "örnek", "label": "Hızlı Test"}]
            // example_prompts KALDIRILDI - hiçbir yerde kullanılmıyor
            
            // İstatistikler
            $table->unsignedBigInteger('usage_count')->default(0); // Kullanım sayısı
            $table->timestamp('last_used_at')->nullable(); // Son kullanım
            $table->decimal('avg_rating', 3, 2)->default(0); // Ortalama puan
            $table->unsignedInteger('rating_count')->default(0); // Oy sayısı
            
            // Timestamps
            $table->timestamps();
            
            // İndexler - Performans için
            $table->index(['status', 'show_in_examples', 'sort_order']);
            $table->index(['category', 'status']);
            $table->index(['is_featured', 'status']);
            $table->index('slug');
            $table->index('usage_count');
            // NOT: Expert prompt foreign key'i ai_feature_prompts pivot table'da
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_features');
    }
};