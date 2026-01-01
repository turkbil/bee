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
        Schema::table('shop_carts', function (Blueprint $table) {
            // Rename 'currency' field to 'currency_code' to avoid conflict with currency() relation
            $table->renameColumn('currency', 'currency_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_carts', function (Blueprint $table) {
            // Revert back to 'currency'
            $table->renameColumn('currency_code', 'currency');
        });
    }
};
