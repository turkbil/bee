<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * is_homepage kolonu kontrol - zaten varsa sadece anasayfayı set et
     * is_homepage=1 olan sayfalar için URL'de slug gözükmez, sadece dil prefix'i gösterilir
     */
    public function up(): void
    {
        // is_homepage kolonu yoksa ekle (normalde zaten var)
        if (!Schema::hasColumn('pages', 'is_homepage')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->boolean('is_homepage')->default(false)->after('is_active');
                $table->index('is_homepage');
            });
        }

        // Anasayfa slug'ına sahip sayfayı is_homepage=1 yap
        DB::table('pages')
            ->where('slug->tr', 'anasayfa')
            ->orWhere('slug->en', 'homepage')
            ->orWhere('slug->en', 'home')
            ->update(['is_homepage' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Anasayfa'yı is_homepage=0 yap
        DB::table('pages')
            ->where('is_homepage', true)
            ->update(['is_homepage' => false]);
    }
};
