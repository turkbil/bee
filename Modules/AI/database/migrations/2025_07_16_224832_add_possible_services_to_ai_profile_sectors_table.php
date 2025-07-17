<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * AI Profile Sectors tablosuna possible_services alanı ekleme
     * 
     * Her sektör için olası hizmetleri JSON formatında tutar.
     * Hizmetler checkbox olarak gösterilecek.
     */
    public function up(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::table('ai_profile_sectors', function (Blueprint $table) {
            $table->json('possible_services')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::table('ai_profile_sectors', function (Blueprint $table) {
            $table->dropColumn('possible_services');
        });
    }
};
