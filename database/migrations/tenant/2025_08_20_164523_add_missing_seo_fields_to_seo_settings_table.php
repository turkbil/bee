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
            // Basic meta fields
            $table->string('author')->nullable()->after('keywords');
            $table->string('publisher')->nullable()->after('author');
            $table->string('copyright')->nullable()->after('publisher');
            
            // Open Graph additional fields
            $table->string('og_locale')->nullable()->after('og_type');
            $table->string('og_site_name')->nullable()->after('og_locale');
            
            // Twitter additional fields
            $table->string('twitter_site')->nullable()->after('twitter_image');
            $table->string('twitter_creator')->nullable()->after('twitter_site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'author',
                'publisher', 
                'copyright',
                'og_locale',
                'og_site_name',
                'twitter_site',
                'twitter_creator'
            ]);
        });
    }
};