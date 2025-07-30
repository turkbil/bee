<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Rename url_data to url_value
            $table->renameColumn('url_data', 'url_value');
            
            // Add new columns
            $table->string('visibility')->default('public')->after('is_active');
            $table->string('icon')->nullable()->after('css_class');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Revert column rename
            $table->renameColumn('url_value', 'url_data');
            
            // Drop new columns
            $table->dropColumn(['visibility', 'icon']);
        });
    }
};
