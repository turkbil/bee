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
        Schema::table('muzibu_playlists', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_radio')->index()
                ->comment('Featured playlist (shown in sidebar)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_playlists', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
