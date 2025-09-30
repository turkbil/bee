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
            // 2025 SEO Standard: Schema.org Article Type for Rich Results
            // Çoklu dil desteği için JSON - {"tr": "Article", "en": "BlogPosting"}
            $table->json('schema_types')->nullable()
                ->comment('Schema.org page types per language for Google Rich Results');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn('schema_types');
        });
    }
};