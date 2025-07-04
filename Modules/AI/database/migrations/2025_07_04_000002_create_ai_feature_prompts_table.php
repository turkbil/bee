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
        Schema::create('ai_feature_prompts', function (Blueprint $table) {
            $table->id();
            
            // İlişkiler
            $table->foreignId('ai_feature_id')->constrained('ai_features')->onDelete('cascade');
            $table->foreignId('ai_prompt_id')->constrained('ai_prompts')->onDelete('cascade');
            
            // Prompt Türü
            $table->enum('prompt_role', [
                'primary',      // Ana prompt (zorunlu)
                'secondary',    // İkincil prompt (destek)
                'support',      // Destek prompt (yardımcı)
                'hidden',       // Gizli sistem promptu
                'conditional',  // Şartlı prompt
                'formatting',   // Format düzenleme
                'validation'    // Doğrulama/kontrol
            ])->default('primary');
            
            // Öncelik ve Sıralama
            $table->integer('priority')->default(0); // Hangi sırada çalışacak
            $table->boolean('is_required')->default(false); // Zorunlu mu
            $table->boolean('is_active')->default(true); // Aktif mi
            
            // Şartlı Çalışma Kuralları
            $table->json('conditions')->nullable(); // Hangi durumlarda çalışacak
            $table->json('parameters')->nullable(); // Prompt parametreleri
            $table->text('notes')->nullable(); // Admin notları
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint - Aynı feature'da aynı role'den sadece bir tane
            $table->unique(['ai_feature_id', 'ai_prompt_id', 'prompt_role'], 'unique_feature_prompt_role');
            
            // İndexler
            $table->index(['ai_feature_id', 'prompt_role', 'priority']);
            $table->index(['ai_prompt_id', 'is_active']);
            $table->index('priority');
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