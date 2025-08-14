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
        Schema::table('ai_features', function (Blueprint $table) {
            $table->string('module_type', 50)->nullable()->comment('blog, page, email, seo, translation')->after('icon');
            $table->string('category', 100)->nullable()->comment('content_generation, optimization, translation')->after('module_type');
            $table->json('supported_modules')->nullable()->comment('["page", "blog", "portfolio"]')->after('category');
            $table->json('context_rules')->nullable()->comment('Module ve context bazlı kurallar')->after('supported_modules');
            $table->boolean('template_support')->default(false)->after('context_rules');
            $table->boolean('bulk_support')->default(false)->after('template_support');
            $table->boolean('streaming_support')->default(false)->after('bulk_support');
            
            // Index'ler
            $table->index(['module_type', 'category'], 'idx_module_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_features', function (Blueprint $table) {
            $table->dropIndex('idx_module_category');
            $table->dropColumn([
                'module_type',
                'category', 
                'supported_modules',
                'context_rules',
                'template_support',
                'bulk_support',
                'streaming_support'
            ]);
        });
    }
};