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
        Schema::create('ai_features', function (Blueprint $table) {
            $table->id();
            
            // Temel Bilgiler
            $table->string('name'); // "İçerik Üretimi", "Code Generation"
            $table->string('slug')->unique(); // "content-generation", "code-generation"
            $table->text('description')->nullable(); // Özellik açıklaması
            $table->string('emoji', 10)->nullable(); // 📝, 💻, ✍️
            $table->string('icon', 50)->nullable(); // FontAwesome class: "fas fa-edit"
            
            // Kategoriler ve Sınıflandırma
            $table->enum('category', [
                'content',      // İçerik üretimi
                'creative',     // Yaratıcı yazım
                'business',     // İş dünyası
                'technical',    // Teknik/kod
                'academic',     // Akademik
                'legal',        // Hukuki
                'marketing',    // Pazarlama
                'analysis',     // Analiz
                'communication',// İletişim
                'other'         // Diğer
            ])->default('other');
            
            // Yanıt Özellikleri
            $table->enum('response_length', ['short', 'medium', 'long', 'variable'])->default('medium');
            $table->enum('response_format', ['text', 'markdown', 'structured', 'code', 'list'])->default('markdown');
            $table->enum('complexity_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate');
            
            // Durum ve Görünürlük
            $table->enum('status', ['active', 'inactive', 'planned', 'beta'])->default('active');
            $table->boolean('is_system')->default(false); // Sistem özelliği (silinemez)
            $table->boolean('is_featured')->default(false); // Öne çıkan özellik
            $table->boolean('show_in_examples')->default(true); // Examples sayfasında göster
            $table->boolean('requires_pro')->default(false); // Pro üyelik gerekir mi
            
            // UI Özellikleri
            $table->integer('sort_order')->default(0); // Sıralama
            $table->string('badge_color', 20)->default('success'); // Bootstrap color
            $table->boolean('requires_input')->default(true); // Input alanı gerekir mi
            $table->string('input_placeholder', 500)->nullable(); // Input placeholder
            $table->string('button_text', 50)->default('Canlı Test Et'); // Test butonu metni
            
            // Hızlı Örnekler ve Ayarlar
            $table->json('example_inputs')->nullable(); // [{"text": "örnek", "label": "Hızlı Test"}]
            $table->json('ui_settings')->nullable(); // Grid boyutu, renk, vb.
            $table->json('api_settings')->nullable(); // Model özellikleri, token limiti
            $table->json('validation_rules')->nullable(); // Input validation kuralları
            
            // SEO ve Meta
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable(); // Arama için etiketler
            
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