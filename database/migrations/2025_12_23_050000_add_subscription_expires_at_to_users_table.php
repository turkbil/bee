<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kullanıcının toplam subscription bitiş tarihini tutar.
     * Tüm zincirlenmiş subscription'ların en son bitiş tarihi burada saklanır.
     * Premium kontrolü için hızlı erişim sağlar.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('subscription_expires_at')->nullable()->after('has_used_trial');
            $table->index('subscription_expires_at', 'users_subscription_expires_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_subscription_expires_at_index');
            $table->dropColumn('subscription_expires_at');
        });
    }
};
