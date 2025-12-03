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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Dinamik cycle sistemi için yeni kolonlar
            $table->string('cycle_key')->nullable()->after('billing_cycle'); // 'aylik', 'yillik', '15-gunluk'...
            $table->json('cycle_metadata')->nullable()->after('cycle_key'); // {label, duration_days, trial_days...}

            // billing_cycle nullable yap (backward compatibility)
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->nullable()->change();
        });

        // Mevcut kayıtları migrate et (billing_cycle → cycle_key)
        DB::table('subscriptions')->whereNotNull('billing_cycle')->get()->each(function ($subscription) {
            // billing_cycle'dan cycle_key oluştur
            $cycleKey = match($subscription->billing_cycle) {
                'daily' => 'gunluk',
                'weekly' => 'haftalik',
                'monthly' => 'aylik',
                'quarterly' => '3-aylik',
                'yearly' => 'yillik',
                default => 'aylik'
            };

            // duration_days hesapla
            $durationDays = match($subscription->billing_cycle) {
                'daily' => 1,
                'weekly' => 7,
                'monthly' => 30,
                'quarterly' => 90,
                'yearly' => 365,
                default => 30
            };

            // cycle_metadata oluştur
            $cycleMetadata = [
                'label' => [
                    'tr' => ucfirst($cycleKey),
                    'en' => ucfirst($subscription->billing_cycle),
                ],
                'duration_days' => $durationDays,
                'price' => (float) $subscription->price_per_cycle,
            ];

            DB::table('subscriptions')
                ->where('subscription_id', $subscription->subscription_id)
                ->update([
                    'cycle_key' => $cycleKey,
                    'cycle_metadata' => json_encode($cycleMetadata),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['cycle_key', 'cycle_metadata']);

            // billing_cycle'ı eski haline getir
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->nullable(false)->change();
        });
    }
};
