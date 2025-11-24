<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ⚠️ BU MIGRATION SHOP MODÜLÜNDEN ESKI PAYMENT TABLOLARINI KALDIRIR
     *
     * SEBEBİ: Artık global Payment modülü kullanıyoruz (polymorphic ilişki)
     *
     * KALDIRILANLAR:
     * 1. shop_payment_methods tablosu → payment_methods (global)
     * 2. shop_payments tablosu → payments (global, polymorphic)
     * 3. shop_orders tablosundan payment kolonları:
     *    - payment_method_id (artık payments tablosunda)
     *    - paid_amount (artık payments tablosunda)
     *    - remaining_amount (artık payments tablosunda)
     */
    public function up(): void
    {
        // 1. shop_orders'dan payment ilişkili foreign key'leri kaldır
        if (Schema::hasTable('shop_orders') && Schema::hasColumn('shop_orders', 'payment_method_id')) {
            // Foreign key kontrolü - DB query ile
            $dbName = config('database.connections.mysql.database');
            $fkExists = \DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'shop_orders'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'shop_orders_payment_method_id_foreign'
            ", [$dbName]);

            if (!empty($fkExists)) {
                Schema::table('shop_orders', function (Blueprint $table) {
                    $table->dropForeign(['payment_method_id']);
                });
            }
        }

        // 2. shop_orders'dan payment kolonlarını kaldır
        if (Schema::hasTable('shop_orders')) {
            Schema::table('shop_orders', function (Blueprint $table) {
                if (Schema::hasColumn('shop_orders', 'payment_method_id')) {
                    $table->dropColumn('payment_method_id');
                }
                if (Schema::hasColumn('shop_orders', 'paid_amount')) {
                    $table->dropColumn('paid_amount');
                }
                if (Schema::hasColumn('shop_orders', 'remaining_amount')) {
                    $table->dropColumn('remaining_amount');
                }
            });
        }

        // 3. shop_payments tablosundan foreign key'leri kaldır
        if (Schema::hasTable('shop_payments')) {
            $dbName = config('database.connections.mysql.database');

            // payment_method_id FK kontrolü
            $fk1 = \DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'shop_payments'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'shop_payments_payment_method_id_foreign'
            ", [$dbName]);

            // order_id FK kontrolü
            $fk2 = \DB::select("
                SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'shop_payments'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'shop_payments_order_id_foreign'
            ", [$dbName]);

            Schema::table('shop_payments', function (Blueprint $table) use ($fk1, $fk2) {
                if (!empty($fk1)) {
                    $table->dropForeign(['payment_method_id']);
                }
                if (!empty($fk2)) {
                    $table->dropForeign(['order_id']);
                }
            });
        }

        // 4. shop_payments tablosunu kaldır
        Schema::dropIfExists('shop_payments');

        // 5. shop_payment_methods tablosunu kaldır
        Schema::dropIfExists('shop_payment_methods');
    }

    /**
     * Reverse the migrations.
     *
     * ⚠️ ROLLBACK YAPMAZ - Eski tablolar geri gelmez!
     * Arşivlenmiş dosyalar: Modules/Shop/database/migrations/archived_old_payment_system/
     */
    public function down(): void
    {
        // NOT: Rollback yapılmaz çünkü artık global Payment modülü kullanıyoruz.
    }
};
