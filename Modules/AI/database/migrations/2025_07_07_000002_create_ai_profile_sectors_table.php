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
        
        Schema::connection('central')->create('ai_profile_sectors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // e-commerce, health, education vb.
            
            // Alt kategori desteği (add_subcategories_to_ai_profile_sectors_table.php entegrasyonu)
            $table->unsignedBigInteger('category_id')->nullable(); // Ana kategori ID (0=ana kategori, diğer=alt kategori)
            
            $table->string('name'); // E-Ticaret, Sağlık, Eğitim vb.
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->string('emoji', 10)->nullable(); // 💻, 🏥, ⚖️ vb.
            $table->string('color', 20)->nullable(); // blue, green, purple vb.
            $table->text('description')->nullable();
            $table->json('possible_services')->nullable(); // Olası hizmetler JSON formatında
            $table->text('keywords')->nullable(); // Arama anahtar kelimeleri
            $table->boolean('is_subcategory')->default(false); // Alt kategori mi?
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Foreign key constraint (self referencing)
            $table->foreign('category_id')->references('id')->on('ai_profile_sectors')->onDelete('cascade');
            
            // Indexes
            $table->index('code');
            $table->index('category_id');
            $table->index('is_subcategory');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::connection('central')->dropIfExists('ai_profile_sectors');
    }
};