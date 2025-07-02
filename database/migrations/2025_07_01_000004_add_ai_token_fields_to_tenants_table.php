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
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedInteger('ai_tokens_balance')->default(0); // Mevcut token bakiyesi
            $table->unsignedInteger('ai_tokens_used_this_month')->default(0); // Bu ay kullanılan tokenlar
            $table->unsignedInteger('ai_monthly_token_limit')->default(0); // Aylık token limiti
            $table->boolean('ai_enabled')->default(false); // AI kullanımı aktif mi?
            $table->timestamp('ai_monthly_reset_at')->nullable(); // Son aylık sıfırlama tarihi
            $table->timestamp('ai_last_used_at')->nullable(); // Son AI kullanım tarihi
            
            $table->index(['ai_enabled']);
            $table->index(['ai_monthly_reset_at']);
            $table->index(['ai_last_used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'ai_tokens_balance',
                'ai_tokens_used_this_month', 
                'ai_monthly_token_limit',
                'ai_enabled',
                'ai_monthly_reset_at',
                'ai_last_used_at'
            ]);
        });
    }
};