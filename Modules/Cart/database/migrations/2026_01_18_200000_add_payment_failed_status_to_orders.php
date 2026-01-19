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
        // ENUM'a payment_failed değerini ekle
        DB::statement("ALTER TABLE cart_orders MODIFY COLUMN status ENUM('pending','confirmed','processing','ready','shipped','delivered','completed','cancelled','refunded','payment_failed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Önce payment_failed durumundaki siparişleri cancelled yap
        DB::table('cart_orders')
            ->where('status', 'payment_failed')
            ->update(['status' => 'cancelled']);

        // ENUM'dan payment_failed'ı kaldır
        DB::statement("ALTER TABLE cart_orders MODIFY COLUMN status ENUM('pending','confirmed','processing','ready','shipped','delivered','completed','cancelled','refunded') NOT NULL DEFAULT 'pending'");
    }
};
