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
        Schema::table('domains', function (Blueprint $table) {
            // Primary domain flag - tenant başına sadece 1 tane is_primary=1 olacak
            $table->tinyInteger('is_primary')->default(0)->after('domain');

            // Performance için index
            $table->index('is_primary', 'idx_is_primary');

            // tenant_id + is_primary unique constraint (sadece 1 primary per tenant)
            // NOT: Bu constraint'i sonra ekleyeceğiz, önce mevcut datayı düzeltelim
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropIndex('idx_is_primary');
            $table->dropColumn('is_primary');
        });
    }
};
