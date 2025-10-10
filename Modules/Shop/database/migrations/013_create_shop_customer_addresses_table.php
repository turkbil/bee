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
        if (Schema::hasTable('shop_customer_addresses')) {
            return;
        }

        Schema::create('shop_customer_addresses', function (Blueprint $table) {
            // Primary Key
            $table->id('address_id');

            // Customer Relation
            $table->unsignedBigInteger('customer_id')->comment('Müşteri ID - shop_customers ilişkisi');

            // Address Type
            $table->enum('address_type', ['billing', 'shipping', 'both'])
                  ->default('both')
                  ->index()
                  ->comment('Adres tipi: billing=Fatura, shipping=Teslimat, both=Her ikisi');

            // Personal Info
            $table->string('first_name')->comment('Ad');
            $table->string('last_name')->comment('Soyad');
            $table->string('company_name')->nullable()->comment('Şirket adı (kurumsal adres için)');
            $table->string('tax_office')->nullable()->comment('Vergi dairesi (fatura adresi için)');
            $table->string('tax_number')->nullable()->comment('Vergi numarası / TC Kimlik');

            // Contact Info
            $table->string('phone')->comment('Telefon numarası');
            $table->string('email')->nullable()->comment('E-posta adresi (opsiyonel)');

            // Address Details
            $table->text('address_line_1')->comment('Adres satırı 1 (sokak, bina no)');
            $table->text('address_line_2')->nullable()->comment('Adres satırı 2 (daire no, vb)');
            $table->string('neighborhood')->nullable()->comment('Mahalle');
            $table->string('district')->comment('İlçe');
            $table->string('city')->index()->comment('İl/Şehir');
            $table->string('postal_code', 10)->nullable()->comment('Posta kodu');
            $table->string('country_code', 2)->default('TR')->index()->comment('Ülke kodu (ISO 3166-1 alpha-2: TR, US, DE)');

            // Default Settings
            $table->boolean('is_default_billing')->default(false)->index()->comment('Varsayılan fatura adresi mi?');
            $table->boolean('is_default_shipping')->default(false)->index()->comment('Varsayılan teslimat adresi mi?');

            // Additional Info
            $table->text('delivery_notes')->nullable()->comment('Teslimat notları (kapıcıya söyleyin, vb)');
            $table->json('metadata')->nullable()->comment('Ek veriler: {"key":"value","vs.":"..."}');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('customer_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['customer_id', 'is_default_billing'], 'shop_customer_addr_cust_default_billing_idx');
            $table->index(['customer_id', 'is_default_shipping'], 'shop_customer_addr_cust_default_shipping_idx');

            // Foreign Keys
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_customer_addresses');
    }
};
