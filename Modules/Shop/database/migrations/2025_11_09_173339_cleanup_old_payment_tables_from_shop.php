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
        if (Schema::hasTable('shop_orders')) {
            Schema::table('shop_orders', function (Blueprint $table) {
                // Foreign key varsa kaldır
                try {
                    $table->dropForeign(['payment_method_id']);
                } catch (\Exception $e) {
                    // Foreign key yoksa devam et
                }
            });
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
            Schema::table('shop_payments', function (Blueprint $table) {
                try {
                    $table->dropForeign(['payment_method_id']);
                } catch (\Exception $e) {
                    // Foreign key yoksa devam et
                }
                try {
                    $table->dropForeign(['order_id']);
                } catch (\Exception $e) {
                    // Foreign key yoksa devam et
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
