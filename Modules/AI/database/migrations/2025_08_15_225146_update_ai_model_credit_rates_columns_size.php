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
        Schema::table('ai_model_credit_rates', function (Blueprint $table) {
            // Credit columns'ları DECIMAL(15,4)'e büyütüyoruz (max 99,999,999.9999)
            $table->decimal('credit_per_1k_input_tokens', 15, 4)->change();
            $table->decimal('credit_per_1k_output_tokens', 15, 4)->change();
            
            // Base cost da büyütülebilir
            $table->decimal('base_cost_usd', 15, 6)->change(); // $99,999,999.999999'a kadar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_model_credit_rates', function (Blueprint $table) {
            // Eski boyutlara geri dön
            $table->decimal('credit_per_1k_input_tokens', 8, 4)->change();
            $table->decimal('credit_per_1k_output_tokens', 8, 4)->change();
            $table->decimal('base_cost_usd', 8, 6)->change();
        });
    }
};
