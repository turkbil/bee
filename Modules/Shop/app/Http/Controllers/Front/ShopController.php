<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers\Front;

use App\Services\ModuleSlugService;
use App\Services\SeoMetaTagService;
use App\Services\ThemeService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;
use App\Jobs\GenerateProductPlaceholderJob;
use App\Models\ProductChatPlaceholder;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Browsershot\Browsershot;

class ShopController extends Controller
{
    public function __construct(private readonly ThemeService $themeService) {}

    public function index()
    {
        $locale = app()->getLocale();

        // Get all root categories (parent_id = null) for display
        $categories = ShopCategory::query()
            ->whereNull('parent_id')
            ->active()
            ->orderBy('sort_order', 'asc')
            ->get();

        // Build products query
        $productsQuery = ShopProduct::query()
            ->with(['category', 'brand', 'media', 'parentProduct', 'childProducts' => function ($q) {
                $q->active()->published()->orderBy('variant_type')->orderBy('product_id');
            }])
            // TÜM ÜRÜNLER (ana + varyantlar) göster
            ->published()
            ->active();

        // Search functionality
        $searchTerm = request('search');
        if ($searchTerm) {
            $productsQuery->where(function ($query) use ($searchTerm, $locale) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) LIKE ?", ["%{$searchTerm}%"])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(body, '$.\"{$locale}\"')) LIKE ?", ["%{$searchTerm}%"])
                      ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter by category if provided
        $selectedCategoryId = request('category');
        $selectedCategory = null;

        if ($selectedCategoryId) {
            $selectedCategory = ShopCategory::where('category_id', $selectedCategoryId)
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$selectedCategoryId])
                ->first();

