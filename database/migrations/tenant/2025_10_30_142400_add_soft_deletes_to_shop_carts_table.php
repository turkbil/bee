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
            $table->boolean('is_active')->default(true)->after('status');
            $table->softDeletes()->after('last_activity_at');
        });

        Schema::table('shop_cart_items', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('cart_id');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_carts', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'deleted_at']);
        });

        Schema::table('shop_cart_items', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'deleted_at']);
        });
    }
};
