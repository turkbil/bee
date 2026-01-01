<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Subscription status'ları:
     * - pending_payment: Checkout'ta oluşturuldu, ödeme bekleniyor
     * - pending: Ödendi, sırada bekliyor (önündeki subscription'lar bitince aktif olacak)
     * - active: Şu an aktif olan subscription
     * - expired: Süresi dolmuş subscription
     * - cancelled: İptal edilmiş subscription
     */
    public function up(): void
    {
        // Status sütununu string olarak değiştir (enum yerine - daha esnek)
        // MySQL'de enum değiştirmek zor olduğundan string kullanıyoruz
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status', 20)->default('pending_payment')->change();
        });

        // Eski status değerlerini güncelle (gerekirse)
        // 'trial' status'unu 'active' olarak güncelle (trial bilgisi has_trial sütununda)
        DB::table('subscriptions')
            ->where('status', 'trial')
            ->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma gerekirse status'u eski haline getir
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->change();
        });
    }
};
