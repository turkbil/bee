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
            $table->string('default_ai_model', 255)->nullable()->after('default_ai_provider_id')->comment('Tenant varsayılan AI model');
            $table->index('default_ai_model', 'idx_ai_model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex('idx_ai_model');
            $table->dropColumn('default_ai_model');
        });
    }
};