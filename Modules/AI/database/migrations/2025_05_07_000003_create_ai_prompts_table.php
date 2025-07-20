<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->enum('prompt_type', [
                'standard',        // Normal prompt
                'common',          // Ortak özellikler (eski is_common)
                'hidden_system',   // Gizli sistem promptu
                'secret_knowledge', // Gizli bilgi tabanı
                'conditional',     // Şartlı yanıtlar
                'feature'          // Feature-specific prompt
            ])->default('standard');
            
            // Priority System
            $table->tinyInteger('priority')->unsigned()->default(3)
                  ->comment('Priority level: 1=critical, 5=rarely used');
            
            // AI Context Weight
            $table->tinyInteger('ai_weight')->unsigned()->default(50)
                  ->comment('AI context building weight (1-100)');
            
            // Prompt Category for AIPriorityEngine mapping
            $table->enum('prompt_category', [
                'system_common',      // Ortak özellikler (en yüksek)
                'system_hidden',      // Gizli sistem kuralları
                'feature_definition', // Quick prompts (feature tanımı)
                'expert_knowledge',   // Expert prompts (nasıl yapacak)
                'tenant_identity',    // Tenant profil context
                'secret_knowledge',   // Gizli bilgi tabanı
                'brand_context',      // Marka detayları
                'response_format',    // Response template'lar
                'conditional_info'    // Şartlı yanıtlar (en düşük)
            ])->default('expert_knowledge')
              ->comment('AIPriorityEngine category mapping');
            
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(false); // Sistem promptları değiştirilemez
            $table->boolean('is_common')->default(false); // Ortak özellikler promptu
            $table->boolean('is_active')->default(true);  // Aktiflik durumu eklendi
            $table->timestamps();
            
            $table->index('name');
            $table->index('prompt_type');
            $table->index('is_default');
            $table->index('is_system');
            $table->index('is_common');
            $table->index('is_active');  // is_active için index eklendi
            $table->index('created_at');
            $table->index('updated_at');
            
            // Priority system performance indexes
            $table->index(['priority', 'ai_weight'], 'idx_prompt_priority_weight');
            $table->index(['prompt_category', 'priority'], 'idx_prompt_category_priority');
            $table->index(['is_active', 'prompt_type'], 'idx_prompt_active_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_prompts');
    }
};