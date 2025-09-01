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
            $table->unsignedBigInteger('prompt_id')->unique()->nullable()->comment('Static ID for seeder management (10001-10900 range)');
            $table->string('name');
            $table->text('content');
            $table->enum('prompt_type', [
                'standard',        // Normal prompt
                'common',          // Ortak özellikler (eski is_common)
                'hidden_system',   // Gizli sistem promptu
                'secret_knowledge', // Gizli bilgi tabanı
                'conditional',     // Şartlı yanıtlar
                'writing_tone',    // Yazım tonu (tüm feature'larda kullanılabilir)
                'content_length',  // İçerik uzunluğu (tüm feature'larda kullanılabilir)
                'feature',         // Feature-specific prompt
                'chat'             // Sohbet odaklı prompt'lar
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
              
            // ADD columns from 2025_08_10_200001_add_v3_columns_to_ai_prompts_table.php
            $table->enum('prompt_type_v3', ['system', 'tone', 'length', 'style', 'context', 'template', 'writing_tone', 'content_length', 'target_audience'])
                  ->default('system')
                  ->comment('Universal Input System V3 - Prompt kategorisi');
            $table->string('module_specific', 50)->nullable()->comment('Hangi modül için özel');
            $table->json('context_conditions')->nullable()->comment('Bu prompt ne zaman kullanılır');
            $table->json('variables')->nullable()->comment('[\"company_name\", \"user_name\", \"module_type\"]');
            $table->boolean('is_chainable')->default(true)->comment('Diğer promptlarla birleştirilebilir mi');
            
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
            // ADD index from 2025_08_10_200001_add_v3_columns_to_ai_prompts_table.php
            $table->index(['prompt_type_v3', 'module_specific'], 'idx_prompt_type_module');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_prompts');
    }
};