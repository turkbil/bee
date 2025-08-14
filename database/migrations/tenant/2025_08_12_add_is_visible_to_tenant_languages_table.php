<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 3 SEVİYELİ DİL SİSTEMİ - KULLANIM KILAVUZU
     * 
     * LEVEL 1 - AKTİF DİLLER: is_active=1 AND is_visible=1
     * → Sitede kullanıcılar tarafından görülebilen diller
     * 
     * LEVEL 2 - PASİF DİLLER: is_active=0 AND is_visible=1  
     * → Admin panelde hazırlık için görünen ama sitede gözükmeyen diller
     * 
     * LEVEL 3 - DİĞER DİLLER: is_visible=0 (is_active değeri önemsiz)
     * → Hiçbir yerde gözükmeyen, var olmamakla aynı dünya dilleri
     * → Bu diller pasif veya aktif yapılabilir (seviye 1-2'ye geçirilebilir)
     */
    public function up(): void
    {
        Schema::table('tenant_languages', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_languages', 'is_visible')) {
                $table->boolean('is_visible')->default(true)->after('is_active')
                    ->comment('3 SEVİYELİ DİL SİSTEMİ: false=Hiçbir yerde gözükmeyen dünya dilleri, true=Admin panelde en azından görünen');
            }
        });
        
        // Mevcut kolonlara da comment ekle
        Schema::table('tenant_languages', function (Blueprint $table) {
            $table->boolean('is_active')->comment('1=Sitede gözükür, 0=Sadece admin panelde hazırlık')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_languages', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
};