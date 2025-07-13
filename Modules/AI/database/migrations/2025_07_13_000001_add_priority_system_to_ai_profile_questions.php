<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ADD PRIORITY SYSTEM TO EXISTING AI PROFILE QUESTIONS
 * 
 * Mevcut ai_profile_questions tablosuna priority sistemi ekliyoruz:
 * - priority: 1=critical, 5=rarely used (AIPriorityEngine uyumlu)
 * - ai_weight: AI context building için ağırlık (1-100)
 * - category: company/sector/ai/founder grouping
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->table('ai_profile_questions', function (Blueprint $table) {
            // Priority System
            $table->tinyInteger('priority')->unsigned()->default(3)->after('sort_order')
                  ->comment('Priority level: 1=critical, 5=rarely used');
            
            // AI Context Weight
            $table->tinyInteger('ai_weight')->unsigned()->default(50)->after('priority')
                  ->comment('AI context building weight (1-100)');
            
            // Category for grouping
            $table->enum('category', ['company', 'sector', 'ai', 'founder'])->default('company')->after('ai_weight')
                  ->comment('Field category grouping');
            
            // Performance indexes
            $table->index(['priority', 'ai_weight'], 'idx_priority_weight');
            $table->index(['category', 'step'], 'idx_category_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('ai_profile_questions', function (Blueprint $table) {
            $table->dropIndex('idx_priority_weight');
            $table->dropIndex('idx_category_step');
            $table->dropColumn(['priority', 'ai_weight', 'category']);
        });
    }
};