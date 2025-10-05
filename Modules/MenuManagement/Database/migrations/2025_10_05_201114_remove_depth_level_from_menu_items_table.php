<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Portfolio pattern'ine uygun olarak depth_level kolonunu kaldırıyoruz.
     * Depth artık accessor ile dinamik hesaplanacak.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (Schema::hasColumn('menu_items', 'depth_level')) {
                $table->dropColumn('depth_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'depth_level')) {
                $table->integer('depth_level')->default(0)->after('sort_order');
            }
        });
    }
};
