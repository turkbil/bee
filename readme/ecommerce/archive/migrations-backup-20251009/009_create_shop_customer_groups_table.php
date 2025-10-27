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
        if (Schema::hasTable('shop_customer_groups')) {
            return;
        }

        Schema::create('shop_customer_groups', function (Blueprint $table) {
            // Primary Key
            $table->id('customer_group_id');

            // Basic Info - JSON çoklu dil
            $table->json('title')->comment('Grup adı: {"tr": "Grup Adı", "en": "Group Name", "vs.": "..."}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "grup-adi", "en": "group-name", "vs.": "..."}');
            $table->json('description')->nullable()->comment('Grup açıklaması: {"tr": "Açıklama", "en": "Description", "vs.": "..."}');

            // Discount Settings
            $table->decimal('discount_percentage', 5, 2)->default(0)->index()->comment('İndirim yüzdesi (%)');
            $table->boolean('has_special_pricing')->default(false)->comment('Özel fiyatlandırma var mı?');
            $table->boolean('price_on_request_only')->default(false)->comment('Sadece fiyat teklifi mi? (normal fiyat görmesin)');

            // Privileges
            $table->boolean('can_see_stock')->default(true)->comment('Stok bilgisini görebilir mi?');
            $table->boolean('can_request_quote')->default(false)->comment('Teklif isteyebilir mi?');
            $table->boolean('can_purchase_on_credit')->default(false)->comment('Vadeli alışveriş yapabilir mi?');
            $table->integer('credit_limit')->default(0)->comment('Kredi limiti (₺)');
            $table->integer('payment_term_days')->default(0)->comment('Ödeme vadesi (gün)');

            // Tax Settings
            $table->boolean('tax_exempt')->default(false)->comment('Vergiden muaf mı?');
            $table->string('tax_exempt_number')->nullable()->comment('Vergi muafiyet belge numarası');

            // Shipping
            $table->boolean('free_shipping')->default(false)->comment('Ücretsiz kargo var mı?');
            $table->decimal('free_shipping_threshold', 12, 2)->nullable()->comment('Ücretsiz kargo minimum tutar (₺)');

            // Loyalty
            $table->decimal('loyalty_points_multiplier', 5, 2)->default(1)->comment('Sadakat puanı çarpanı (1.5 = %50 fazla puan)');

            // Access Control
            $table->boolean('requires_approval')->default(false)->comment('Gruba katılım onay gerektirir mi?');
            $table->boolean('is_default')->default(false)->index()->comment('Varsayılan grup mu? (yeni müşteriler)');
            $table->boolean('is_active')->default(true)->index()->comment('Aktif/Pasif durumu');

            // Display
            $table->integer('sort_order')->default(0)->index()->comment('Sıralama düzeni');
            $table->string('color_code', 7)->nullable()->comment('Renk kodu (#FF5733)');

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
                $supportsJsonIndex = $mariaVersion >= 10.5;
            } else {
                $majorVersion = (int) explode('.', $version)[0];
                $supportsJsonIndex = $majorVersion >= 8;
            }

            if ($supportsJsonIndex) {
                $systemLanguages = config('modules.system_languages', ['tr', 'en']);

                foreach ($systemLanguages as $locale) {
                    DB::statement("
                        ALTER TABLE shop_customer_groups
                        ADD INDEX shop_customer_groups_slug_{$locale} (
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
        Schema::dropIfExists('shop_customer_groups');
    }
};
