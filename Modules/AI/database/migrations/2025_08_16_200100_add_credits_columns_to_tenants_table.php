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
            if (!Schema::hasColumn('tenants', 'credits')) {
                $table->decimal('credits', 10, 2)->default(0)->after('id');
            }
            
            if (!Schema::hasColumn('tenants', 'ai_credits')) {
                $table->decimal('ai_credits', 10, 2)->default(0)->after('credits');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'ai_credits')) {
                $table->dropColumn('ai_credits');
            }
            
            if (Schema::hasColumn('tenants', 'credits')) {
                $table->dropColumn('credits');
            }
        });
    }
};