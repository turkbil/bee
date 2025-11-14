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
        Schema::table('cart_items', function (Blueprint $table) {
            // Display bilgileri (frontend için)
            $table->string('item_title')->nullable()->after('cartable_id');
            $table->string('item_image')->nullable()->after('item_title');
            $table->string('item_sku')->nullable()->after('item_image');

            // Currency metadata (dönüşüm takibi için)
            $table->string('original_currency', 3)->default('TRY')->after('tax_rate');
            $table->decimal('original_price', 15, 2)->nullable()->after('original_currency');
            $table->decimal('conversion_rate', 10, 4)->default(1.0000)->after('original_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn([
                'item_title',
                'item_image',
                'item_sku',
                'original_currency',
                'original_price',
                'conversion_rate',
            ]);
        });
    }
};
