<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * AI Profile Sectors tablosuna alt kategori desteği ekleme
     * 
     * Alt kategoriler (subcategories) sistemi:
     * - Ana sektörler: technology, health, legal vb.
     * - Alt kategoriler: web_development, mobile_apps, family_law vb.
     * - Keywords: Arama için genişletilmiş anahtar kelimeler
     * - Parent/Child ilişkisi: Alt kategoriler ana sektörlere bağlı
     */
    public function up(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::table('ai_profile_sectors', function (Blueprint $table) {
            // Alt kategori desteği
            $table->unsignedBigInteger('category_id')->nullable()->after('code'); // Ana kategori ID (0=ana kategori, diğer=alt kategori)
            $table->string('emoji', 10)->nullable()->after('icon'); // 💻, 🏥, ⚖️ vb.
            $table->string('color', 20)->nullable()->after('emoji'); // blue, green, purple vb.
            $table->text('keywords')->nullable()->after('description'); // Arama anahtar kelimeleri
            $table->boolean('is_subcategory')->default(false)->after('keywords'); // Alt kategori mi?
            
            // Foreign key constraint (self referencing)
            $table->foreign('category_id')->references('id')->on('ai_profile_sectors')->onDelete('cascade');
            
            // Indexes
            $table->index('category_id');
            $table->index('is_subcategory');
        });
    }

    public function down(): void
    {
        // Sadece central veritabanında çalışır
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        Schema::table('ai_profile_sectors', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'emoji', 
                'color',
                'keywords',
                'is_subcategory'
            ]);
        });
    }
};