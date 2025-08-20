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
        Schema::table('tenants', function (Blueprint $table) {
            // Basic meta fields
            $table->string('seo_default_author')->nullable()->after('ai_last_used_at');
            $table->string('seo_default_publisher')->nullable()->after('seo_default_author');
            $table->string('seo_default_copyright')->nullable()->after('seo_default_publisher');
            
            // Open Graph fields  
            $table->string('seo_default_og_site_name')->nullable()->after('seo_default_copyright');
            
            // Twitter fields
            $table->string('seo_default_twitter_site')->nullable()->after('seo_default_og_site_name');
            $table->string('seo_default_twitter_creator')->nullable()->after('seo_default_twitter_site');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'seo_default_author',
                'seo_default_publisher', 
                'seo_default_copyright',
                'seo_default_og_site_name',
                'seo_default_twitter_site',
                'seo_default_twitter_creator'
            ]);
        });
    }
};
