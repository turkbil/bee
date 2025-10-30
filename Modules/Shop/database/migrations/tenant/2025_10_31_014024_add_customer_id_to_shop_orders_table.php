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
        Schema::table('shop_orders', function (Blueprint $table) {
            // Customer relation (nullable - misafir sipariş olabilir)
            $table->unsignedBigInteger('customer_id')->nullable()->after('tenant_id');

            // Foreign key
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->nullOnDelete();

            // Index
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
