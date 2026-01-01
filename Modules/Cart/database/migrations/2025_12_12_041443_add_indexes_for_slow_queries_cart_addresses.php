<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Slow query fix: cart_addresses query taking 415ms
     * Query: SELECT * FROM cart_addresses WHERE user_id = ? AND address_type IN (?, ?) AND is_default_billing = ?
     * Solution: Add composite index on (user_id, address_type, is_default_billing)
     */
    public function up(): void
    {
        Schema::table('cart_addresses', function (Blueprint $table) {
            // Composite index for user cart address queries
            $table->index(['user_id', 'address_type', 'is_default_billing'], 'idx_cart_addresses_user_type_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_addresses', function (Blueprint $table) {
            $table->dropIndex('idx_cart_addresses_user_type_default');
        });
    }
};
