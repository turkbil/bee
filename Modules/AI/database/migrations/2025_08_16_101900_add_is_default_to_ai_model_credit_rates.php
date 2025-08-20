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
            $table->boolean('is_default')->default(false)->after('is_active')->comment('Bu model provider için varsayılan mı?');
            
            // Index ekle
            $table->index(['provider_id', 'is_default'], 'idx_provider_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_model_credit_rates', function (Blueprint $table) {
            $table->dropIndex('idx_provider_default');
            $table->dropColumn('is_default');
        });
    }
};