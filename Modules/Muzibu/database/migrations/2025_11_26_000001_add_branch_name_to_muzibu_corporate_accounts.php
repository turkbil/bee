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
        if (!Schema::hasTable('muzibu_corporate_accounts')) {
            return;
        }

        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('muzibu_corporate_accounts', 'branch_name')) {
                $table->string('branch_name')->nullable()->after('company_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('muzibu_corporate_accounts')) {
            return;
        }

        Schema::table('muzibu_corporate_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('muzibu_corporate_accounts', 'branch_name')) {
                $table->dropColumn('branch_name');
            }
        });
    }
};
