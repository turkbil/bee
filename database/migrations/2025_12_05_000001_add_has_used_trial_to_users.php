<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'has_used_trial')) {
                $table->boolean('has_used_trial')->default(false)->after('email_verified_at')->comment('Kullanıcı trial kullandı mı? (Ömür boyu 1 kere)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'has_used_trial')) {
                $table->dropColumn('has_used_trial');
            }
        });
    }
};
