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
        Schema::create('ai_model_credit_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ai_providers')->onDelete('cascade');
            $table->string('model_name', 255);
            $table->decimal('credit_per_1k_input_tokens', 8, 4)->default(1.0000)->comment('1K input token için kredi');
            $table->decimal('credit_per_1k_output_tokens', 8, 4)->default(1.0000)->comment('1K output token için kredi');
            $table->decimal('base_cost_usd', 10, 6)->nullable()->comment('API gerçek maliyeti USD');
            $table->decimal('markup_percentage', 5, 2)->default(20.00)->comment('Kâr marjı yüzdesi');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->unique(['provider_id', 'model_name'], 'unique_provider_model');
            $table->index(['provider_id', 'model_name'], 'idx_provider_model');
            $table->index('is_active', 'idx_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_model_credit_rates');
    }
};