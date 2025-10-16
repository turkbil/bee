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
        $products = ShopProduct::query()
            ->with(['category', 'brand', 'childProducts' => function ($q) {
                $q->active()->published()->orderBy('variant_type')->orderBy('product_id');
            }])
            ->whereNull('parent_product_id') // Sadece ana ürünler (varyant olmayanlar)
            ->published()
            ->active()
            ->orderByDesc('published_at')
            ->simplePaginate(config('shop.pagination.front_per_shop', 12));

        $moduleTitle = __('shop::front.module_title');

        try {
            $viewPath = $this->themeService->getThemeViewPath('index', 'shop');

            return view($viewPath, compact('products', 'moduleTitle'));
        } catch (\Throwable $e) {
            Log::error('Shop theme index view error', ['message' => $e->getMessage()]);

            return view('shop::front.index', compact('products', 'moduleTitle'));
        }
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
            ->with(['products' => fn($query) => $query->active()->published()])
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug])
            ->firstOrFail();

        $products = $category->products()->active()->published()->paginate(12);

        try {
            $viewPath = $this->themeService->getThemeViewPath('category', 'shop');
            return view($viewPath, compact('category', 'products'));
        } catch (\Throwable $e) {
            Log::error('Shop theme category view error', ['message' => $e->getMessage()]);
            return view('shop::front.category', compact('category', 'products'));
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
     * Export product info as PDF using Browsershot (renders actual web page)
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

        // Build product URL
        $productUrl = self::resolveProductUrl($product, $locale);

        // Generate filename
        $filename = \Str::slug($title) . '-urun-katalogu.pdf';
        $pdfPath = storage_path('app/temp/' . $filename);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        try {
            // Use Browsershot to render the actual web page as PDF
            Browsershot::url($productUrl)
                ->setNodeBinary('/opt/homebrew/bin/node')
                ->setNpmBinary('/opt/homebrew/bin/npm')
                ->addChromiumArguments([
                    'no-sandbox',
                    'disable-setuid-sandbox',
                ])
                ->waitUntilNetworkIdle()
                ->showBackground()
                ->format('A4')            // Standard A4 paper size
                ->margins(10, 10, 10, 10) // 10mm margins for print-friendly
                ->windowSize(1200, 1600)  // Modern viewport width (not too narrow)
                ->pages('1-100')          // Capture all pages
                // Hide sidebar and make content full width with custom CSS
                ->setOption('addStyleTag', json_encode([
                    'content' => '
                        /* ========================================
                           PDF EXPORT - CUSTOM CSS RULES
                           ======================================== */

                        /* Hide right sidebar */
                        #sticky-sidebar { display: none !important; }
                        .lg\:col-span-1 { display: none !important; }

                        /* Make left content full width */
                        .lg\:col-span-2 {
                            grid-column: span 3 / span 3 !important;
                            max-width: 100% !important;
                        }

                        /* Hide header, navigation, TOC bar, footer */
                        header, nav, footer, #toc-bar { display: none !important; }

                        /* Hide Contact Form (#contact section) */
                        #contact { display: none !important; }

                        /* Hide Trust Signals Section */
                        #trust-signals { display: none !important; }

                        /* Hide Hero CTA Buttons (Teklif Al, Ara buttons) */
                        #hero-section .flex.flex-col.sm\:flex-row { display: none !important; }
                        #hero-section a[href="#contact"] { display: none !important; }
                        #hero-section a[href^="tel:"] { display: none !important; }

                        /* Hide AI Chat Section */
                        section.py-16.bg-white { display: none !important; }
                        .inline-flex.items-center.gap-2.bg-white\/20 { display: none !important; }

                        /* Hide floating widgets */
                        .fixed { display: none !important; }
                        .sticky { position: relative !important; }

                        /* FAQ - Always Open (Disable Alpine.js accordion) */
                        #faq [x-show] {
                            display: block !important;
                            opacity: 1 !important;
                            max-height: none !important;
                        }
                        #faq .fa-chevron-down { display: none !important; }

                        /* Industries - Force 3x3 Grid */
                        #industries .grid {
                            grid-template-columns: repeat(3, 1fr) !important;
                        }

                        /* Variants - Hide ALL Links & Buttons */
                        #variants a {
                            pointer-events: none !important;
                            text-decoration: none !important;
                            color: inherit !important;
                        }
                        #variants .text-sm.font-semibold { display: none !important; }
                        #variants .mt-4.pt-4.border-t { display: none !important; }
                        #variants .fa-arrow-right { display: none !important; }
                        #variants .fa-chevron-right { display: none !important; }

                        /* Disable All Links (No pointer events, no underline) */
                        a {
                            pointer-events: none !important;
                            text-decoration: none !important;
                            cursor: default !important;
                        }

                        /* ========================================
                           PAGE BREAK FIXES
                           Prevent sections from breaking across pages
                           ======================================== */

                        /* Prevent all sections from breaking */
                        section {
                            page-break-inside: avoid !important;
                            break-inside: avoid !important;
                        }

                        /* Specific section IDs */
                        #gallery, #description, #primary-specs, #features, #variants,
                        #competitive, #technical, #accessories, #usecases, #industries,
                        #certifications, #warranty, #faq {
                            page-break-inside: avoid !important;
                            break-inside: avoid !important;
                        }

                        /* Keep section headers with content */
                        header {
                            page-break-after: avoid !important;
                            break-after: avoid !important;
                        }

                        /* Prevent orphan items in grids/lists */
                        .grid > *, .space-y-4 > *, .space-y-6 > *, .space-y-8 > * {
                            page-break-inside: avoid !important;
                            break-inside: avoid !important;
                        }

                        /* Optimize for print */
                        body { background: white !important; }
                        * {
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                    '
                ]))
                ->save($pdfPath);

            return response()->download($pdfPath, $filename, [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Browsershot PDF generation failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->product_id,
                'url' => $productUrl,
            ]);

            abort(500, 'PDF oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
