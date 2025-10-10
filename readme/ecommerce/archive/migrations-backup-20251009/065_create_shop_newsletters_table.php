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
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('shop_newsletters')) {
            return;
        }

        Schema::create('shop_newsletters', function (Blueprint $table) {
            // Primary Key
            $table->id('newsletter_id');

            // Subscriber Info
            $table->string('email')->unique()->comment('E-posta adresi');
            $table->string('name')->nullable()->comment('Ad Soyad');
            $table->foreignId('customer_id')->nullable()->comment('Müşteri ID - shop_customers ilişkisi');

            // Status
            $table->enum('status', ['subscribed', 'unsubscribed', 'bounced', 'complained'])
                  ->default('subscribed')
                  ->comment('Durum: subscribed=Abone, unsubscribed=Abonelik iptal, bounced=Geri döndü, complained=Şikayet');

            // Subscription
            $table->timestamp('subscribed_at')->nullable()->comment('Abone olma tarihi');
            $table->timestamp('unsubscribed_at')->nullable()->comment('Abonelik iptal tarihi');
            $table->text('unsubscribe_reason')->nullable()->comment('İptal nedeni');

            // Preferences
            $table->json('interests')->nullable()->comment('İlgi alanları (JSON array - ["electronics","clothing"])');
            $table->json('preferences')->nullable()->comment('Tercihler (JSON - {"frequency":"weekly","format":"html"})');

            // Source
            $table->string('source')->nullable()->comment('Kaynak (homepage, checkout, popup, import)');
            $table->string('utm_source')->nullable()->comment('UTM kaynak');
            $table->string('utm_campaign')->nullable()->comment('UTM kampanya');

            // Email Stats
            $table->integer('emails_sent')->default(0)->comment('Gönderilen e-posta sayısı');
            $table->integer('emails_opened')->default(0)->comment('Açılan e-posta sayısı');
            $table->integer('emails_clicked')->default(0)->comment('Tıklanan e-posta sayısı');
            $table->timestamp('last_email_sent_at')->nullable()->comment('Son e-posta gönderim tarihi');
            $table->timestamp('last_email_opened_at')->nullable()->comment('Son e-posta açma tarihi');

            // Verification
            $table->boolean('is_verified')->default(false)->comment('Doğrulandı mı?');
            $table->string('verification_token')->nullable()->comment('Doğrulama token');
            $table->timestamp('verified_at')->nullable()->comment('Doğrulama tarihi');

            // GDPR Consent
            $table->boolean('gdpr_consent')->default(false)->comment('GDPR onayı var mı?');
            $table->timestamp('consent_given_at')->nullable()->comment('Onay verme tarihi');
            $table->string('ip_address', 45)->nullable()->comment('IP adresi (kayıt anında)');

            // Additional Info
            $table->text('notes')->nullable()->comment('Notlar');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('email', 'idx_email');
            $table->index('customer_id', 'idx_customer');
            $table->index('status', 'idx_status');
            $table->index('is_verified', 'idx_verified');
            $table->index('subscribed_at', 'idx_subscribed');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade')
                  ->comment('Müşteri silinirse ID null olur ama abonelik kalır');
        })
        ->comment('Bülten aboneleri - E-posta bülten abonelikleri');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_newsletters');
    }
};
