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
        Schema::table('muzibu_abuse_reports', function (Blueprint $table) {
            $table->json('patterns_json')->nullable()->after('daily_stats')
                  ->comment('Tespit edilen suistimal pattern\'leri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('muzibu_abuse_reports', function (Blueprint $table) {
            $table->dropColumn('patterns_json');
        });
    }
};
