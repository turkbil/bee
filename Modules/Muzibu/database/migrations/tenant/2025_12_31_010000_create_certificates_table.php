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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('certificate_code', 32)->unique(); // MZB-2025-XXXX
            $table->string('qr_hash', 64)->unique(); // SHA-256 hash for URL
            $table->string('member_name'); // Firma/Kişi adı (imla düzeltilmiş)
            $table->string('tax_office')->nullable(); // Vergi Dairesi
            $table->string('tax_number')->nullable(); // Vergi Numarası
            $table->text('address')->nullable(); // Adres
            $table->date('membership_start'); // İlk ÜCRETLİ üyelik tarihi
            $table->unsignedInteger('view_count')->default(0); // Doğrulama sayacı
            $table->timestamp('issued_at'); // Sertifika oluşturulma tarihi
            $table->boolean('is_valid')->default(true); // Geçerli mi
            $table->timestamps();

            // Index for faster lookups
            $table->index('user_id');
            $table->index('qr_hash');
            $table->index('is_valid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
