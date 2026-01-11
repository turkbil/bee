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
        Schema::table('muzibu_radios', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active')->index()
                ->comment('Featured radio (shown in sidebar)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_radios', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
