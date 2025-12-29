<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->table('muzibu_songs', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::connection('tenant')->hasColumn('muzibu_songs', 'encryption_iv')) {
                $table->string('encryption_iv', 32)->nullable()->after('encryption_key');
            }
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('muzibu_songs', function (Blueprint $table) {
            $table->dropColumn(['encryption_key', 'encryption_iv']);
        });
    }
};
