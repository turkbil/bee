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
        if (Schema::hasTable('shop_customers')) {
            return;
        }

        Schema::create('shop_customers', function (Blueprint $table) {
            // Primary Key
            $table->id('customer_id');

            // User Relation (Optional - eğer sisteme kayıtlı kullanıcıysa)
            $table->unsignedBigInteger('user_id')->nullable()->comment('User ID - users tablosu ilişkisi (kayıtlı kullanıcı)');

            // Customer Type
            $table->enum('customer_type', ['individual', 'corporate', 'dealer'])
                  ->default('individual')
                  ->index()
                  ->comment('Müşteri tipi: individual=Bireysel, corporate=Kurumsal, dealer=Bayi');

            // Basic Info
            $table->string('first_name')->comment('Ad');
            $table->string('last_name')->comment('Soyad');
            $table->string('email')->unique()->comment('E-posta adresi');
            $table->string('phone')->nullable()->index()->comment('Telefon numarası');

            // Corporate Info
            $table->string('company_name')->nullable()->comment('Şirket adı (kurumsal müşteri için)');
            $table->string('tax_office')->nullable()->comment('Vergi dairesi');
            $table->string('tax_number')->nullable()->comment('Vergi numarası / TC Kimlik');

            // Customer Group
            $table->unsignedBigInteger('customer_group_id')->nullable()->comment('Müşteri grubu ID - shop_customer_groups ilişkisi');

            // Login Info (Guest müşteriler için)
            $table->string('password')->nullable()->comment('Şifre (hash) - Misafir müşteri kayıt olursa');
            $table->rememberToken();

            // Communication Preferences
            $table->boolean('email_verified')->default(false)->comment('E-posta doğrulanmış mı?');
            $table->timestamp('email_verified_at')->nullable()->comment('E-posta doğrulama tarihi');
            $table->boolean('accepts_marketing')->default(false)->comment('Pazarlama e-postalarını kabul ediyor mu?');
            $table->boolean('accepts_sms')->default(false)->comment('SMS bildirimlerini kabul ediyor mu?');

            // Purchase Stats
            $table->integer('total_orders')->default(0)->comment('Toplam sipariş sayısı');
            $table->decimal('total_spent', 14, 2)->default(0)->index()->comment('Toplam harcama (₺)');
            $table->decimal('average_order_value', 12, 2)->default(0)->comment('Ortalama sipariş değeri (₺)');
            $table->timestamp('last_order_at')->nullable()->index()->comment('Son sipariş tarihi');

            // Loyalty Points
            $table->integer('loyalty_points')->default(0)->comment('Sadakat puanı');

            // Notes
            $table->text('notes')->nullable()->comment('Müşteri hakkında notlar (admin için)');
            $table->json('tags')->nullable()->comment('Müşteri etiketleri (JSON array): ["tag1", "tag2", "vs."]');

            // Status
            $table->boolean('is_active')->default(true)->index()->comment('Aktif/Pasif durumu');
            $table->boolean('is_verified')->default(false)->comment('Doğrulanmış müşteri mi? (kimlik kontrolü yapıldı)');
            $table->timestamp('last_login_at')->nullable()->comment('Son giriş tarihi');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('customer_group_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['customer_id', 'is_active'], 'shop_customers_id_active_idx');

            // Foreign Keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('customer_group_id')
                  ->references('customer_group_id')
                  ->on('shop_customer_groups')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_customers');
    }
};
