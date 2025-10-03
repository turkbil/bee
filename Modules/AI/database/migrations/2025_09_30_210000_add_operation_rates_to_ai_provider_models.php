<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * HYBRID KREDİ SİSTEMİ - Operation-based Pricing
     *
     * Bu migration, farklı AI işlemleri için özel fiyatlandırma sistemi ekler.
     *
     * AMAÇ:
     * - Chat: Token bazlı ama indirimli (ucuz)
     * - SEO: Sabit fiyat (öngörülebilir)
     * - Çeviri: Kelime/token tier (paketli)
     * - İçerik: Kelime tier (makul fiyat)
     * - PDF: Sayfa tier (makul fiyat)
     *
     * GERİYE UYUMLU: operation_rates NULL ise eski sistem çalışır
     */
    public function up(): void
    {
        Schema::table('ai_provider_models', function (Blueprint $table) {
            // Operation-based fiyatlandırma JSON
            $table->json('operation_rates')->nullable()->after('markup_percentage')
                ->comment('İşlem türüne göre özel fiyatlandırma (fixed, tier, token_multiplier)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_provider_models', function (Blueprint $table) {
            $table->dropColumn('operation_rates');
        });
    }
};
