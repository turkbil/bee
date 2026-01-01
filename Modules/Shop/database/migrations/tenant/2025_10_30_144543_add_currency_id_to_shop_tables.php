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
        // shop_products tablosuna currency_id ekle
        Schema::table('shop_products', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_products', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('base_price');
                $table->foreign('currency_id')
                    ->references('currency_id')
                    ->on('shop_currencies')
                    ->onDelete('set null');
            }
        });

        // shop_carts tablosuna currency_id ekle
        Schema::table('shop_carts', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_carts', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('currency');
                $table->foreign('currency_id')
                    ->references('currency_id')
                    ->on('shop_currencies')
                    ->onDelete('set null');
            }
        });

        // shop_cart_items tablosuna currency_id ekle
        Schema::table('shop_cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('shop_cart_items', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('total');
                $table->foreign('currency_id')
                    ->references('currency_id')
                    ->on('shop_currencies')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        Schema::table('shop_carts', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        Schema::table('shop_cart_items', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
