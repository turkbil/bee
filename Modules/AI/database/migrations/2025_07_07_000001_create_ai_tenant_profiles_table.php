<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * AI Tenant Profil Sistemi - SADECE CENTRAL VERITABANI
     * 
     * Bu tablo her tenant için AI davranışlarını yönlendirecek profil bilgilerini tutar.
     * Firma bilgileri, sektör detayları, AI davranış kuralları ve kurucu bilgileri JSON formatında saklanır.
     * Bu sayede AI, her tenant için kişiselleştirilmiş içerik üretebilir.
     */
    public function up(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::create('ai_tenant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained('tenants')->onDelete('cascade');
            
            // Firma temel bilgileri (isim, kuruluş yılı, çalışan sayısı, lokasyon vb.)
            $table->json('company_info')->nullable();
            
            // Sektöre özel detaylar (e-ticaret için ürün kategorileri, sağlık için branşlar vb.)
            $table->json('sector_details')->nullable();
            
            // Başarı hikayeleri ve rekabet avantajları
            $table->json('success_stories')->nullable();
            
            // AI davranış kuralları (ton, vurgular, kaçınılacak konular vb.)
            $table->json('ai_behavior_rules')->nullable();
            
            // Kurucu bilgileri (opsiyonel - checkbox ile aktif edilir)
            $table->json('founder_info')->nullable();
            
            // Ek bilgiler (gelecekte eklenebilecek alanlar için)
            $table->json('additional_info')->nullable();
            
            // Marka hikayesi alanları
            $table->text('brand_story')->nullable();
            $table->timestamp('brand_story_created_at')->nullable();
            
            // AI Context Optimizasyon Alanları - EKLENEN
            $table->text('ai_context')->nullable()
                  ->comment('AI için optimize edilmiş context - öncelikli bilgiler');
            $table->json('context_priority')->nullable()
                  ->comment('Context bilgilerinin priority sıralaması');
            
            // SMART SCORING SYSTEM
            $table->json('smart_field_scores')->nullable();
            $table->json('field_calculation_metadata')->nullable();
            $table->decimal('profile_completeness_score', 5, 2)->default(0.0);
            $table->string('profile_quality_grade', 5)->default('F');
            
            // CONTEXT-AWARE SYSTEM
            $table->string('last_calculation_context', 50)->default('normal');
            $table->timestamp('scores_calculated_at')->nullable();
            $table->json('context_performance')->nullable(); // Different context scores
            
            // SMART RECOMMENDATIONS
            $table->json('ai_recommendations')->nullable();
            $table->integer('missing_critical_fields')->default(0);
            $table->json('field_quality_analysis')->nullable();
            
            // ADVANCED ANALYTICS
            $table->json('usage_analytics')->nullable();
            $table->integer('ai_interactions_count')->default(0);
            $table->timestamp('last_ai_interaction_at')->nullable();
            $table->decimal('avg_ai_response_quality', 3, 2)->default(0.0);
            
            // SMART VERSIONING
            $table->integer('profile_version')->default(1);
            $table->json('version_history')->nullable();
            $table->boolean('auto_optimization_enabled')->default(true);
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_completed')->default(false); // Profil tamamlandı mı?
            $table->json('data')->nullable()->comment('Context data for AI'); // Eklenen
            
            $table->timestamps();
            
            $table->index('tenant_id');
            $table->index('is_active');
            
            // PERFORMANCE INDEXES
            $table->index(['tenant_id', 'profile_completeness_score'], 'idx_tenant_completeness');
            $table->index(['profile_quality_grade', 'is_completed'], 'idx_quality_completed');
            $table->index(['last_calculation_context', 'scores_calculated_at'], 'idx_context_timing');
            $table->index(['missing_critical_fields', 'is_active'], 'idx_critical_fields');
        });
    }

    public function down(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::dropIfExists('ai_tenant_profiles');
    }
};