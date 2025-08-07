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
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->decimal('credit_cost_multiplier', 8, 4)->default(1.0000)->after('priority')
                ->comment('Kredi maliyet çarpanı - DeepSeek 0.5, OpenAI 1.0, Anthropic 1.2 gibi');
            $table->integer('credits_per_request_estimate')->default(10)->after('credit_cost_multiplier')
                ->comment('Request başına ortalama kredi tahmini');
            $table->json('cost_structure')->nullable()->after('credits_per_request_estimate')
                ->comment('Provider-specific kredi maliyetleri');
            $table->boolean('tracks_usage')->default(true)->after('cost_structure')
                ->comment('Bu provider kullanım istatistiklerini takip ediyor mu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            $table->dropColumn([
                'credit_cost_multiplier', 
                'credits_per_request_estimate', 
                'cost_structure',
                'tracks_usage'
            ]);
        });
    }
};
