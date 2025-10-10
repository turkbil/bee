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
        if (Schema::hasTable('shop_settings')) {
            return;
        }

        Schema::create('shop_settings', function (Blueprint $table) {
            $table->comment('Ayarlar - Sistem ayarları ve konfigürasyonlar (key-value store)');

            // Primary Key
            $table->id('setting_id');

            // Setting Info
            $table->string('group')->comment('Ayar grubu (general, shipping, payment, email, seo)');
            $table->string('key')->comment('Ayar anahtarı (store_name, default_currency)');
            $table->text('value')->nullable()->comment('Ayar değeri');

            // Value Type
            $table->enum('value_type', ['string', 'text', 'integer', 'decimal', 'boolean', 'json', 'array'])
                  ->default('string')
                  ->comment('Değer tipi');

            // Multilingual
            $table->boolean('is_multilingual')->default(false)->comment('Çoklu dil desteği var mı?');
            $table->json('multilingual_value')->nullable()->comment('Çoklu dil değeri (JSON)');

            // Description
            $table->string('label')->nullable()->comment('Ayar etiketi (gösterim için)');
            $table->text('description')->nullable()->comment('Ayar açıklaması');

            // Validation
            $table->string('validation_rules')->nullable()->comment('Validasyon kuralları (required|email|min:3)');
            $table->json('options')->nullable()->comment('Seçenekler (select/radio için - JSON array)');

            // Display
            $table->integer('sort_order')->default(0)->comment('Sıralama düzeni');
            $table->boolean('is_visible')->default(true)->comment('Admin panelde görünsün mü?');
            $table->boolean('is_editable')->default(true)->comment('Düzenlenebilir mi?');

            // Cache
            $table->boolean('is_cached')->default(true)->comment('Cache\'lensin mi?');

            // Timestamps
            $table->timestamps();

            // Indexes

            $table->index('created_at');
            $table->index('updated_at');
            $table->index('group');
            $table->index('key');
            $table->unique(['group', 'key'], 'unique_group_key');
            $table->index('is_visible');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_settings');
    }
};
