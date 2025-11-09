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
        if (Schema::hasTable('shop_payment_methods')) {
            return;
        }

        Schema::create('shop_payment_methods', function (Blueprint $table) {
            $table->comment('Ödeme yöntemleri - Kredi kartı, havale, kapıda ödeme vb.');

            // Primary Key
            $table->id('payment_method_id');

            // Basic Info
            $table->json('title')->comment('Ödeme yöntemi adı ({"tr":"Kredi Kartı","en":"Credit Card"})');
            $table->string('slug')->unique()->comment('URL-dostu slug (kredi-karti)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Payment Type
            $table->enum('payment_type', [
                'credit_card',      // Kredi kartı
                'debit_card',       // Banka kartı
                'bank_transfer',    // Havale/EFT
                'cash_on_delivery', // Kapıda ödeme
                'cash',             // Nakit
                'wire_transfer',    // Banka havalesi
                'check',            // Çek
                'installment',      // Taksit
                'paypal',           // PayPal
                'stripe',           // Stripe
                'other'             // Diğer
            ])->comment('Ödeme tipi');

            // Gateway Info
            $table->string('gateway_name')->nullable()->comment('Ödeme gateway adı (iyzico, paytr, stripe, vb)');
            $table->string('gateway_mode')->nullable()->comment('Gateway modu (test, live)');
            $table->json('gateway_config')->nullable()->comment('Gateway ayarları (JSON - API keys, merchant ID, vb)');

            // Fees
            $table->decimal('fixed_fee', 10, 2)->default(0)->comment('Sabit komisyon (₺)');
            $table->decimal('percentage_fee', 5, 2)->default(0)->comment('Yüzde komisyon (%)');
            $table->decimal('min_amount', 10, 2)->nullable()->comment('Minimum tutar (₺)');
            $table->decimal('max_amount', 14, 2)->nullable()->comment('Maximum tutar (₺)');

            // Installment Settings
            $table->boolean('supports_installment')->default(false)->comment('Taksit desteği var mı?');
            $table->json('installment_options')->nullable()->comment('Taksit seçenekleri (JSON - [{\"months\":3,\"rate\":1.05}])');
            $table->integer('max_installments')->default(1)->comment('Maksimum taksit sayısı');

            // Currency
            $table->json('supported_currencies')->nullable()->comment('Desteklenen para birimleri (JSON - ["TRY","USD","EUR"])');

            // Display
            $table->string('icon')->nullable()->comment('İkon dosya yolu veya sınıfı');
            $table->string('logo_url')->nullable()->comment('Logo URL');
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

            // Status & Rules
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('requires_verification')->default(false)->comment('Doğrulama gerektirir mi?');
            $table->boolean('is_manual')->default(false)->comment('Manuel onay gerektirir mi? (havale gibi)');

            // Availability
            $table->boolean('available_for_b2c')->default(true)->comment('B2C müşteriler kullanabilir mi?');
            $table->boolean('available_for_b2b')->default(true)->comment('B2B müşteriler kullanabilir mi?');
            $table->json('customer_group_ids')->nullable()->comment('Sadece belirli müşteri gruplarına özel (JSON array)');

            // Instructions
            $table->json('instructions')->nullable()->comment('Ödeme talimatları (JSON çoklu dil - havale için IBAN vb)');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('slug');
            $table->index('payment_type');
            $table->index('is_active');
            $table->index('gateway_name');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_payment_methods');
    }
};
