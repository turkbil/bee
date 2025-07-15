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
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_completed')->default(false); // Profil tamamlandı mı?
            
            $table->timestamps();
            
            $table->index('tenant_id');
            $table->index('is_active');
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