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
        Schema::create('tenant_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable()->comment('Kategori (Manuel - Ör: Kargo, Ürün, Ödeme)');
            $table->text('question')->comment('Soru');
            $table->text('answer')->nullable()->comment('Cevap (Tenant tarafından girilir)');
            $table->boolean('is_active')->default(true)->comment('Aktif mi?');
            $table->integer('sort_order')->default(0)->comment('Sıralama');
            $table->timestamps();

            // Index for performance
            $table->index(['is_active', 'category', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_knowledge_base');
    }
};
