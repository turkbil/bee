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
            $table->decimal('token_cost_multiplier', 8, 4)->default(1.0000)->after('priority')
                ->comment('Token maliyet çarpanı - DeepSeek 0.5, OpenAI 1.0, Anthropic 1.2 gibi');
            $table->integer('tokens_per_request_estimate')->default(100)->after('token_cost_multiplier')
                ->comment('Request başına ortalama token tahmini');
            $table->json('cost_structure')->nullable()->after('tokens_per_request_estimate')
                ->comment('Provider-specific token maliyetleri');
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
                'token_cost_multiplier', 
                'tokens_per_request_estimate', 
                'cost_structure',
                'tracks_usage'
            ]);
        });
    }
};
