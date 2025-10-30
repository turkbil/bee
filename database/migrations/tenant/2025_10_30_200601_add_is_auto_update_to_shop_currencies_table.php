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
        Schema::table('shop_currencies', function (Blueprint $table) {
            $table->boolean('is_auto_update')->default(false)->after('is_default')->comment('TCMB\'den otomatik güncelleme yapılsın mı?');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_currencies', function (Blueprint $table) {
            $table->dropColumn('is_auto_update');
        });
    }
};
