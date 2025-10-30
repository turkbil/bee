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
            $table->timestamp('last_updated_at')->nullable()->after('is_auto_update')->comment('TCMB\'den son güncelleme zamanı');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_currencies', function (Blueprint $table) {
            $table->dropColumn('last_updated_at');
        });
    }
};
