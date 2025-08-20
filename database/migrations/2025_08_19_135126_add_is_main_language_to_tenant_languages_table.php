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
            $table->boolean('is_main_language')->default(true)->after('is_visible')->comment('Ana dil kategorisi mi? (visible=false olanlar için)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_languages', function (Blueprint $table) {
            $table->dropColumn('is_main_language');
        });
    }
};
