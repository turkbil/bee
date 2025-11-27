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
            // Eski foreign key'i sil (shop_customers'a bağlı olan)
            $table->dropForeign('shop_subscriptions_customer_id_foreign');

            // Yeni foreign key ekle (users tablosuna bağlı)
            $table->foreign('customer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Geri al: users foreign key'ini sil
            $table->dropForeign(['customer_id']);

            // Eski foreign key'i geri ekle (shop_customers'a)
            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('shop_customers')
                ->onDelete('cascade');
        });
    }
};
