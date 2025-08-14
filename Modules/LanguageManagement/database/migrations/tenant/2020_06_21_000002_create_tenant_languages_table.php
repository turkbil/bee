<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 3 SEVİYELİ DİL SİSTEMİ - TENANT DİLLERİ
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
        // TENANT DİLLERİ (Frontend/İçerik Dilleri)
        Schema::create('tenant_languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->comment('Dil kodu: tr, en, de, fr, es, vs...');
            $table->string('name')->comment('İngilizce dil adı: Turkish, English, German, French');
            $table->string('native_name')->comment('Yerel dil adı: Türkçe, English, Deutsch, Français');
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr')->comment('Metin yönü: ltr=soldan sağa, rtl=sağdan sola');
            $table->string('flag_icon')->nullable()->comment('Bayrak emoji veya icon kodu');
            $table->boolean('is_active')->default(true)->comment('3 SEVİYELİ SİSTEM: 1=Sitede gözükür, 0=Sadece admin panelde hazırlık');
            // is_default kaldırıldı - artık tenants.tenant_default_locale'de tutuluyor
            $table->enum('url_prefix_mode', ['none', 'except_default', 'all'])
                ->default('except_default')
                ->comment('URL prefix strategy: none=no prefix, except_default=prefix except default lang, all=prefix for all');
            $table->integer('sort_order')->default(0)->comment('Dil sıralama numarası');
            $table->timestamps();
            
            // Her tenant kendi dil kodlarını yönetir
            $table->unique('code'); // Her tenant'ta kod unique
            $table->index(['code', 'is_active']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_languages');
    }
};