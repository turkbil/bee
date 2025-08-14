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
        Schema::table('ai_prompts', function (Blueprint $table) {
            // V3 Universal Input System - Prompt Type enum
            if (!Schema::hasColumn('ai_prompts', 'prompt_type')) {
                $table->enum('prompt_type', ['system', 'tone', 'length', 'style', 'context', 'template', 'writing_tone', 'content_length', 'target_audience'])
                      ->default('system')
                      ->comment('Universal Input System V3 - Prompt kategorisi')
                      ->after('prompt_category');
            }
            
            // Sadece yoksa ekle kontrolü ile
            if (!Schema::hasColumn('ai_prompts', 'module_specific')) {
                $table->string('module_specific', 50)->nullable()->comment('Hangi modül için özel')->after('prompt_type');
            }
            if (!Schema::hasColumn('ai_prompts', 'context_conditions')) {
                $table->json('context_conditions')->nullable()->comment('Bu prompt ne zaman kullanılır')->after('module_specific');
            }
            if (!Schema::hasColumn('ai_prompts', 'variables')) {
                $table->json('variables')->nullable()->comment('["company_name", "user_name", "module_type"]')->after('context_conditions');
            }
            if (!Schema::hasColumn('ai_prompts', 'is_chainable')) {
                $table->boolean('is_chainable')->default(true)->comment('Diğer promptlarla birleştirilebilir mi')->after('variables');
            }
        });
        
        // Index'i ayrıca ekle
        if (!$this->indexExists('ai_prompts', 'idx_prompt_type_module')) {
            Schema::table('ai_prompts', function (Blueprint $table) {
                $table->index(['prompt_type', 'module_specific'], 'idx_prompt_type_module');
            });
        }
    }
    
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes($table);
            return array_key_exists($indexName, $indexes);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            if ($this->indexExists('ai_prompts', 'idx_prompt_type_module')) {
                $table->dropIndex('idx_prompt_type_module');
            }
            
            $columns = ['prompt_type', 'module_specific', 'context_conditions', 'variables', 'is_chainable'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('ai_prompts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};