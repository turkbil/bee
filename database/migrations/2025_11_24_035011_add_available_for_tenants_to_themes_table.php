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
        if (!Schema::hasColumn('themes', 'available_for_tenants')) {
            Schema::table('themes', function (Blueprint $table) {
                // available_for_tenants: ["all"] veya [1, 2, 5] gibi tenant ID listesi
                // null veya ["all"] = herkese açık
                $table->json('available_for_tenants')->nullable()->after('is_default');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('available_for_tenants');
        });
    }
};
