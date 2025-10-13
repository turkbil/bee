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
        Schema::create('ai_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();

            // Kategori (string, manuel veya dropdown'dan)
            $table->string('category', 100)->nullable()->index();

            // Soru & Yanıt (tenant'ın default_locale'inde)
            $table->text('question');
            $table->text('answer');

            // Metadata (JSON: tags, internal_note, priority, icon, etc.)
            $table->json('metadata')->nullable();

            // Durum & Sıralama
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Index'ler
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_base');
    }
};
