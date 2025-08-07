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
        Schema::table('ai_credit_usage', function (Blueprint $table) {
            // Add foreign key constraint for ai_provider_id (after ai_providers table exists)
            $table->foreign('ai_provider_id')->references('id')->on('ai_providers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_credit_usage', function (Blueprint $table) {
            $table->dropForeign(['ai_provider_id']);
        });
    }
};
