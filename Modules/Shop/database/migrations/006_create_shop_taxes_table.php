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
        if (Schema::hasTable('shop_taxes')) {
            return;
        }

        Schema::create('shop_taxes', function (Blueprint $table) {
            $table->comment('Vergiler - KDV, ÖTV ve diğer vergi tanımları');

            // Primary Key
            $table->id('tax_id');

            // Basic Info
            $table->json('title')->comment('Vergi adı ({"tr":"KDV %18","en":"VAT 18%"})');
            $table->string('code')->unique()->comment('Vergi kodu (VAT18)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Tax Rate
            $table->decimal('rate', 5, 2)->comment('Vergi oranı (%) - 18, 8, 1, 0');

            // Tax Type
            $table->enum('tax_type', ['vat', 'sales_tax', 'service_tax', 'excise', 'other'])
                  ->default('vat')
                  ->comment('Vergi tipi: vat=KDV, sales_tax=Satış vergisi, service_tax=Hizmet vergisi, excise=ÖTV, other=Diğer');

            // Application
            $table->enum('applies_to', ['products', 'shipping', 'both'])
                  ->default('products')
                  ->comment('Nerelere uygulanır: products=Ürünler, shipping=Kargo, both=Her ikisi');

            // Geographic Scope
            $table->boolean('is_compound')->default(false)->comment('Bileşik vergi mi? (diğer vergilerin üzerine uygulanır)');
            $table->json('country_codes')->nullable()->comment('Geçerli ülkeler (JSON array - ["TR","DE"])');
            $table->json('excluded_regions')->nullable()->comment('Hariç tutulan bölgeler (JSON array)');

            // Priority (multiple taxes için sıralama)
            $table->integer('priority')->default(0)->comment('Öncelik (düşük değer önce uygulanır)');

            // Status
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

            // Timestamps
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete için silinme tarihi');

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index('code');
            $table->index('tax_type');
            $table->index('is_active');
            $table->index('rate');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_taxes');
    }
};
