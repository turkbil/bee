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
        // First, set default JSON values for NULL fields
        \DB::statement("UPDATE seo_settings SET og_title = '{}' WHERE og_title IS NULL");
        \DB::statement("UPDATE seo_settings SET og_description = '{}' WHERE og_description IS NULL");
        
        Schema::table('seo_settings', function (Blueprint $table) {
            // Change og_title from string to json
            $table->json('og_title')->nullable()->change();
            // Change og_description from text to json  
            $table->json('og_description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            // Revert og_title to string
            $table->string('og_title')->nullable()->change();
            // Revert og_description to text
            $table->text('og_description')->nullable()->change();
        });
    }
};