            if ($selectedCategory) {
                // Get category and all its children IDs
                $categoryIds = $this->getCategoryWithChildren($selectedCategory);
                $productsQuery->whereIn('category_id', $categoryIds);
            }
        }

        // Özel sıralama: Yedek Parça kategorisi alfabetik, diğerleri normal
        $yedekParcaCategoryId = 7; // Yedek Parça category_id

        if ($selectedCategory && $selectedCategory->category_id === $yedekParcaCategoryId) {
            // Yedek Parça: Alfabetik sıralama
            $products = $productsQuery
                ->with(['category', 'brand', 'media'])
                ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) ASC")
                ->paginate(config('shop.pagination.front_per_shop', 12));
        } else {
            // Diğer kategoriler: Normal sıralama
            $products = $productsQuery
                ->with(['category', 'brand', 'media'])
                ->orderBy('sort_order', 'asc')
                ->orderByDesc('published_at')
                ->paginate(config('shop.pagination.front_per_shop', 12));
        }

        $moduleTitle = __('shop::front.module_title');

        // SEO injection
        view()->share('currentModel', $selectedCategory ?? null);

        try {
            $viewPath = $this->themeService->getThemeViewPath('index', 'shop');

            return view($viewPath, compact('products', 'moduleTitle', 'categories', 'selectedCategory'));
        } catch (\Throwable $e) {
            Log::error('Shop theme index view error', ['message' => $e->getMessage()]);

            return view('shop::front.index', compact('products', 'moduleTitle', 'categories', 'selectedCategory'));
        }
    }

    /**
     * Get category ID and all its children IDs recursively
     */
    private function getCategoryWithChildren(ShopCategory $category): array
    {
        $ids = [$category->category_id];

        $children = ShopCategory::where('parent_id', $category->category_id)
            ->active()
            ->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getCategoryWithChildren($child));
        }

        return $ids;
    }

    /**
     * Show product by ID (fallback for AI old format)
     * Redirects to slug-based URL
     */
    public function showById(int $id)
    {
        $product = ShopProduct::query()
            ->active()
            ->published()
            ->where('product_id', $id)
            ->first();

        if (!$product) {
            abort(404);
        }

        // Redirect to slug-based URL
        $locale = app()->getLocale();
        $slug = $product->getTranslated('slug', $locale) ?? $product->slug[$locale] ?? null;

        if ($slug === null) {
            abort(404);
        }

        $moduleSlug = ModuleSlugService::getSlug('Shop', 'show');
        $defaultLocale = get_tenant_default_locale();

        $slug = ltrim($slug, '/');
        $moduleSlug = trim($moduleSlug, '/');

        if ($locale === $defaultLocale) {
            return redirect('/' . $moduleSlug . '/' . $slug, 301);
        }

        return redirect('/' . $locale . '/' . $moduleSlug . '/' . $slug, 301);
    }

    public function show(string $slug)
    {
        $locale = app()->getLocale();

        $product = ShopProduct::query()
            ->with(['category', 'brand', 'seoSetting'])
            ->active()
            ->published()
            ->where(function ($query) use ($slug, $locale) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug]);
            })
            ->first();

        if (!$product) {
            abort(404);
        }

        // SEO servisi inject et
        $seoService = app(SeoMetaTagService::class);

        // Handle variant logic
        $parentProduct = null;
        $siblingVariants = collect();
        $isVariantPage = false;

        if ($product->isVariant()) {
            // This is a variant, load parent and siblings
            $parentProduct = $product->parentProduct;
            $isVariantPage = true;

            if ($parentProduct) {
                // ✅ CONTENT INHERITANCE: Varyant içeriği yoksa parent'tan inherit et
                $fieldsToInherit = [
                    'body',
                    'features',
                    'faq_data',
                    'use_cases',
                    'competitive_advantages',
                    'target_industries',
                    'warranty_info',
                    'accessories',
                    'certifications',
                    'technical_specs',
                    'primary_specs',
                    'highlighted_features',
                ];

                foreach ($fieldsToInherit as $field) {
                    if (empty($product->{$field}) && !empty($parentProduct->{$field})) {
                        $product->{$field} = $parentProduct->{$field};
                    }
                }

                // 📸 MEDIA INHERITANCE: Varyantın kendi fotoğrafı yoksa parent'ın fotoğraflarını kullan
                if ($product->getMedia('featured_image')->isEmpty() && $parentProduct->hasMedia('featured_image')) {
                    // Parent'ın featured_image'ını varyanta kopyala (geçici olarak, DB'ye kaydetmiyoruz)
                    $product->setRelation('media', $parentProduct->getMedia('featured_image')->merge($product->getMedia('gallery')));
                }

                if ($product->getMedia('gallery')->isEmpty() && $parentProduct->hasMedia('gallery')) {
                    // Parent'ın gallery'sini varyanta kopyala
                    $product->setRelation('media', $product->getMedia('featured_image')->merge($parentProduct->getMedia('gallery')));
                }

                $siblingVariants = $parentProduct->childProducts()
                    ->where('product_id', '!=', $product->product_id)
                    ->active()
                    ->published()
                    ->get();
            }
        } else {
            // This is a regular product or master product, load child variants
            $siblingVariants = $product->childProducts()
                ->active()
                ->published()
                ->get();
        }

        // ⚠️ ÖNEMLİ: SeoMetaTagService'in model'i algılayabilmesi için ÖNCE share et
        view()->share('currentModel', $product);

        // 🔄 PLACEHOLDER QUEUE: Generate AI placeholder in background if not exists
        // - İlk ziyaretçi: Fallback görür, queue işler
        // - Sonraki ziyaretçiler: Gerçek AI conversation görür
        try {
            $placeholderExists = ProductChatPlaceholder::where('product_id', $product->product_id)->exists();

            if (!$placeholderExists) {
                // Queue'ya job at (non-blocking, arka planda çalışır)
                GenerateProductPlaceholderJob::dispatch((string) $product->product_id);

                Log::info('🔄 Placeholder job dispatched', [
                    'product_id' => $product->product_id,
                    'product_title' => $product->title,
                ]);
            }
        } catch (\Exception $e) {
            // Silent fail - placeholder generation hatası sayfayı bozmamalı
            Log::warning('⚠️ Placeholder queue dispatch failed', [
                'product_id' => $product->product_id,
                'error' => $e->getMessage(),
            ]);
        }

        // Shop modülü özel: Çoklu schema desteği (Product + Breadcrumb + FAQ)
        $metaTags = null;
        if ($seoService && method_exists($product, 'getAllSchemas')) {
            // Shop product için özel schema'ları ekle
            $shopSchemas = $product->getAllSchemas();

            // SEO servisine schema'ları ekle (mevcut schemas array'ine merge et)
            $currentMetaTags = $seoService->generateMetaTags();
            $currentSchemas = $currentMetaTags['schemas'] ?? [];

            // Shop schema'larını ekle (Product, Breadcrumb, FAQ)
            foreach ($shopSchemas as $key => $schema) {
                if ($schema) {
                    // Shop modülü schema'ları `shop_` prefix'i ile ekle
                    $currentSchemas['shop_' . $key] = $schema;
                }
            }

            // Meta tags'i güncelle
            $metaTags = array_merge($currentMetaTags, ['schemas' => $currentSchemas]);
            view()->share('metaTags', $metaTags);

            \Log::info('ShopController - Shop schemas added', [
                'total_schemas' => count($currentSchemas),
                'shop_schemas' => array_keys($shopSchemas),
                'all_schema_keys' => array_keys($currentSchemas),
            ]);
        }

        try {
            // ✅ Varyant ise show-variant, değilse show render et
            $viewName = $isVariantPage ? 'show-variant' : 'show';
            $viewPath = $this->themeService->getThemeViewPath($viewName, 'shop');

            $viewData = [
                'item' => $product,
                'parentProduct' => $parentProduct,
                'siblingVariants' => $siblingVariants,
                'isVariantPage' => $isVariantPage,
            ];

            // metaTags varsa view'a ekle
            if ($metaTags) {
                $viewData['metaTags'] = $metaTags;
            }

            return view($viewPath, $viewData);
        } catch (\Throwable $e) {
            Log::error('Shop theme show view error', ['message' => $e->getMessage()]);

            // Fallback: varyant ise show-variant, değilse show
            $fallbackView = $isVariantPage ? 'shop::front.show-variant' : 'shop::front.show';

            $fallbackData = [
                'item' => $product,
                'parentProduct' => $parentProduct,
                'siblingVariants' => $siblingVariants,
                'isVariantPage' => $isVariantPage,
            ];

            // metaTags varsa view'a ekle
            if ($metaTags) {
                $fallbackData['metaTags'] = $metaTags;
            }

            return view($fallbackView, $fallbackData);
        }
    }

    /**
     * Show category by ID (fallback for AI ID-based links)
     * Redirects to slug-based URL
     */
    public function categoryById(int $id)
    {
        $category = ShopCategory::query()
            ->where('category_id', $id)
            ->first();

        if (!$category) {
            abort(404);
        }

        // Redirect to slug-based URL
        $locale = app()->getLocale();
        $slug = $category->getTranslated('slug', $locale) ?? $category->slug[$locale] ?? null;

        if ($slug === null) {
            abort(404);
        }

        $defaultLocale = get_tenant_default_locale();
        $slug = ltrim($slug, '/');

        if ($locale === $defaultLocale) {
            return redirect('/shop/category/' . $slug, 301);
        }

        return redirect('/' . $locale . '/shop/category/' . $slug, 301);
    }

    public function category(string $slug)
    {
        $locale = app()->getLocale();

        $category = ShopCategory::query()
            ->with(['children' => fn($query) => $query->active()->orderBy('sort_order')])
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug])
            ->firstOrFail();

        // Get all category IDs (this category + all children recursively)
        $categoryIds = $this->getCategoryWithChildren($category);

        // Get products from this category and all subcategories
        $yedekParcaCategoryId = 7;

        if ($category->category_id === $yedekParcaCategoryId || in_array($yedekParcaCategoryId, $categoryIds)) {
            // Yedek Parça veya alt kategorisi: Alfabetik sıralama
            $products = ShopProduct::query()
                ->with(['category', 'brand', 'media'])
                ->whereIn('category_id', $categoryIds)
                ->active()
                ->published()
                ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) ASC")
                ->paginate(config('shop.pagination.front_per_shop', 12));
        } else {
            // Diğer kategoriler: Normal sıralama
            $products = ShopProduct::query()
                ->with(['category', 'brand', 'media'])
                ->whereIn('category_id', $categoryIds)
                ->active()
                ->published()
                ->orderBy('sort_order', 'asc')
                ->orderByDesc('published_at')
                ->paginate(config('shop.pagination.front_per_shop', 12));
        }

        // Get direct subcategories for display
        $subcategories = $category->children;

        // SEO injection
        view()->share('currentModel', $category);

        try {
            $viewPath = $this->themeService->getThemeViewPath('category', 'shop');
            return view($viewPath, compact('category', 'products', 'subcategories'));
        } catch (\Throwable $e) {
            Log::error('Shop theme category view error', ['message' => $e->getMessage()]);
            return view('shop::front.category', compact('category', 'products', 'subcategories'));
        }
    }

    public function brand(string $slug)
    {
        $locale = app()->getLocale();

        $brand = ShopBrand::query()
            ->with(['products' => fn($query) => $query->active()->published()])
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug])
            ->firstOrFail();

        $products = $brand->products()->active()->published()->paginate(12);

        try {
            $viewPath = $this->themeService->getThemeViewPath('brand', 'shop');
            return view($viewPath, compact('brand', 'products'));
        } catch (\Throwable $e) {
            Log::error('Shop theme brand view error', ['message' => $e->getMessage()]);
            return view('shop::front.brand', compact('brand', 'products'));
        }
    }

    public static function resolveProductUrl(ShopProduct $product, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $product->getTranslated('slug', $locale) ?? $product->slug[$locale] ?? null;

        if ($slug === null) {
            return url('/');
        }

        $moduleSlug = ModuleSlugService::getSlug('Shop', 'show');
        $defaultLocale = get_tenant_default_locale();

        // FIX: Ensure proper URL separation with explicit concatenation
        // Prevents AI chatbot link generation issues where /shop/slug becomes /shopslug
        $slug = ltrim($slug, '/');
        $moduleSlug = trim($moduleSlug, '/');

        if ($locale === $defaultLocale) {
            return url('/' . $moduleSlug . '/' . $slug);
        }

        return url('/' . $locale . '/' . $moduleSlug . '/' . $slug);
    }

    // VERSION-SPECIFIC SHOW METHODS
    private function showVersion(string $slug, string $version)
    {
        $locale = app()->getLocale();

        $product = ShopProduct::query()
            ->with(['category', 'brand', 'seoSetting'])
            ->active()
            ->published()
            ->where(function ($query) use ($slug, $locale) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug]);
            })
            ->first();

        if (!$product) {
            abort(404);
        }

        // SEO servisi inject et
        $seoService = app(SeoMetaTagService::class);

        $parentProduct = null;
        $siblingVariants = collect();
        $isVariantPage = false;

        if ($product->isVariant()) {
            $parentProduct = $product->parentProduct;
            $isVariantPage = true;

            if ($parentProduct) {
                $fieldsToInherit = ['body', 'features', 'faq_data', 'use_cases', 'competitive_advantages', 'target_industries', 'warranty_info', 'accessories', 'certifications', 'technical_specs', 'primary_specs', 'highlighted_features'];
                foreach ($fieldsToInherit as $field) {
                    if (empty($product->{$field}) && !empty($parentProduct->{$field})) {
                        $product->{$field} = $parentProduct->{$field};
                    }
                }
                if ($product->getMedia('featured_image')->isEmpty() && $parentProduct->hasMedia('featured_image')) {
                    $product->setRelation('media', $parentProduct->getMedia('featured_image')->merge($product->getMedia('gallery')));
                }
                if ($product->getMedia('gallery')->isEmpty() && $parentProduct->hasMedia('gallery')) {
                    $product->setRelation('media', $product->getMedia('featured_image')->merge($parentProduct->getMedia('gallery')));
                }
                $siblingVariants = $parentProduct->childProducts()->where('product_id', '!=', $product->product_id)->active()->published()->get();
            }
        } else {
            $siblingVariants = $product->childProducts()->active()->published()->get();
        }

        // ⚠️ ÖNEMLİ: SeoMetaTagService'in model'i algılayabilmesi için ÖNCE share et
        view()->share('currentModel', $product);

        // 🔄 PLACEHOLDER QUEUE: Generate AI placeholder in background if not exists
        // - İlk ziyaretçi: Fallback görür, queue işler
        // - Sonraki ziyaretçiler: Gerçek AI conversation görür
        try {
            $placeholderExists = ProductChatPlaceholder::where('product_id', $product->product_id)->exists();

            if (!$placeholderExists) {
                // Queue'ya job at (non-blocking, arka planda çalışır)
                GenerateProductPlaceholderJob::dispatch((string) $product->product_id);

                Log::info('🔄 Placeholder job dispatched', [
                    'product_id' => $product->product_id,
                    'product_title' => $product->title,
                ]);
            }
        } catch (\Exception $e) {
            // Silent fail - placeholder generation hatası sayfayı bozmamalı
            Log::warning('⚠️ Placeholder queue dispatch failed', [
                'product_id' => $product->product_id,
                'error' => $e->getMessage(),
            ]);
        }

        // Shop modülü özel: Çoklu schema desteği (Product + Breadcrumb + FAQ)
        $metaTags = null;
        if ($seoService && method_exists($product, 'getAllSchemas')) {
            // Shop product için özel schema'ları ekle
            $shopSchemas = $product->getAllSchemas();

            // SEO servisine schema'ları ekle (mevcut schemas array'ine merge et)
            $currentMetaTags = $seoService->generateMetaTags();
            $currentSchemas = $currentMetaTags['schemas'] ?? [];

            // Shop schema'larını ekle (Product, Breadcrumb, FAQ)
            foreach ($shopSchemas as $key => $schema) {
                if ($schema) {
                    // Shop modülü schema'ları `shop_` prefix'i ile ekle
                    $currentSchemas['shop_' . $key] = $schema;
                }
            }

            // Meta tags'i güncelle
            $metaTags = array_merge($currentMetaTags, ['schemas' => $currentSchemas]);
            view()->share('metaTags', $metaTags);

            \Log::info('ShopController - Shop schemas added', [
                'total_schemas' => count($currentSchemas),
                'shop_schemas' => array_keys($shopSchemas),
                'all_schema_keys' => array_keys($currentSchemas),
            ]);
        }

        // Dinamik tema ile view path oluştur
        $viewName = $isVariantPage ? "show-variant-{$version}" : "show-{$version}";

        try {
            $viewPath = $this->themeService->getThemeViewPath($viewName, 'shop');
        } catch (\Throwable $e) {
            // Fallback: simple tema kullan
            $defaultTheme = config('studio.themes.default', 'simple');
            $viewPath = "shop::themes.{$defaultTheme}.{$viewName}";

            Log::warning('Shop versioned view fallback', [
                'version' => $version,
                'view_name' => $viewName,
                'fallback_theme' => $defaultTheme,
                'error' => $e->getMessage()
            ]);
        }

        $viewData = [
            'item' => $product,
            'parentProduct' => $parentProduct,
            'siblingVariants' => $siblingVariants,
            'isVariantPage' => $isVariantPage,
        ];

        // metaTags varsa view'a ekle
        if ($metaTags) {
            $viewData['metaTags'] = $metaTags;
        }

        return view($viewPath, $viewData);
    }

    public function showV1(string $slug)
    {
        return $this->showVersion($slug, 'v1');
    }

    public function showV2(string $slug)
    {
        return $this->showVersion($slug, 'v2');
    }

    public function showV3(string $slug)
    {
        return $this->showVersion($slug, 'v3');
    }

    public function showV4(string $slug)
    {
        return $this->showVersion($slug, 'v4');
    }

    public function showV5(string $slug)
    {
        return $this->showVersion($slug, 'v5');
    }

    public function showV6(string $slug)
    {
        return $this->showVersion($slug, 'v6');
    }

    /**
     * Export product info as PDF with first/last pages using Browsershot
     */
    public function exportPdf(string $slug)
    {
        $locale = app()->getLocale();

        $product = ShopProduct::query()
            ->with(['category', 'brand'])
            ->active()
            ->published()
            ->where(function ($query) use ($slug, $locale) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug]);
            })
            ->first();

        if (!$product) {
            abort(404);
        }

        $title = $product->getTranslated('title', $locale);
        $productUrl = self::resolveProductUrl($product, $locale);
        $filename = \Str::slug($title) . '-urun-katalogu.pdf';
        $pdfPath = storage_path('app/temp/' . $filename);
        $htmlPath = storage_path('app/temp/' . \Str::slug($title) . '-combined.html');

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        try {
            // Get current date in Turkish
            $catalogDate = now()->locale('tr')->isoFormat('D MMMM YYYY');

            // Logo URL - Use tenant storage or public fallback
            $logoPath = storage_path('app/public/settings/ixtif-logo-white.png');
            if (file_exists($logoPath)) {
                $logoUrl = url('/storage/settings/ixtif-logo-white.png');
            } else {
                $logoUrl = url('/ixtif-designs/assets/logos/ixtif-logo-white.png');
            }

            // Product image URL
            $productImage = null;
            if ($product->hasMedia('featured_image')) {
                $productImage = $product->getFirstMediaUrl('featured_image');
            }

            // Render first page body content
            $firstPageBodyHtml = view('shop::themes.ixtif.pdf.first-page', [
                'productTitle' => $title,
                'logoUrl' => $logoUrl,
                'catalogDate' => $catalogDate,
                'productImage' => $productImage,
            ])->render();

            // Extract body content only
            preg_match('/<body[^>]*>(.*?)<\/body>/is', $firstPageBodyHtml, $firstMatches);
            $firstPageBody = $firstMatches[1] ?? '';

            // Render last page body content
            $lastPageBodyHtml = view('shop::themes.ixtif.pdf.last-page', [
                'logoUrl' => $logoUrl,
                'catalogDate' => $catalogDate,
            ])->render();

            // Extract body content only
            preg_match('/<body[^>]*>(.*?)<\/body>/is', $lastPageBodyHtml, $lastMatches);
            $lastPageBody = $lastMatches[1] ?? '';

            // Fetch product content page via HTTP (with SSL verification disabled for local dev)
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            $productPageHtml = file_get_contents($productUrl, false, $context);

            // CSS to inject for product page cleanup
            $productPageCss = '
                /* ========== HIDE HEADER & FOOTER ========== */
                body > header,
                body > nav,
                body > footer,
                header[class*="bg-"],
                footer[class*="bg-"],
                .site-header,
                .site-footer,
                [id*="header"],
                [id*="Header"],
                [class*="header-"],
                [class*="footer-"] {
                    display: none !important;
                }

                /* Hide TOC bar */
                #toc-bar { display: none !important; }

                /* ========== HIDE RIGHT SIDEBAR ========== */
                #sticky-sidebar,
                aside,
                [class*="sidebar"],
                .lg\:col-span-1 {
                    display: none !important;
                }

                /* Make left content full width */
                .lg\:col-span-2 {
                    grid-column: span 3 / span 3 !important;
                    max-width: 100% !important;
                    width: 100% !important;
                }

                /* Force single column layout */
                .grid.grid-cols-1.lg\:grid-cols-3 {
                    grid-template-columns: 1fr !important;
                }

                /* ========== HIDE CONTACT FORM ========== */
                #contact,
                #trust-signals,
                [id*="contact-form"],
                form[action*="contact"] {
                    display: none !important;
                }

                /* Hide Hero CTA Buttons */
                #hero-section .flex.flex-col.sm\:flex-row,
                #hero-section a[href="#contact"],
                #hero-section a[href^="tel:"] {
                    display: none !important;
                }

                /* ========== OPEN ALL ACCORDIONS (FAQ) ========== */
                #faq [x-show],
                [x-show][class*="faq"],
                .accordion-content {
                    display: block !important;
                    opacity: 1 !important;
                    max-height: none !important;
                    height: auto !important;
                    visibility: visible !important;
                }

                #faq .fa-chevron-down,
                #faq .fa-chevron-up {
                    display: none !important;
                }

                /* Force accordion buttons to show content */
                [x-cloak] {
                    display: block !important;
                }

                /* ========== SHOW ALL ICONS (Font Awesome fix) ========== */
                .fa, .fas, .far, .fal, .fab, i[class*="fa-"] {
                    font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome" !important;
                    font-weight: 900 !important;
                    display: inline-block !important;
                    font-style: normal !important;
                    font-variant: normal !important;
                    text-rendering: auto !important;
                    -webkit-font-smoothing: antialiased !important;
                }

                /* ========== SEKTÖRLER 3 COLUMN GRID ========== */
                #industries .grid,
                #target-industries .grid,
                [id*="industr"] .grid {
                    grid-template-columns: repeat(3, 1fr) !important;
                    display: grid !important;
                }

                /* ========== HIDE FLOATING WIDGETS ========== */
                .fixed,
                [class*="fixed"],
                [style*="position: fixed"] {
                    display: none !important;
                }

                .sticky {
                    position: relative !important;
                }

                /* ========== DISABLE ALL LINKS ========== */
                a {
                    pointer-events: none !important;
                    text-decoration: none !important;
                    cursor: default !important;
                }

                /* ========== PAGE BREAK CONTROL ========== */
                section {
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }

                /* ========== PRINT OPTIMIZATION ========== */
                body {
                    background: white !important;
                }

                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }
            ';

            // Inject CSS into product page HTML
            $productPageHtml = str_replace(
                '</head>',
                '<style>' . $productPageCss . '</style></head>',
                $productPageHtml
            );

            // Extract body content from product page
            preg_match('/<body[^>]*>(.*?)<\/body>/is', $productPageHtml, $productMatches);
            $productBodyContent = $productMatches[1] ?? '';

            // Combine all 3 sections with page breaks - single HTML document
            $combinedHtml = <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - İXTİF Ürün Kataloğu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        .pdf-page {
            page-break-after: always;
            page-break-inside: avoid;
            min-height: 297mm;
            width: 210mm;
            position: relative;
        }

        .pdf-page:last-child {
            page-break-after: auto;
        }

        body {
            margin: 0;
            padding: 0;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    </style>
</head>
<body>

<!-- FIRST PAGE -->
<div class="pdf-page">
{$firstPageBody}
</div>

<!-- PRODUCT CONTENT PAGES -->
<div style="padding: 10mm;">
{$productBodyContent}
</div>

<!-- LAST PAGE -->
<div class="pdf-page">
{$lastPageBody}
</div>

</body>
</html>
HTML;

            // Write combined HTML to temp file (for debugging if needed)
            file_put_contents($htmlPath, $combinedHtml);

            // Generate PDF from combined HTML
            Browsershot::html($combinedHtml)
                ->setNodeBinary('/opt/homebrew/bin/node')
                ->setNpmBinary('/opt/homebrew/bin/npm')
                ->addChromiumArguments(['no-sandbox', 'disable-setuid-sandbox'])
                ->waitUntilNetworkIdle()
                ->showBackground()
                ->format('A4')
                ->margins(0, 0, 0, 0)
                ->windowSize(794, 1123)  // A4 at 96 DPI
                ->save($pdfPath);

            // Clean up temp HTML file
            if (file_exists($htmlPath)) {
                unlink($htmlPath);
            }

            return response()->download($pdfPath, $filename, [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('PDF generation failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->product_id,
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'PDF oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
