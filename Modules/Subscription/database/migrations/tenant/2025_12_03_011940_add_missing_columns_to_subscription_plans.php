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
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Kolon zaten varsa hata vermemesi için kontrol et
            if (!Schema::hasColumn('subscription_plans', 'compare_price_monthly')) {
                $table->decimal('compare_price_monthly', 10, 2)->nullable()->after('price_yearly');
            }

            if (!Schema::hasColumn('subscription_plans', 'compare_price_yearly')) {
                $table->decimal('compare_price_yearly', 10, 2)->nullable()->after('compare_price_monthly');
            }

            if (!Schema::hasColumn('subscription_plans', 'device_limit')) {
                $table->integer('device_limit')->default(1)->after('trial_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (Schema::hasColumn('subscription_plans', 'compare_price_monthly')) {
                $table->dropColumn('compare_price_monthly');
            }

            if (Schema::hasColumn('subscription_plans', 'compare_price_yearly')) {
                $table->dropColumn('compare_price_yearly');
            }

            if (Schema::hasColumn('subscription_plans', 'device_limit')) {
                $table->dropColumn('device_limit');
            }
        });
    }
};
