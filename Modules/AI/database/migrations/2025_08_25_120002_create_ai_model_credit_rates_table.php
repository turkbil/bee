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
        Schema::connection('central')->create('ai_model_credit_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ai_providers')->onDelete('cascade');
            $table->string('model_name');
            $table->decimal('credit_per_1k_input_tokens', 10, 4)->default(0);
            $table->decimal('credit_per_1k_output_tokens', 10, 4)->default(0);
            $table->decimal('base_cost_usd', 10, 6)->nullable();
            $table->decimal('markup_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['provider_id', 'model_name']);
            $table->index(['provider_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_model_credit_rates');
    }
};