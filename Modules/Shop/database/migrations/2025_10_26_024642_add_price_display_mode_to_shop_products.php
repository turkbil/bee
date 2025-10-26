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
        Schema::table('shop_products', function (Blueprint $table) {
            // Add price_display_mode column
            $table->enum('price_display_mode', ['show', 'hide', 'request'])
                ->default('show')
                ->after('price_on_request')
                ->comment('Fiyat gösterim modu: show=Göster, hide=Gizle, request=Fiyat Sorunuz');
        });

        // Migrate existing data: price_on_request=true -> price_display_mode='request'
        DB::table('shop_products')
            ->where('price_on_request', true)
            ->update(['price_display_mode' => 'request']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_products', function (Blueprint $table) {
            $table->dropColumn('price_display_mode');
        });
    }
};
