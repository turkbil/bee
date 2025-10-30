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
        Schema::create('shop_currencies', function (Blueprint $table) {
            $table->id('currency_id');
            $table->string('code', 3)->unique(); // TRY, USD, EUR
            $table->string('symbol', 10); // ₺, $, €
            $table->string('name'); // Turkish Lira, US Dollar, Euro
            $table->json('name_translations')->nullable(); // {"tr":"Türk Lirası","en":"Turkish Lira"}
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // TRY'ye göre kur
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('decimal_places')->default(2);
            $table->string('format', 50)->default('symbol_before'); // symbol_before, symbol_after
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_currencies');
    }
};
