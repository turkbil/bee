<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // AI Credit sistemi için yeni alanlar
            if (!Schema::hasColumn('tenants', 'ai_credits_balance')) {
                $table->decimal('ai_credits_balance', 10, 4)->default(100.00)->after('ai_tokens_balance')->comment('AI kredi bakiyesi');
            }
            if (!Schema::hasColumn('tenants', 'ai_credits_used_this_month')) {
                $table->decimal('ai_credits_used_this_month', 10, 4)->default(0.00)->after('ai_credits_balance')->comment('Bu ay kullanılan krediler');
            }
            if (!Schema::hasColumn('tenants', 'ai_monthly_credit_limit')) {
                $table->decimal('ai_monthly_credit_limit', 10, 4)->default(0.00)->after('ai_credits_used_this_month')->comment('Aylık kredi limiti (0=sınırsız)');
            }
        });

        // Mevcut tenant'lara başlangıç kredisi ver
        DB::table('tenants')->update([
            'ai_credits_balance' => 100.00,
            'ai_credits_used_this_month' => 0.00,
            'ai_monthly_credit_limit' => 0.00
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'ai_credits_balance',
                'ai_credits_used_this_month', 
                'ai_monthly_credit_limit'
            ]);
        });
    }
};
