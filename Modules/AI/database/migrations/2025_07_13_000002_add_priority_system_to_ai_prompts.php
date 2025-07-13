<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADD PRIORITY SYSTEM TO AI PROMPTS
 * 
 * Tüm AI prompt türlerine priority sistemi ekliyoruz:
 * - priority: 1=critical, 5=rarely used (AIPriorityEngine uyumlu)
 * - ai_weight: AI context building için ağırlık (1-100)
 * - prompt_category: system/feature/response/hidden grouping
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->table('ai_prompts', function (Blueprint $table) {
            // Priority System
            $table->tinyInteger('priority')->unsigned()->default(3)->after('prompt_type')
                  ->comment('Priority level: 1=critical, 5=rarely used');
            
            // AI Context Weight
            $table->tinyInteger('ai_weight')->unsigned()->default(50)->after('priority')
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
            ])->default('expert_knowledge')->after('ai_weight')
              ->comment('AIPriorityEngine category mapping');
            
            // Performance indexes
            $table->index(['priority', 'ai_weight'], 'idx_prompt_priority_weight');
            $table->index(['prompt_category', 'priority'], 'idx_prompt_category_priority');
            $table->index(['is_active', 'prompt_type'], 'idx_prompt_active_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('ai_prompts', function (Blueprint $table) {
            $table->dropIndex('idx_prompt_priority_weight');
            $table->dropIndex('idx_prompt_category_priority');
            $table->dropIndex('idx_prompt_active_type');
            $table->dropColumn(['priority', 'ai_weight', 'prompt_category']);
        });
    }
};