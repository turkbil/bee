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
        if (Schema::hasTable('ai_context_rules')) {
            return;
        }

        Schema::create('ai_context_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_key', 100)->unique();
            $table->string('rule_name', 255);
            $table->enum('rule_type', ['module', 'user', 'time', 'content', 'language', 'system'])->default('module');
            $table->json('conditions')->comment('Koşullar: {"module": "blog", "user_role": "author"}');
            $table->json('actions')->comment('Uygulanacak değişiklikler');
            $table->json('prompt_modifiers')->nullable()->comment('Prompt değişiklikleri');
            $table->integer('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->json('applies_to')->nullable()->comment('Hangi feature_ids için geçerli');
            $table->timestamps();
            
            // Index'ler
            $table->index(['rule_type', 'priority'], 'idx_rule_type_priority');
            $table->index(['is_active'], 'idx_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_context_rules');
    }
};