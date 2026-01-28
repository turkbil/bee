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
        Schema::table('users', function (Blueprint $table) {
            // Kullanım Koşulları ve Üyelik Sözleşmesi
            $table->boolean('terms_accepted')->default(false)->nullable()->after('is_approved');
            $table->timestamp('terms_accepted_at')->nullable()->after('terms_accepted');
            $table->string('terms_accepted_ip', 45)->nullable()->after('terms_accepted_at');

            // Üyelik ve Satın Alım Faaliyetleri Kapsamında Aydınlatma Metni
            $table->boolean('privacy_accepted')->default(false)->nullable()->after('terms_accepted_ip');
            $table->timestamp('privacy_accepted_at')->nullable()->after('privacy_accepted');
            $table->string('privacy_accepted_ip', 45)->nullable()->after('privacy_accepted_at');

            // Ticari Elektronik İleti Gönderimi Süreçlerine İlişkin Kişisel Verilerin İşlenmesi ve Korunması Hakkında Aydınlatma Metni
            $table->boolean('marketing_accepted')->default(false)->nullable()->after('privacy_accepted_ip');
            $table->timestamp('marketing_accepted_at')->nullable()->after('marketing_accepted');
            $table->string('marketing_accepted_ip', 45)->nullable()->after('marketing_accepted_at');

            // İndeksler
            $table->index('terms_accepted');
            $table->index('privacy_accepted');
            $table->index('marketing_accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['terms_accepted']);
            $table->dropIndex(['privacy_accepted']);
            $table->dropIndex(['marketing_accepted']);

            $table->dropColumn([
                'terms_accepted',
                'terms_accepted_at',
                'terms_accepted_ip',
                'privacy_accepted',
                'privacy_accepted_at',
                'privacy_accepted_ip',
                'marketing_accepted',
                'marketing_accepted_at',
                'marketing_accepted_ip',
            ]);
        });
    }
};
