<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscription_plans')) {
            return;
        }
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id('subscription_plan_id');
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->json('features')->nullable();

            // Dinamik billing cycles (15 gün, 1 ay, 2 ay, istediğin süre)
            $table->json('billing_cycles')->nullable()->comment('Dinamik fiyat döngüleri: {monthly: {price, duration_days, label...}}');

            // Currency & Tax & Display
            $table->string('currency', 10)->default('TRY')->comment('Para birimi kodu (TRY, USD, EUR) - Legacy');
            $table->unsignedBigInteger('currency_id')->nullable()->comment('Para birimi ID - currencies tablosu ilişkisi');
            $table->decimal('tax_rate', 5, 2)->default(20.00)->comment('KDV oranı (%)');
            $table->enum('price_display_mode', ['show', 'hide', 'request'])->default('show')->comment('Fiyat gösterim modu: show=Göster, hide=Gizle, request=Fiyat Sorunuz');

            // Limits & Features
            $table->integer('trial_days')->default(0)->comment('Ücretsiz deneme süresi (gün)');
            $table->boolean('is_trial')->default(false)->comment('Bu bir trial planı mı?');
            $table->integer('device_limit')->default(1)->comment('Maksimum cihaz sayısı');

            // Display & Status
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->boolean('is_featured')->default(false)->comment('Öne çıkan plan mı?');
            $table->boolean('is_active')->default(true)->comment('Aktif mi?');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
