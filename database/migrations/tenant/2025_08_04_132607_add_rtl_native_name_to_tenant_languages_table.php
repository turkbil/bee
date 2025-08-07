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
            // Önce is_default kolonunu ekle
            $table->boolean('is_default')->default(false)->after('is_active');
            // Sonra diğer kolonları ekle
            $table->boolean('is_rtl')->default(false)->after('is_default');
            $table->string('flag_emoji', 10)->nullable()->after('native_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_languages', function (Blueprint $table) {
            $table->dropColumn(['is_rtl', 'flag_emoji', 'is_default']);
        });
    }
};
