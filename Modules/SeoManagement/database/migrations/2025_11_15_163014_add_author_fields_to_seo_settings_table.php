<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Google E-E-A-T için author bilgileri - Universal SEO Tab
     */
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            // author ve author_url zaten var, sadece yeni alanlar ekleniyor
            $table->string('author_title')->nullable()->after('author_url')->comment('Author job title/expertise (E-E-A-T)');
            $table->text('author_bio')->nullable()->after('author_title')->comment('Author biography/experience (E-E-A-T)');
            $table->string('author_image')->nullable()->after('author_bio')->comment('Author profile image URL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn(['author_title', 'author_bio', 'author_image']);
        });
    }
};
