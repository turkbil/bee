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
        if (Schema::hasTable('shop_tax_rates')) {
            return;
        }

        Schema::create('shop_tax_rates', function (Blueprint $table) {
            $table->comment('Vergi oranları - Bölge bazlı farklı vergi oranları');

            // Primary Key
            $table->id('tax_rate_id');

            // Relations
            $table->foreignId('tax_id')->comment('Vergi ID - shop_taxes ilişkisi');

            // Geographic Info
            $table->string('country_code', 2)->comment('Ülke kodu (ISO 3166-1 alpha-2: TR, US, DE)');
            $table->string('state_code')->nullable()->comment('Eyalet/İl kodu (California: CA, İstanbul: 34)');
            $table->string('city')->nullable()->comment('Şehir/İlçe');
            $table->string('postal_code', 10)->nullable()->comment('Posta kodu');

            // Rate
            $table->decimal('rate', 5, 2)->comment('Vergi oranı (%) - bu bölge için özel oran');

            // Priority
            $table->integer('priority')->default(0)->comment('Öncelik (birden fazla kural eşleşirse hangisi uygulanacak)');

            // Validity Period
            $table->timestamp('valid_from')->nullable()->comment('Geçerlilik başlangıç tarihi');
            $table->timestamp('valid_until')->nullable()->comment('Geçerlilik bitiş tarihi');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('tax_id');
            $table->index('country_code');
            $table->index('state_code');
            $table->index('postal_code');
            $table->index('is_active');
            $table->index(['country_code', 'state_code']);
            $table->index(['valid_from', 'valid_until']);

            // Foreign Keys
            $table->foreign('tax_id')
                  ->references('tax_id')
                  ->on('shop_taxes')
                  ->onDelete('cascade')
                  ->comment('Vergi silinirse oranları da silinir');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_tax_rates');
    }
};
