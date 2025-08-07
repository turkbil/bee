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
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            $table->json('data')->nullable()->after('is_completed')->comment('Context data for AI');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
};
