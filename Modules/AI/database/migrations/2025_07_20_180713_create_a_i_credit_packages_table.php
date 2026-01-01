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
        if (!Schema::hasTable('ai_credit_packages')) {
            Schema::create('ai_credit_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Başlangıç Paketi, Premium Paket vs.
                $table->unsignedInteger('credit_amount'); // Kredi miktarı
                $table->decimal('price', 10, 2); // Fiyat
                $table->string('currency', 3)->default('TRY'); // Para birimi
                $table->text('description')->nullable(); // Paket açıklaması
                $table->boolean('is_active')->default(true); // Aktif mi?
                $table->boolean('is_popular')->default(false); // Popüler paket mi?
                $table->json('features')->nullable(); // Paket özellikleri JSON
                $table->integer('sort_order')->default(0); // Sıralama
                $table->timestamps();
                
                $table->index(['is_active', 'sort_order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credit_packages');
    }
};
