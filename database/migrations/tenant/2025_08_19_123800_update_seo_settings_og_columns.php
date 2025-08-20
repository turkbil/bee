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
        Schema::table('seo_settings', function (Blueprint $table) {
            // og_title kolonunu og_titles olarak yeniden adlandır
            if (Schema::hasColumn('seo_settings', 'og_title')) {
                $table->renameColumn('og_title', 'og_titles');
            }
            
            // og_description kolonunu og_descriptions olarak yeniden adlandır
            if (Schema::hasColumn('seo_settings', 'og_description')) {
                $table->renameColumn('og_description', 'og_descriptions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            // Geri alma işlemi: og_titles'ı og_title'a çevir
            if (Schema::hasColumn('seo_settings', 'og_titles')) {
                $table->renameColumn('og_titles', 'og_title');
            }
            
            // Geri alma işlemi: og_descriptions'ı og_description'a çevir
            if (Schema::hasColumn('seo_settings', 'og_descriptions')) {
                $table->renameColumn('og_descriptions', 'og_description');
            }
        });
    }
};
