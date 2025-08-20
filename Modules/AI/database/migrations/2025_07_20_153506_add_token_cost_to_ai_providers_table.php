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
        // ai_providers tablosuna sütunları koşullu olarak ekle
        Schema::table('ai_providers', function (Blueprint $table) {
            // credit_cost_multiplier sütununu kontrol et ve ekle
            if (!Schema::hasColumn('ai_providers', 'credit_cost_multiplier')) {
                $table->decimal('credit_cost_multiplier', 8, 4)->default(1.0000)->after('priority')
                    ->comment('Kredi maliyet çarpanı - DeepSeek 0.5, OpenAI 1.0, Anthropic 1.2 gibi');
            }

            // credits_per_request_estimate sütununu kontrol et ve ekle
            if (!Schema::hasColumn('ai_providers', 'credits_per_request_estimate')) {
                $table->integer('credits_per_request_estimate')->default(10)->after('credit_cost_multiplier')
                    ->comment('Request başına ortalama kredi tahmini');
            }

            // cost_structure sütununu kontrol et ve ekle
            if (!Schema::hasColumn('ai_providers', 'cost_structure')) {
                $table->json('cost_structure')->nullable()->after('credits_per_request_estimate')
                    ->comment('Provider-specific kredi maliyetleri');
            }

            // tracks_usage sütununu kontrol et ve ekle
            if (!Schema::hasColumn('ai_providers', 'tracks_usage')) {
                $table->boolean('tracks_usage')->default(true)->after('cost_structure')
                    ->comment('Bu provider kullanım istatistiklerini takip ediyor mu');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // down() metodu için sütunları koşullu olarak silmek daha güvenli
        Schema::table('ai_providers', function (Blueprint $table) {
            if (Schema::hasColumn('ai_providers', 'credit_cost_multiplier')) {
                $table->dropColumn('credit_cost_multiplier');
            }
            if (Schema::hasColumn('ai_providers', 'credits_per_request_estimate')) {
                $table->dropColumn('credits_per_request_estimate');
            }
            if (Schema::hasColumn('ai_providers', 'cost_structure')) {
                $table->dropColumn('cost_structure');
            }
            if (Schema::hasColumn('ai_providers', 'tracks_usage')) {
                $table->dropColumn('tracks_usage');
            }
        });
    }
};
