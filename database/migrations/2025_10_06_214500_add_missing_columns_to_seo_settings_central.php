<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - CENTRAL DATABASE ONLY
     */
    public function up(): void
    {
        // This migration runs on CENTRAL database (mysql connection)
        Schema::connection('mysql')->table('seo_settings', function (Blueprint $table) {
            // Schema type - JSON for multi-language support
            if (!Schema::connection('mysql')->hasColumn('seo_settings', 'schema_type')) {
                $table->json('schema_type')->nullable()->comment('Schema.org page types per language')->after('robots_meta');
            }

            // OG Images - JSON for multi-language support
            if (!Schema::connection('mysql')->hasColumn('seo_settings', 'og_images')) {
                $table->json('og_images')->nullable()->comment('Multi-language OG images')->after('og_descriptions');
            }

            // Author URL
            if (!Schema::connection('mysql')->hasColumn('seo_settings', 'author_url')) {
                $table->string('author_url')->nullable()->after('author');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('seo_settings', function (Blueprint $table) {
            $table->dropColumn(['schema_type', 'og_images', 'author_url']);
        });
    }
};
