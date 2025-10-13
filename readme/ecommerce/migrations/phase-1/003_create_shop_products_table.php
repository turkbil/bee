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
        if (Schema::hasTable('shop_products')) {
            return;
        }

        Schema::create('shop_products', function (Blueprint $table) {
            // Primary Key
            $table->id('product_id');

            // Relations
            $table->unsignedBigInteger('category_id')->comment('Kategori ID - shop_categories ilişkisi');
            $table->unsignedBigInteger('brand_id')->nullable()->comment('Marka ID - shop_brands ilişkisi');

            // Product Identifiers
            $table->string('sku')->unique()->comment('Stok Kodu (Stock Keeping Unit) - Benzersiz');
            $table->string('model_number')->nullable()->comment('Model numarası');
            $table->string('barcode')->nullable()->comment('Barkod numarası');

            // Basic Info - JSON çoklu dil
            $table->json('title')->comment('Ürün başlığı: {"tr": "Ürün Adı", "en": "Product Name", "vs.": "..."}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "urun-adi", "en": "product-name", "vs.": "..."}');
            $table->json('short_description')->nullable()->comment('Kısa açıklama (maksimum 160 karakter): {"tr": "Kısa açıklama", "en": "Short description", "vs.": "..."}');
            $table->json('body')->nullable()->comment('Detaylı açıklama (Rich text HTML): {"tr": "<p>Detaylı açıklama</p>", "en": "<p>Detailed description</p>", "vs.": "..."}');

            // Product Type & Condition
            $table->enum('product_type', ['physical', 'digital', 'service', 'membership', 'bundle'])
                ->default('physical')
                ->comment('Ürün tipi: physical=Fiziksel, digital=Dijital, service=Hizmet, membership=Üyelik, bundle=Paket');

            $table->enum('condition', ['new', 'used', 'refurbished'])
                ->default('new')
                ->comment('Ürün durumu: new=Sıfır, used=İkinci el, refurbished=Yenilenmiş');

            // Pricing
            $table->boolean('price_on_request')->default(false)->index()->comment('Fiyat sorunuz aktif mi? (B2B için)');
            $table->decimal('base_price', 12, 2)->nullable()->comment('Temel fiyat (₺)');
            $table->decimal('compare_at_price', 12, 2)->nullable()->comment('İndirim öncesi fiyat (₺)');
            $table->decimal('cost_price', 12, 2)->nullable()->comment('Maliyet fiyatı (₺) - Kar hesabı için');
            $table->string('currency', 3)->default('TRY')->comment('Para birimi (ISO 4217: TRY, USD, EUR)');

            // Deposit & Installment (B2B)
            $table->boolean('deposit_required')->default(false)->comment('Kapora gerekli mi?');
            $table->decimal('deposit_amount', 12, 2)->nullable()->comment('Sabit kapora tutarı (₺)');
            $table->integer('deposit_percentage')->nullable()->comment('Kapora yüzdesi (%)');
            $table->boolean('installment_available')->default(false)->comment('Taksit yapılabilir mi?');
            $table->integer('max_installments')->nullable()->comment('Maksimum taksit sayısı (9, 12, vb)');

            // Stock Management
            $table->boolean('stock_tracking')->default(true)->comment('Stok takibi yapılsın mı?');
            $table->integer('current_stock')->default(0)->comment('Mevcut stok miktarı');
            $table->integer('low_stock_threshold')->default(5)->comment('Düşük stok uyarı seviyesi');
            $table->boolean('allow_backorder')->default(false)->comment('Stokta yokken sipariş alınabilir mi?');
            $table->integer('lead_time_days')->nullable()->comment('Temin süresi (gün)');

            // Physical Properties
            $table->decimal('weight', 10, 2)->nullable()->comment('Ağırlık (kg)');
            $table->json('dimensions')->nullable()->comment('Boyutlar: {"length":100,"width":50,"height":30,"unit":"cm"}');

            // Technical Specifications
            $table->json('technical_specs')->nullable()->comment('Teknik özellikler (JSON nested object - kapasite, performans, elektrik, vb)');
            $table->json('features')->nullable()->comment('Özellikler listesi (JSON array) - Bullet points');
            $table->json('highlighted_features')->nullable()->comment('Öne çıkan özellikler: [{"icon":"battery","title":"...","description":"..."}, ...]');

            // Media
            $table->json('media_gallery')->nullable()->comment('Medya galerisi: [{"type":"image","url":"...","is_primary":true}, ...]');
            $table->string('video_url')->nullable()->comment('Video URL (YouTube, Vimeo)');
            $table->string('manual_pdf_url')->nullable()->comment('Kullanım kılavuzu PDF URL');

            // NOT: SEO ayarları Universal SEO sistemi üzerinden yönetilir (SeoManagement modülü)

            // Display & Status
            $table->boolean('is_active')->default(true)->index()->comment('Aktif/Pasif durumu');
            $table->boolean('is_featured')->default(false)->index()->comment('Öne çıkan ürün');
            $table->boolean('is_bestseller')->default(false)->index()->comment('Çok satan ürün');
            $table->integer('view_count')->default(0)->comment('Görüntülenme sayısı');
            $table->integer('sales_count')->default(0)->comment('Satış sayısı');
            $table->timestamp('published_at')->nullable()->index()->comment('Yayınlanma tarihi');

            // Additional Data
            $table->json('warranty_info')->nullable()->comment('Garanti bilgisi: {"period":24,"unit":"month","details":"..."}');
            $table->json('shipping_info')->nullable()->comment('Kargo bilgisi: {"weight_limit":50,"size_limit":"large","free_shipping":false}');
            $table->json('tags')->nullable()->comment('Etiketler (JSON array): ["tag1", "tag2", "tag3"]');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('sku');
            $table->index('product_type');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
            $table->index(['category_id', 'is_active'], 'shop_products_cat_active_idx');
            $table->index(['brand_id', 'is_active'], 'shop_products_brand_active_idx');
            $table->index(['is_active', 'deleted_at', 'published_at'], 'shop_products_active_deleted_published_idx');
            $table->index(['is_featured', 'is_active'], 'shop_products_featured_active_idx');
            $table->index(['is_bestseller', 'is_active'], 'shop_products_bestseller_active_idx');

            // Foreign Keys
            $table->foreign('category_id')
                ->references('category_id')
                ->on('shop_categories')
                ->onDelete('restrict');

            $table->foreign('brand_id')
                ->references('brand_id')
                ->on('shop_brands')
                ->onDelete('set null');
        });

        // JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Tablo oluşturulduktan sonra
        // Dinamik olarak system_languages'dan alınır
        if (DB::getDriverName() === 'mysql') {
            $version = DB::selectOne('SELECT VERSION() as version')->version;

            // MySQL 8.0+ veya MariaDB 10.5+ kontrolü
            $isMariaDB = stripos($version, 'MariaDB') !== false;

            if ($isMariaDB) {
                // MariaDB için versiyon kontrolü (10.5+)
                preg_match('/(\d+\.\d+)/', $version, $matches);
                $mariaVersion = isset($matches[1]) ? (float) $matches[1] : 0;
                $supportsJsonIndex = $mariaVersion >= 10.5;
            } else {
                // MySQL için versiyon kontrolü (8.0+)
                $majorVersion = (int) explode('.', $version)[0];
                $supportsJsonIndex = $majorVersion >= 8;
            }

            if ($supportsJsonIndex) {
                // Config'den sistem dillerini al
                $systemLanguages = config('modules.system_languages', ['tr', 'en']);

                foreach ($systemLanguages as $locale) {
                    DB::statement("
                        ALTER TABLE shop_products
                        ADD INDEX shop_products_slug_{$locale} (
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
        Schema::dropIfExists('shop_products');
    }
};
