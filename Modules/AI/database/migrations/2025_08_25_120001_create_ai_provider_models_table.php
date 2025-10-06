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
        // ai_provider_models tablosunu oluştur (ai_model_credit_rates'in yerini alıyor)
        Schema::connection('central')->create('ai_provider_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->string('model_name', 100);
            $table->decimal('credit_per_1k_input_tokens', 8, 4)->default(0.0000);
            $table->decimal('credit_per_1k_output_tokens', 8, 4)->default(0.0000);
            $table->decimal('base_cost_usd', 10, 6)->default(0.000000);
            $table->decimal('markup_percentage', 5, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0); // Sıralama sütunu
            $table->timestamps();
            
            // İndeksler ve foreign key'ler
            $table->foreign('provider_id')->references('id')->on('ai_providers')->onDelete('cascade');
            $table->index(['provider_id', 'is_active']);
            $table->index(['is_default', 'sort_order']);
            $table->unique(['provider_id', 'model_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_provider_models');
    }
};