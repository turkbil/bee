<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * Sektör listesi tablosu - SADECE CENTRAL VERITABANI
     * 
     * AI profil oluştururken seçilebilecek sektörleri tutar.
     * Her sektörün kendine özel soruları ve özellikleri vardır.
     */
    public function up(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::create('ai_profile_sectors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // e-commerce, health, education vb.
            $table->string('name'); // E-Ticaret, Sağlık, Eğitim vb.
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::dropIfExists('ai_profile_sectors');
    }
};