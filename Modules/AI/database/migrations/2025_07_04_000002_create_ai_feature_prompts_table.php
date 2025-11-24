<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * AI Feature Prompts Table - Feature'lara Özel Prompt'lar
     * 
     * Bu tablo AI feature'lar için özel prompt'ları saklar.
     * ai_prompts tablosundan ayrı tutulur (sistem prompt'larından farklı mantık)
     * 
     * KULLANIM AMACI:
     * - Her feature'ın kendine özel expert prompt'ları olur
     * - Quick prompt + Expert prompt + Response template sistemi
     * - Priority ve role based çalışma mantığı
     * - Static Category ID sistemi ile uyumlu (1-18 kategori)
     * 
     * ÖRNEK KULLANIM:
     * "SEO İçerik Uzmanı" prompt'u:
     * - name: "SEO İçerik Uzmanı"
     * - expert_prompt: "Sen bir SEO uzmanısın..."
     * - supported_categories: [1,4,6] (SEO, Pazarlama, Sosyal Medya)
     * - response_template: {"sections": ["title", "meta", "content"]}
     * 
     * STATIC CATEGORY SYSTEM (ID: 1-18):
     * 1=SEO, 2=İçerik, 3=Çeviri, 4=Pazarlama, 5=E-ticaret, 6=Sosyal Medya,
     * 7=Email, 8=Analiz, 9=Müşteri Hizmetleri, 10=İş Geliştirme, 11=Araştırma,
     * 12=Yaratıcı İçerik, 13=Teknik Dok., 14=Kod, 15=Tasarım, 16=Eğitim, 17=Finans, 18=Hukuki
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_feature_prompts')) {
            return;
        }

        Schema::create('ai_feature_prompts', function (Blueprint $table) {
                $table->id();
                
                // Prompt Identity
                $table->string('name')->index(); // "SEO İçerik Uzmanı", "Pazarlama Stratejisti"
                $table->string('slug')->unique(); // "seo-expert", "marketing-strategist"
                $table->text('description')->nullable(); // Prompt'ın ne yaptığının açıklaması
                
                // Prompt Content
                $table->longText('expert_prompt'); // Expert'ın ana prompt'u
                $table->json('response_template')->nullable(); // Yanıt formatı JSON şablonu
                $table->json('supported_categories')->nullable(); // Hangi kategorilerde kullanılabilir (1,2,3 static ID'ler)
                
                // Expert Persona
                $table->string('expert_persona')->default('general'); // "seo_expert", "content_creator", "marketing_specialist"
                $table->text('personality_traits')->nullable(); // Expert'ın kişilik özellikleri
                $table->json('expertise_areas')->nullable(); // Uzmanlık alanları JSON array
                
                // Configuration
                $table->integer('priority')->default(10); // Öncelik puanı (yüksek = öncelikli)
                $table->enum('prompt_type', ['expert', 'system', 'template', 'helper'])->default('expert');
                $table->enum('complexity_level', ['basic', 'intermediate', 'advanced', 'expert'])->default('intermediate');
                
                // AI Integration
                $table->integer('context_weight')->default(50); // Context'teki ağırlığı (1-100)
                $table->json('model_configs')->nullable(); // AI model özel ayarları
                $table->text('system_instructions')->nullable(); // Sistem talimatları
                
                // Status & Control
                $table->boolean('is_active')->default(true)->index();
                $table->boolean('is_system_prompt')->default(false); // Sistem prompt'u mu?
                $table->boolean('is_premium')->default(false); // Premium prompt mu?
                
                // Performance & Analytics
                $table->unsignedBigInteger('usage_count')->default(0);
                $table->timestamp('last_used_at')->nullable();
                $table->decimal('avg_quality_score', 4, 2)->default(0.00);
                
                // Versioning
                $table->string('version')->default('1.0.0');
                $table->text('version_notes')->nullable();
                
                // Timestamps
                $table->timestamps();
                $table->softDeletes();
                
                // Business Logic Constraints
                $table->json('validation_rules')->nullable(); // JSON validation kuralları
                $table->json('usage_limits')->nullable(); // Kullanım limitleri {"daily": 100, "monthly": 3000}
                $table->json('compatibility_matrix')->nullable(); // Hangi feature tiplerle uyumlu
                
                // Indexes
                $table->index(['is_active', 'priority']);
                $table->index(['prompt_type', 'complexity_level']);
                $table->index(['usage_count', 'avg_quality_score']);
                $table->index(['expert_persona', 'is_active']); // Expert persona filtreleme için
                // 🧪 TEST FIX: SQLite full text search desteği yok, sadece MySQL/PostgreSQL için
                if (config('database.default') !== 'sqlite') {
                    $table->fullText(['name', 'description', 'personality_traits']); // Full text search genişletildi
                }
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