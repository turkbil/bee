<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TENANT DİL KATEGORİLEME SİSTEMİ
     * 
     * Gizli dilleri (is_visible=false) iki kategoriye ayırır:
     * - is_main_language=true: Ana Diller (popüler)  
     * - is_main_language=false: Diğer Diller (AI için)
     */
    public function up(): void
    {
        Schema::table('tenant_languages', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_languages', 'is_main_language')) {
                $table->boolean('is_main_language')->default(true)->after('is_visible')
                    ->comment('Ana dil kategorisi mi? (visible=false olanlar için)');
            }
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
