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
            ->with(['category', 'brand', 'media', 'currency', 'parentProduct', 'childProducts' => function ($q) {
                $q->with('currency')->active()->published()->orderBy('variant_type')->orderBy('product_id');
            }])
            // SADECE MASTER PRODUCTLAR (varyantlar değil) - Admin panelle tutarlılık
            ->whereNull('parent_product_id')
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
                ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) ASC")
                ->paginate(config('shop.pagination.front_per_shop', 12));
        } else {
            // Shop Index Sıralaması:
            // 1. show_on_homepage (vitrin ürünleri önce)
            // 2. homepage_sort_order (anasayfa özel sıralama, null'lar en sonda)
            // 3. category.sort_order (kategori sıralaması)
            // 4. product.sort_order (ürün sıralaması)
            $products = $productsQuery
                ->leftJoin('shop_categories', 'shop_products.category_id', '=', 'shop_categories.category_id')
                ->select('shop_products.*')
                ->orderByDesc('shop_products.show_on_homepage')
                ->orderByRaw('COALESCE(shop_products.homepage_sort_order, 999999) ASC')
                ->orderBy('shop_categories.sort_order', 'asc')
                ->orderBy('shop_products.sort_order', 'asc')
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

        // ⚡ PLACEHOLDER: Frontend'de async olarak kontrol edilir (sayfa hızı için)
        // Controller'da DB query yok = Sayfa anında yüklenir

        // Shop modülü özel: Çoklu schema desteği (Product + Breadcrumb + FAQ)
        $metaTags = null;
        if ($seoService && method_exists($product, 'getAllSchemas')) {
            // Shop product için özel schema'ları al
            $shopSchemas = $product->getAllSchemas();

            // SEO servisinden mevcut meta tags'i al
            $currentMetaTags = $seoService->generateMetaTags();
            $currentSchemas = $currentMetaTags['schemas'] ?? [];

            // ⚠️ DUPLICATE PREVENTION: Shop modülü kendi Product/Breadcrumb schema'sını üretiyor
            // SeoMetaTagService'in ürettiği 'main' (Product) ve 'breadcrumb' schema'larını kaldır
            // Çünkü ShopProduct::getAllSchemas() daha detaylı ve offers içeren schema üretiyor
            unset($currentSchemas['main']);      // Generic Product schema - kaldır
            unset($currentSchemas['breadcrumb']); // Generic Breadcrumb - kaldır

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

        // Get sort parameter
        $sortParam = request('sort');

        // Get products from this category and all subcategories
        $yedekParcaCategoryId = 7;

        $productsQuery = ShopProduct::query()
            ->with(['category', 'brand', 'media'])
            ->whereNull('parent_product_id') // Sadece master productlar (varyantlar değil)
            ->whereIn('category_id', $categoryIds)
            ->active()
            ->published();

        // Apply sorting based on sort parameter or category rules
        if ($sortParam === 'a-z') {
            // A'dan Z'ye alfabetik sıralama
            $productsQuery->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) ASC");
        } elseif ($sortParam === 'z-a') {
            // Z'den A'ya ters alfabetik sıralama
            $productsQuery->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) DESC");
        } elseif ($category->category_id === $yedekParcaCategoryId || in_array($yedekParcaCategoryId, $categoryIds)) {
            // Yedek Parça veya alt kategorisi: Alfabetik sıralama (varsayılan)
            $productsQuery->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr')) ASC");
        } else {
            // Varsayılan sıralama: sort_order + product_id
            $productsQuery->orderBy('sort_order', 'asc')->orderBy('product_id', 'asc');
        }

        $products = $productsQuery->paginate(config('shop.pagination.front_per_shop', 12));

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

        // ⚡ PLACEHOLDER: Frontend'de async olarak kontrol edilir (sayfa hızı için)
        // Controller'da DB query yok = Sayfa anında yüklenir

        // Shop modülü özel: Çoklu schema desteği (Product + Breadcrumb + FAQ)
        $metaTags = null;
        if ($seoService && method_exists($product, 'getAllSchemas')) {
            // Shop product için özel schema'ları al
            $shopSchemas = $product->getAllSchemas();

            // SEO servisinden mevcut meta tags'i al
            $currentMetaTags = $seoService->generateMetaTags();
            $currentSchemas = $currentMetaTags['schemas'] ?? [];

            // ⚠️ DUPLICATE PREVENTION: Shop modülü kendi Product/Breadcrumb schema'sını üretiyor
            // SeoMetaTagService'in ürettiği 'main' (Product) ve 'breadcrumb' schema'larını kaldır
            // Çünkü ShopProduct::getAllSchemas() daha detaylı ve offers içeren schema üretiyor
            unset($currentSchemas['main']);      // Generic Product schema - kaldır
            unset($currentSchemas['breadcrumb']); // Generic Breadcrumb - kaldır

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
     * Export product info as PDF using Browsershot (Chrome)
     * Captures actual webpage screenshot for professional PDF
     */
    public function exportPdf(string $slug)
    {
        $locale = app()->getLocale();

        $product = ShopProduct::query()
            ->with(['category', 'brand', 'media'])
            ->active()
            ->published()
            ->where('slug->' . $locale, $slug)
            ->first();

        if (!$product) {
            \Log::error('PDF Export: Product not found', ['slug' => $slug]);
            abort(404);
        }

        \Log::info('PDF Export: Product found', ['product_id' => $product->product_id, 'slug' => $slug]);

        // Increase PHP execution time for PDF generation
        set_time_limit(120);

        try {
            $title = $product->getTranslated('title', $locale);
            $filename = \Str::slug($title) . '-urun-katalogu.pdf';

            // Get product page URL
            $productUrl = self::resolveProductUrl($product, $locale);

            \Log::info('PDF Export: Starting', ['url' => $productUrl, 'product_id' => $product->product_id]);

            // Generate PDF by capturing the actual product page URL directly
            $pdfPath = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';

            // CSS to hide unwanted elements in PDF
            $hideElementsCSS = '
                header, .header, #header,
                footer, .footer, #footer,
                nav, .navbar, .navigation,
                .sidebar, #sidebar, aside,
                .breadcrumb, .breadcrumbs,
                .floating-widget, .ai-chat-widget,
                .cart-widget, .mini-cart,
                .sticky-bar, .sticky-header,
                .cookie-notice, .cookie-banner,
                .back-to-top,
                .whatsapp-button, .whatsapp-widget,
                .pdf-download-button, .export-pdf,
                #nprogress,
                [data-hide-in-pdf="true"] {
                    display: none !important;
                }
                body {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            ';

            Browsershot::url($productUrl)
                ->setNodeBinary('/usr/bin/node')
                ->setNpmBinary('/usr/bin/npm')
                ->setChromePath('/var/www/vhosts/tuufi.com/.puppeteer-cache/chrome/linux-141.0.7390.78/chrome-linux64/chrome')
                ->addChromiumArguments([
                    'no-sandbox',
                    'disable-setuid-sandbox',
                    'disable-dev-shm-usage',
                    'disable-gpu',
                ])
                ->setOption('addStyleTag', json_encode(['content' => $hideElementsCSS]))
                ->showBackground()
                ->emulateMedia('screen')
                ->paperSize(210, 5000)  // A4 width (210mm), very tall height (5000mm)
                ->margins(10, 10, 10, 10)
                ->timeout(90000)
                ->setDelay(2000)
                ->save($pdfPath);

            // Check if PDF was created
            if (!file_exists($pdfPath) || filesize($pdfPath) < 1000) {
                throw new \Exception('PDF dosyası oluşturulamadı');
            }

            \Log::info('PDF Export: Success', [
                'product_id' => $product->product_id,
                'file_size' => filesize($pdfPath)
            ]);

            // Return PDF response
            $pdfContent = file_get_contents($pdfPath);
            @unlink($pdfPath);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

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
