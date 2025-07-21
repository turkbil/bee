<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Credit cost alanını nullable yap
     */
    public function up(): void
    {
        Schema::table('ai_token_usage', function (Blueprint $table) {
            $table->decimal('credit_cost', 8, 4)->nullable()->default(0.0000)->change()->comment('İşlem kredi maliyeti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_token_usage', function (Blueprint $table) {
            $table->decimal('credit_cost', 8, 4)->nullable(false)->change();
        });
    }
};