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
        Schema::connection('central')->create('ai_prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_key', 100)->unique();
            $table->string('template_name', 255);
            $table->enum('template_type', ['feature', 'module', 'page', 'component'])->default('feature');
            $table->string('module_type', 50)->nullable();
            $table->string('category', 100)->nullable();
            $table->json('template_structure')->comment('Template alan yapısı');
            $table->json('field_mappings')->comment('Hangi alan nereye map edilecek');
            $table->json('prompt_chain')->nullable()->comment('Kullanılacak prompt ID listesi');
            $table->string('preview_image', 500)->nullable();
            $table->text('example_output')->nullable();
            $table->integer('min_fields')->default(1);
            $table->integer('max_fields')->default(20);
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            
            // Index'ler
            $table->index(['template_type', 'module_type'], 'idx_template_type_module');
            $table->index(['category', 'is_active'], 'idx_category_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_prompt_templates');
    }
};