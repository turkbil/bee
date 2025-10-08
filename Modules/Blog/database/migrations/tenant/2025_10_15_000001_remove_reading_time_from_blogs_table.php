<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('blogs') && Schema::hasColumn('blogs', 'reading_time')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn('reading_time');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blogs') && !Schema::hasColumn('blogs', 'reading_time')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->integer('reading_time')->nullable()->after('excerpt')->comment('Okuma süresi (dakika)');
            });
        }
    }
};
