<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Categories table
        Schema::create('ecommerce_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->json('name'); // {"tr": "Forklift", "en": "Forklift"}
            $table->string('slug', 255);
            $table->json('description')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->string('icon_class', 100)->nullable(); // FontAwesome icon
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('level')->default(1); // 1=main, 2=sub, 3=sub-sub
            $table->string('path', 500)->nullable(); // breadcrumb path: "1/3/5"
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('seo_data')->nullable(); // title, description, keywords per language
            $table->json('metadata')->nullable(); // extra fields, filters, display options
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index(['parent_id', 'sort_order']);
            $table->index('path');
            $table->unique(['slug', 'tenant_id']);
            $table->foreign('parent_id')->references('id')->on('ecommerce_categories')->onDelete('cascade');
        });

        // Brands table
        Schema::create('ecommerce_brands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('parent_brand_id')->nullable(); // sub-brand support
            $table->json('name'); // {"tr": "iXtif", "en": "iXtif"}
            $table->string('slug', 255);
            $table->string('logo_url', 500)->nullable();
            $table->string('banner_url', 500)->nullable();
            $table->json('description')->nullable();
            $table->json('short_description')->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('country_code', 2)->nullable(); // TR, US, DE
            $table->year('founded_year')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('seo_data')->nullable();
            $table->json('contact_info')->nullable(); // phone, email, address per language
            $table->json('social_media')->nullable(); // facebook, twitter, linkedin, youtube
            $table->json('certifications')->nullable(); // ISO, CE, quality certifications
            $table->json('metadata')->nullable(); // extra brand info, categories they serve
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index('parent_brand_id');
            $table->unique(['slug', 'tenant_id']);
            $table->index(['is_featured', 'sort_order']);
            $table->foreign('parent_brand_id')->references('id')->on('ecommerce_brands')->onDelete('cascade');
        });

        // Products table
        Schema::create('ecommerce_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('parent_product_id')->nullable(); // product groups
            $table->string('sku', 255);
            $table->string('model_number', 255)->nullable();
            $table->string('series_name', 255)->nullable();
            $table->json('name'); // {"tr": "...", "en": "..."}
            $table->string('slug', 255);
            $table->json('short_description')->nullable();
            $table->json('long_description')->nullable();
            $table->json('features')->nullable(); // multilingual feature list
            $table->json('technical_specs')->nullable(); // capacity, voltage, etc.
            $table->json('highlighted_features')->nullable(); // starred features: icon, priority, category
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('compare_price', 15, 2)->nullable(); // old/list price
            $table->decimal('cost_price', 15, 2)->nullable(); // cost - admin only
            $table->string('currency', 3)->default('USD');
            $table->boolean('price_on_request')->default(false);
            $table->decimal('weight', 10, 3)->nullable(); // kg
            $table->json('dimensions')->nullable(); // length, width, height, unit
            $table->boolean('stock_tracking')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->integer('max_stock_level')->nullable();
            $table->integer('lead_time_days')->nullable(); // supply time
            $table->integer('warranty_months')->nullable(); // warranty time
            $table->enum('condition', ['new', 'used', 'refurbished'])->default('new');
            $table->enum('availability', ['in_stock', 'out_of_stock', 'on_order', 'discontinued'])->default('in_stock');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_new_arrival')->default(false);
            $table->integer('sort_order')->default(0);
            $table->bigInteger('view_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0); // average rating
            $table->integer('rating_count')->default(0);
            $table->json('tags')->nullable(); // ["electric", "compact", "high-capacity"]
            $table->json('use_cases')->nullable(); // use cases per language
            $table->json('competitive_advantages')->nullable(); // advantages per language
            $table->json('target_industries')->nullable(); // target sectors per language
            $table->json('seo_data')->nullable(); // title, description, keywords per language
            $table->json('faq_data')->nullable(); // frequently asked questions per language
            $table->json('media_gallery')->nullable(); // image, video, pdf ordering
            $table->json('related_products')->nullable(); // related product IDs
            $table->json('cross_sell_products')->nullable(); // cross-sell products
            $table->json('metadata')->nullable(); // extra fields, integrations
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
            $table->index(['brand_id', 'is_active']);
            $table->unique(['sku', 'tenant_id']);
            $table->unique(['slug', 'tenant_id']);
            $table->index(['is_featured', 'sort_order']);
            $table->index(['base_price', 'currency']);
            $table->index(['stock_quantity', 'availability']);
            $table->index('parent_product_id');
            $table->fullText(['technical_specs', 'features', 'tags']);

            $table->foreign('category_id')->references('id')->on('ecommerce_categories');
            $table->foreign('brand_id')->references('id')->on('ecommerce_brands');
            $table->foreign('parent_product_id')->references('id')->on('ecommerce_products')->onDelete('cascade');
        });

        // Product variants table
        Schema::create('ecommerce_product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('sku', 255);
            $table->string('barcode', 255)->nullable();
            $table->json('name'); // {"tr": "CPD15TVL Standard Mast", "en": "..."}
            $table->string('variant_type', 100)->nullable(); // mast_height, battery_type, cabin_type
            $table->json('option_values')->nullable(); // {"mast_height": "3000mm", "battery": "150Ah"}
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('price_modifier', 15, 2)->default(0); // +/- difference from main product
            $table->decimal('compare_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('weight', 10, 3)->nullable();
            $table->json('dimensions')->nullable(); // length, width, height, unit
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // reserved
            $table->integer('min_stock_level')->default(0);
            $table->string('stock_location', 255)->nullable();
            $table->enum('availability', ['in_stock', 'out_of_stock', 'on_order', 'discontinued'])->default('in_stock');
            $table->integer('lead_time_days')->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('variant_data')->nullable(); // variant-specific technical specs
            $table->json('images')->nullable(); // variant-specific images
            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'is_active']);
            $table->unique('sku');
            $table->index(['product_id', 'is_default']);
            $table->index(['stock_quantity', 'availability']);

            $table->foreign('product_id')->references('id')->on('ecommerce_products')->onDelete('cascade');
        });

        // Attributes table
        Schema::create('ecommerce_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->json('name'); // {"tr": "Kapasite", "en": "Capacity"}
            $table->string('slug', 255);
            $table->enum('type', ['text', 'number', 'select', 'multiselect', 'boolean', 'date', 'json'])->default('text');
            $table->string('unit', 50)->nullable(); // kg, mm, volt, etc.
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_comparable')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('options')->nullable(); // for select/multiselect
            $table->json('validation_rules')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_filterable']);
            $table->unique(['slug', 'tenant_id']);
        });

        // Product attributes table
        Schema::create('ecommerce_product_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('attribute_id');
            $table->json('value'); // multilingual value
            $table->decimal('numeric_value', 15, 4)->nullable(); // for numeric filters
            $table->timestamps();

            $table->index(['product_id', 'attribute_id']);
            $table->index(['variant_id', 'attribute_id']);
            $table->index('numeric_value');

            $table->foreign('product_id')->references('id')->on('ecommerce_products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('ecommerce_product_variants')->onDelete('cascade');
            $table->foreign('attribute_id')->references('id')->on('ecommerce_attributes')->onDelete('cascade');
        });

        // Media table
        Schema::create('ecommerce_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('mediable_type', 255); // Product, Category, Brand
            $table->unsignedBigInteger('mediable_id');
            $table->enum('type', ['image', 'video', 'document', 'pdf', '3d_model'])->default('image');
            $table->string('file_name', 500);
            $table->string('file_path', 1000);
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->json('alt_text')->nullable(); // multilingual
            $table->json('title')->nullable(); // multilingual
            $table->json('description')->nullable(); // multilingual
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->json('metadata')->nullable(); // width, height, duration, etc.
            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id', 'type']);
            $table->index(['tenant_id', 'type']);
            $table->index(['is_primary', 'sort_order']);
        });

        // Tenant settings table
        Schema::create('ecommerce_tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->string('default_currency', 3)->default('USD');
            $table->json('supported_currencies')->nullable();
            $table->string('default_language', 5)->default('en');
            $table->json('supported_languages')->nullable();
            $table->json('tax_settings')->nullable();
            $table->json('shipping_settings')->nullable();
            $table->json('payment_settings')->nullable();
            $table->json('catalog_settings')->nullable();
            $table->json('ui_settings')->nullable();
            $table->timestamps();
        });

        // Search indexes table
        Schema::create('ecommerce_search_indexes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('searchable_type', 255);
            $table->unsignedBigInteger('searchable_id');
            $table->string('language', 5);
            $table->text('content');
            $table->text('keywords')->nullable();
            $table->integer('weight')->default(1);
            $table->timestamps();

            $table->index(['tenant_id', 'language', 'searchable_type']);
            $table->fullText(['content', 'keywords']);
        });

        // Product views tracking
        Schema::create('ecommerce_product_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer', 1000)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->timestamp('created_at');

            $table->index(['tenant_id', 'product_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_product_views');
        Schema::dropIfExists('ecommerce_search_indexes');
        Schema::dropIfExists('ecommerce_tenant_settings');
        Schema::dropIfExists('ecommerce_media');
        Schema::dropIfExists('ecommerce_product_attributes');
        Schema::dropIfExists('ecommerce_attributes');
        Schema::dropIfExists('ecommerce_product_variants');
        Schema::dropIfExists('ecommerce_products');
        Schema::dropIfExists('ecommerce_brands');
        Schema::dropIfExists('ecommerce_categories');
    }
};