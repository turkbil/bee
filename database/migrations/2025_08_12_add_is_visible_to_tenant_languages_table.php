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
        Schema::table('tenant_languages', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_languages', 'is_visible')) {
                $table->boolean('is_visible')->default(true)->after('is_active')
                    ->comment('Admin panelde görünür mü? (is_active ile karıştırma)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_languages', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
};