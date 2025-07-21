<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tenant ID alanı ekle
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->after('id')->comment('Kullanıcının bağlı olduğu tenant');
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
            }
        });

        // Mevcut kullanıcıları ilk tenant'a ata (development için)
        $firstTenant = DB::table('tenants')->first();
        if ($firstTenant) {
            DB::table('users')->whereNull('tenant_id')->update([
                'tenant_id' => $firstTenant->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
