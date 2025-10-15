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
            $table->boolean('is_premium')
                ->default(false)
                ->after('id')
                ->comment('Premium tenant - Sınırsız AI kullanımı, otomatik SEO ve tüm premium özelliklere erişim');

            // Index for performance
            $table->index('is_premium', 'idx_tenants_is_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex('idx_tenants_is_premium');
            $table->dropColumn('is_premium');
        });
    }
};
