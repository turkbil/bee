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
        if (Schema::hasTable('shop_warehouses')) {
            return;
        }

        Schema::create('shop_warehouses', function (Blueprint $table) {
            $table->comment('Depolar - Stok yönetimi için farklı depo/lokasyonlar');

            // Primary Key
            $table->id('warehouse_id');

            // Basic Info
            $table->json('title')->comment('Depo adı ({"tr":"Ana Depo","en":"Main Warehouse"})');
            $table->string('code')->unique()->comment('Depo kodu (WH-001)');
            $table->json('description')->nullable()->comment('Açıklama (JSON çoklu dil)');

            // Warehouse Type
            $table->enum('warehouse_type', ['main', 'branch', 'virtual', 'supplier', 'return', 'damaged'])
                  ->default('main')
                  ->comment('Depo tipi: main=Ana depo, branch=Şube, virtual=Sanal (dropshipping), supplier=Tedarikçi, return=İade, damaged=Hasarlı ürünler');

            // Contact Info
            $table->string('contact_person')->nullable()->comment('Yetkili kişi');
            $table->string('phone')->nullable()->comment('Telefon numarası');
            $table->string('email')->nullable()->comment('E-posta adresi');

            // Address
            $table->text('address_line_1')->nullable()->comment('Adres satırı 1');
            $table->text('address_line_2')->nullable()->comment('Adres satırı 2');
            $table->string('city')->nullable()->comment('İl/Şehir');
            $table->string('postal_code', 10)->nullable()->comment('Posta kodu');
            $table->string('country_code', 2)->default('TR')->comment('Ülke kodu (ISO 3166-1 alpha-2)');

            // Geolocation
            $table->decimal('latitude', 10, 8)->nullable()->comment('Enlem (GPS koordinatı)');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Boylam (GPS koordinatı)');

            // Capacity
            $table->decimal('total_area', 10, 2)->nullable()->comment('Toplam alan (m²)');
            $table->integer('total_capacity')->nullable()->comment('Toplam kapasite (ürün adedi)');
            $table->integer('used_capacity')->default(0)->comment('Kullanılan kapasite');

            // Settings
            $table->boolean('is_active')->default(true)->comment('Aktif/Pasif durumu');
            $table->boolean('is_default')->default(false)->comment('Varsayılan depo mu?');
            $table->boolean('allow_backorders')->default(false)->comment('Ön sipariş kabul eder mi?');
            $table->boolean('allow_shipping')->default(true)->comment('Buradan sevkiyat yapılabilir mi?');
            $table->boolean('allow_pickup')->default(false)->comment('Müşteri teslim alabilir mi?');

            // Operating Hours
            $table->json('operating_hours')->nullable()->comment('Çalışma saatleri (JSON - {"monday":"09:00-18:00"})');

            // Priority
            $table->integer('priority')->default(0)->comment('Öncelik sırası (stok tahsisinde kullanılır)');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');

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
            $table->index('code');
            $table->index('warehouse_type');
            $table->index('is_active');
            $table->index('is_default');
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
        Schema::dropIfExists('shop_warehouses');
    }
};
