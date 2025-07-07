<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * Dinamik soru sistemi tablosu - SADECE CENTRAL VERITABANI
     * 
     * Her sektör için farklı sorular ve tüm sektörler için ortak sorular bu tabloda tutulur.
     * Checkbox ile aktif edilen opsiyonel bölümler (kurucu bilgisi gibi) de buradan yönetilir.
     */
    public function up(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::create('ai_profile_questions', function (Blueprint $table) {
            $table->id();
            $table->string('sector_code', 50)->nullable(); // null ise tüm sektörler için geçerli
            $table->integer('step')->default(1); // Form wizard adımı
            $table->string('section')->nullable(); // founder_info, additional_info vb.
            $table->string('question_key')->unique(); // company_name, founding_year vb.
            $table->string('question_text'); // Görünen soru metni
            $table->text('help_text')->nullable(); // Yardım metni
            $table->string('input_type'); // text, select, checkbox, textarea, number, date vb.
            $table->json('options')->nullable(); // Select/checkbox için seçenekler
            $table->json('validation_rules')->nullable(); // required, min:3, max:100 vb.
            $table->string('depends_on')->nullable(); // Başka bir soruya bağlı mı? (checkbox mantığı)
            $table->json('show_if')->nullable(); // Gösterilme koşulları
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('sector_code');
            $table->index('step');
            $table->index('section');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::dropIfExists('ai_profile_questions');
    }
};