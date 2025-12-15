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
        if (Schema::hasTable('shop_order_addresses')) {
            return;
        }

        Schema::create('shop_order_addresses', function (Blueprint $table) {
            $table->comment('Sipariş adresleri - Fatura ve teslimat adresleri snapshot (değişmez kayıt)');

            // Primary Key
            $table->id('order_address_id');

            // Order Relation
            $table->foreignId('order_id')->comment('Sipariş ID - shop_orders ilişkisi');

            // Address Type
            $table->enum('address_type', ['billing', 'shipping'])
                  ->comment('Adres tipi: billing=Fatura, shipping=Teslimat');

            // Personal Info (Snapshot)
            $table->string('first_name')->comment('Ad (snapshot)');
            $table->string('last_name')->comment('Soyad (snapshot)');
            $table->string('company_name')->nullable()->comment('Şirket adı (snapshot)');
            $table->string('tax_office')->nullable()->comment('Vergi dairesi (snapshot)');
            $table->string('tax_number')->nullable()->comment('Vergi numarası / TC Kimlik (snapshot)');

            // Contact Info (Snapshot)
            $table->string('phone')->comment('Telefon numarası (snapshot)');
            $table->string('email')->nullable()->comment('E-posta adresi (snapshot)');

            // Address Details (Snapshot)
            $table->text('address_line_1')->comment('Adres satırı 1 (snapshot)');
            $table->text('address_line_2')->nullable()->comment('Adres satırı 2 (snapshot)');
            $table->string('neighborhood')->nullable()->comment('Mahalle (snapshot)');
            $table->string('district')->comment('İlçe (snapshot)');
            $table->string('city')->comment('İl/Şehir (snapshot)');
            $table->string('postal_code', 10)->nullable()->comment('Posta kodu (snapshot)');
            $table->string('country_code', 2)->default('TR')->comment('Ülke kodu (snapshot - ISO 3166-1 alpha-2)');
            $table->string('country_name')->nullable()->comment('Ülke adı (snapshot - Türkiye, United States)');

            // Additional Info
            $table->text('delivery_notes')->nullable()->comment('Teslimat notları (snapshot)');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('order_id');
            $table->index('address_type');
            $table->index(['order_id', 'address_type']);

            // Foreign Keys
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('shop_orders')
                  ->onDelete('cascade')
                  ->comment('Sipariş silinirse adresleri de silinir');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_order_addresses');
    }
};
