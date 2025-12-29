<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tenant users tablosuna subscription_expires_at sütunu ekler.
     * Bu sütun, kullanıcının toplam subscription bitiş tarihini tutar.
     * Tüm zincirlenmiş subscription'ların en son bitiş tarihi burada saklanır.
     * Premium kontrolü için hızlı erişim sağlar (DB sorgusu gerektirmez).
     */
    public function up(): void
    {
        // Tenant users tablosunda subscription_expires_at yoksa ekle
        if (!Schema::hasColumn('users', 'subscription_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('subscription_expires_at')->nullable()->after('remember_token');
                $table->index('subscription_expires_at', 'users_subscription_expires_at_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'subscription_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_subscription_expires_at_index');
                $table->dropColumn('subscription_expires_at');
            });
        }
    }
};
