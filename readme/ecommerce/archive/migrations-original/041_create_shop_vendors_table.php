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
        if (Schema::hasTable('shop_vendors')) {
            return;
        }

        Schema::create('shop_vendors', function (Blueprint $table) {
            // Primary Key
            $table->id('vendor_id');

            // User Relation
            $table->foreignId('user_id')->nullable()->comment('Kullanıcı ID - users tablosu ilişkisi');

            // Vendor Type
            $table->enum('vendor_type', ['dealer', 'manufacturer', 'distributor', 'reseller'])
                  ->default('dealer')
                  ->comment('Satıcı tipi: dealer=Bayi, manufacturer=Üretici, distributor=Distribütör, reseller=Satıcı');

            // Basic Info
            $table->json('business_name')->comment('İşletme adı (JSON çoklu dil)');
            $table->json('slug')->comment('URL-dostu slug');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Contact Info
            $table->string('contact_person')->nullable()->comment('Yetkili kişi');
            $table->string('email')->unique()->comment('E-posta adresi');
            $table->string('phone')->nullable()->comment('Telefon numarası');
            $table->string('website')->nullable()->comment('Web sitesi');

            // Tax Info
            $table->string('tax_office')->nullable()->comment('Vergi dairesi');
            $table->string('tax_number')->unique()->nullable()->comment('Vergi numarası');

            // Address
            $table->text('address_line_1')->nullable()->comment('Adres satırı 1');
            $table->text('address_line_2')->nullable()->comment('Adres satırı 2');
            $table->string('city')->nullable()->comment('İl/Şehir');
            $table->string('postal_code', 10)->nullable()->comment('Posta kodu');
            $table->string('country_code', 2)->default('TR')->comment('Ülke kodu (ISO 3166-1 alpha-2)');

            // Commission Settings
            $table->decimal('commission_rate', 5, 2)->default(0)->comment('Komisyon oranı (%)');
            $table->enum('commission_type', ['percentage', 'fixed'])
                  ->default('percentage')
                  ->comment('Komisyon tipi: percentage=Yüzde, fixed=Sabit tutar');
            $table->decimal('fixed_commission', 10, 2)->default(0)->comment('Sabit komisyon tutarı (₺)');

            // Payment Settings
            $table->enum('payment_method', ['bank_transfer', 'check', 'cash', 'other'])
                  ->default('bank_transfer')
                  ->comment('Ödeme yöntemi');
            $table->integer('payment_term_days')->default(30)->comment('Ödeme vadesi (gün)');
            $table->string('bank_name')->nullable()->comment('Banka adı');
            $table->string('bank_account_number')->nullable()->comment('Banka hesap numarası');
            $table->string('iban')->nullable()->comment('IBAN');

            // Limits
            $table->decimal('credit_limit', 14, 2)->nullable()->comment('Kredi limiti (₺)');
            $table->decimal('current_balance', 14, 2)->default(0)->comment('Güncel bakiye (₺)');

            // Statistics
            $table->integer('total_products')->default(0)->comment('Toplam ürün sayısı');
            $table->integer('total_sales')->default(0)->comment('Toplam satış sayısı');
            $table->decimal('total_revenue', 14, 2)->default(0)->comment('Toplam gelir (₺)');
            $table->decimal('total_commission', 12, 2)->default(0)->comment('Toplam komisyon (₺)');

            // Ratings
            $table->decimal('rating_average', 3, 2)->default(0)->comment('Ortalama puan (0-5)');
            $table->integer('rating_count')->default(0)->comment('Puan sayısı');

            // Logo & Images
            $table->string('logo')->nullable()->comment('Logo dosya yolu');
            $table->string('cover_image')->nullable()->comment('Kapak görseli');

            // NOT: SEO ayarları Universal SEO sistemi üzerinden yönetilir (SeoManagement modülü)

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_verified')->default(false)->comment('Doğrulanmış mı?');
            $table->timestamp('verified_at')->nullable()->comment('Doğrulama tarihi');

            // Additional Info
            $table->text('notes')->nullable()->comment('Admin notları');
            $table->json('metadata')->nullable()->comment('Ek veriler (JSON)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('user_id', 'idx_user');
            $table->index('slug', 'idx_slug');
            $table->index('vendor_type', 'idx_type');
            $table->index('is_active', 'idx_active');
            $table->index('is_verified', 'idx_verified');
            $table->index('rating_average', 'idx_rating');

            // Foreign Keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null')
                  ->comment('Kullanıcı silinirse ID null olur');
        })
        ->comment('Satıcılar/Bayiler - Ürün satan firmalar (marketplace için)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_vendors');
    }
};
