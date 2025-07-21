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
            $table->unsignedBigInteger('default_ai_provider_id')->nullable()->after('is_active');
            $table->json('ai_settings')->nullable()->after('default_ai_provider_id')->comment('Tenant-specific AI configuration');
            
            // Foreign key constraint
            $table->foreign('default_ai_provider_id')->references('id')->on('ai_providers')->onDelete('set null');
            
            // Index for better performance
            $table->index('default_ai_provider_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['default_ai_provider_id']);
            $table->dropIndex(['default_ai_provider_id']);
            $table->dropColumn(['default_ai_provider_id', 'ai_settings']);
        });
    }
};
