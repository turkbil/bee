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
            // Add is_default column (removed from original migration)
            $table->boolean('is_default')->default(false)->after('is_active');
            
            // Add computed is_rtl column for easier queries
            $table->boolean('is_rtl')->storedAs("CASE WHEN direction = 'rtl' THEN 1 ELSE 0 END")->after('direction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_languages', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'is_rtl']);
        });
    }
};
