<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            return;
        }

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->string('subscription_number')->unique();

            // Dinamik cycle sistemi
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->nullable()->comment('Legacy billing cycle - nullable for backward compatibility');
            $table->string('cycle_key')->nullable()->comment('Dinamik cycle anahtarı: aylik, yillik, 15-gunluk...');
            $table->json('cycle_metadata')->nullable()->comment('Cycle detayları: {label, duration_days, trial_days, price...}');

            // Fiyat & Tarihler
            $table->decimal('price_per_cycle', 10, 2);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Status & Yönetim
            $table->enum('status', ['active', 'trial', 'expired', 'cancelled'])->default('trial');
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
