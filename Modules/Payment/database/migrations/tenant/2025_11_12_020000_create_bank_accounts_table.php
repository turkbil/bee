<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOT: Bu migration TENANT DATABASE'de çalışır!
     * Her tenant'ın kendi banka hesapları olacak.
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id('bank_account_id');

            // Banka bilgileri
            $table->string('bank_name', 100); // Ziraat Bankası, Garanti BBVA, vb.
            $table->string('branch_name', 100)->nullable(); // Şube adı
            $table->string('branch_code', 20)->nullable(); // Şube kodu

            // Hesap bilgileri
            $table->string('account_holder_name', 150); // Hesap sahibi (firma adı)
            $table->string('account_number', 50)->nullable(); // Hesap numarası
            $table->string('iban', 34); // TR için 26 karakter, max 34 (IBAN standart)
            $table->string('swift_code', 11)->nullable(); // BIC/SWIFT kodu (uluslararası transferler)

            // Para birimi (enum)
            $table->enum('currency', ['TRY', 'USD', 'EUR', 'GBP', 'RUB'])->default('TRY');

            // Durum ve sıralama
            $table->boolean('is_active')->default(true); // Aktif/Pasif
            $table->unsignedInteger('sort_order')->default(0); // Gösterim sırası

            // Müşteriye gösterilecek açıklama (HTML destekli)
            $table->text('description')->nullable(); // "Lütfen açıklama kısmına sipariş numaranızı yazın"

            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Soft delete (silinmiş hesapları sakla)

            // Indexes
            $table->index('is_active');
            $table->index('currency');
            $table->index('sort_order');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
};
