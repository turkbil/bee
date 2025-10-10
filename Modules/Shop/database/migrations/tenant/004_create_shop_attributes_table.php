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
        if (Schema::hasTable('shop_attributes')) {
            return;
        }

        Schema::create('shop_attributes', function (Blueprint $table) {
            // Primary Key
            $table->id('attribute_id');

            // Basic Info - JSON çoklu dil
            $table->json('title')->comment('Özellik adı: {"tr": "Özellik Adı", "en": "Attribute Name", "vs.": "..."}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "ozellik-adi", "en": "attribute-name", "vs.": "..."}');
            $table->json('description')->nullable()->comment('Özellik açıklaması: {"tr": "Açıklama", "en": "Description", "vs.": "..."}');

            // Attribute Type
            $table->enum('type', ['text', 'select', 'multiselect', 'boolean', 'number', 'range', 'color'])
                  ->default('text')
                  ->index()
                  ->comment('Özellik tipi: text=Metin, select=Seçim, multiselect=Çoklu seçim, boolean=Evet/Hayır, number=Sayı, range=Aralık, color=Renk');

            // Options (for select/multiselect)
            $table->json('options')->nullable()->comment('Seçenek değerleri (select için): [{"value":"value1","label":{"tr":"Etiket","en":"Label","vs.":"..."}}, ...]');

            // Unit
            $table->string('unit')->nullable()->comment('Birim (kg, mm, kW, vb)');

            // Display Options
            $table->boolean('is_filterable')->default(true)->index()->comment('Filtrelemede kullanılsın mı?');
            $table->boolean('is_searchable')->default(false)->index()->comment('Aramada kullanılsın mı?');
            $table->boolean('is_comparable')->default(true)->comment('Karşılaştırmada gösterilsin mi?');
            $table->boolean('is_visible')->default(true)->index()->comment('Ürün detayında gösterilsin mi?');
            $table->boolean('is_required')->default(false)->comment('Zorunlu mu?');

            // Validation
            $table->json('validation_rules')->nullable()->comment('Validasyon kuralları: {"min":0,"max":10000,"vs.":"..."}');

            // Display
            $table->integer('sort_order')->default(0)->index()->comment('Sıralama düzeni');
            $table->string('icon_class')->nullable()->comment('İkon sınıfı');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
        });

        // JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Tablo oluşturulduktan sonra
        if (DB::getDriverName() === 'mysql') {
            $version = DB::selectOne('SELECT VERSION() as version')->version;
            $isMariaDB = stripos($version, 'MariaDB') !== false;

            if ($isMariaDB) {
                preg_match('/(\d+\.\d+)/', $version, $matches);
                $mariaVersion = isset($matches[1]) ? (float) $matches[1] : 0;
                $supportsJsonIndex = false; // Disabled for MariaDB compatibility
            } else {
                $majorVersion = (int) explode('.', $version)[0];
                $supportsJsonIndex = false; // Disabled for MySQL compatibility
            }

            if ($supportsJsonIndex) {
                $systemLanguages = config('modules.system_languages', ['tr', 'en']);

                foreach ($systemLanguages as $locale) {
                    DB::statement("
                        ALTER TABLE shop_attributes
                        ADD INDEX shop_attributes_slug_{$locale} (
                            (CAST(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) AS CHAR(255)) COLLATE utf8mb4_unicode_ci)
                        )
                    ");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_attributes');
    }
};
