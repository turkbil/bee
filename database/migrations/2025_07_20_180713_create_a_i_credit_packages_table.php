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
        Schema::create('ai_credit_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Başlangıç Paketi, Premium Paket vs.
            $table->text('description')->nullable(); // Paket açıklaması
            $table->decimal('credits', 10, 2); // Kredi miktarı
            $table->decimal('price_usd', 8, 2); // USD fiyat
            $table->decimal('price_try', 10, 2)->nullable(); // TL fiyat (opsiyonel)
            $table->boolean('is_popular')->default(false); // Popüler paket mi?
            $table->boolean('is_active')->default(true); // Aktif mi?
            $table->integer('sort_order')->default(0); // Sıralama
            $table->json('features')->nullable(); // Paket özellikleri JSON
            $table->decimal('discount_percentage', 5, 2)->default(0); // İndirim yüzdesi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credit_packages');
    }
};
