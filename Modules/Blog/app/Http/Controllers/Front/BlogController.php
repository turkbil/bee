<?php

namespace Modules\Blog\App\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogCategory;
use App\Services\ThemeService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Spatie\ResponseCache\Facades\ResponseCache;
use App\Services\ModuleSlugService;
use App\Traits\HasModuleAccessControl;
use App\Models\ModuleTenantSetting;
use App\Services\SeoMetaTagService;
use App\Models\Tag;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    use HasModuleAccessControl;

    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;

        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Blog');
    }

    /**
     * Ana sayfa desteği yok - Blog modülünde homeblog özelliği kaldırıldı
     */
    public function homeblog(SeoMetaTagService $seoService)
    {
        // Ana sayfa özelliği Blog modülünde desteklenmiyor
        abort(404);
    }

    public function index()
    {
        $query = Blog::query()
            ->with(['category', 'tags', 'media'])
            ->published();

        $tag = request('tag');
        $resolvedTag = null;

        if ($tag) {
            $slug = Str::slug($tag);

            $resolvedTag = Tag::query()
                ->where('slug', $slug)
                ->first();

            $query->whereHas('tags', function ($q) use ($slug, $tag) {
                $q->where('slug', $slug)
                    ->orWhere('name', 'like', '%' . $tag . '%');
            });
        }

        // Kategori seçimi yok - index tüm blogları gösterir
        $selectedCategory = null;

        $items = $query->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('created_at')
            ->simplePaginate(12);

        // Sadece ana kategorileri çek (parent_id = null), alt kategorilerle birlikte
        $categories = BlogCategory::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)
                    ->withCount(['blogs' => function ($q2) {
                        $q2->published();
                    }])
                    ->orderBy('sort_order')
                    ->orderBy('title');
            }])
            ->withCount(['blogs' => function ($q) {
                $q->published();
            }])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        // Modül title'ını al
        $moduleTitle = $tag
            ? '#' . ($resolvedTag?->name ?? Str::title(str_replace('-', ' ', $tag))) . ' Etiketli Yazılar'
            : $this->getModuleTitle('Blog');

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('index', 'blog');
            return view($viewPath, [
                'items' => $items,
                'moduleTitle' => $moduleTitle,
                'tag' => $tag,
                'resolvedTag' => $resolvedTag,
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
            ]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());

            // Fallback view'a yönlendir
            return view('blog::front.index', [
                'items' => $items,
                'moduleTitle' => $moduleTitle,
                'tag' => $tag,
                'resolvedTag' => $resolvedTag,
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
            ]);
        }
    }

    /**
     * Infinity scroll için JSON API endpoint
     */
    public function loadMore()
    {
        $query = Blog::query()
            ->with(['category', 'tags', 'media'])
            ->published();

        $tag = request('tag');
        $categorySlug = request('category');

        // Category filtering (Shop pattern - includes children)
        if ($categorySlug) {
            $currentLocale = app()->getLocale();
            $selectedCategory = BlogCategory::where('is_active', true)
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"" . $currentLocale . "\"')) = ?", [$categorySlug])
                ->first();

            if ($selectedCategory) {
                $categoryIds = $this->getCategoryWithChildren($selectedCategory);
                $query->whereIn('blog_category_id', $categoryIds);
            }
        }

        // Tag filtering
        if ($tag) {
            $slug = Str::slug($tag);

            $query->whereHas('tags', function ($q) use ($slug, $tag) {
                $q->where('slug', $slug)
                    ->orWhere('name', 'like', '%' . $tag . '%');
            });
        }

        $items = $query->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('created_at')
            ->simplePaginate(12);

        $currentLocale = app()->getLocale();
        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';

        $blogs = $items->map(function ($item) use ($currentLocale, $slugPrefix, $localePrefix) {
            $slugData = $item->getRawOriginal('slug');

            if (is_string($slugData)) {
                $slugData = json_decode($slugData, true) ?: [];
            }

            if (is_array($slugData) && isset($slugData[$currentLocale]) && !empty($slugData[$currentLocale])) {
                $slug = $slugData[$currentLocale];
            } elseif (is_array($slugData) && !empty($slugData)) {
                $slug = reset($slugData);
            } elseif (is_string($slugData) && !empty($slugData)) {
                $slug = $slugData;
            } else {
                $slug = 'blog-' . $item->blog_id;
            }

            $title = $item->getTranslated('title', $currentLocale);
            $body = $item->getTranslated('body', $currentLocale);
            $excerpt = $item->getTranslated('excerpt', $currentLocale) ?: Str::limit(strip_tags($body), 150);

            // Helper function ile multi-collection fallback
            $featuredMedia = getFirstMediaWithFallback($item);
            $imageUrl = $featuredMedia
                ? thumb($featuredMedia, 400, 300, ['quality' => 85, 'format' => 'webp'])
                : null;

            return [
                'id' => $item->blog_id,
                'title' => $title,
                'excerpt' => $excerpt,
                'url' => url($localePrefix . '/' . $slugPrefix . '/' . $slug),
                'image' => $imageUrl,
                'date' => $item->created_at->format('d.m.Y'),
            ];
        });

        return response()->json([
            'data' => $blogs,
            'has_more' => $items->hasMorePages(),
            'next_page' => $items->hasMorePages() ? $items->currentPage() + 1 : null,
        ]);
    }

    /**
     * Kategori sayfası
     */
    public function category($slug)
    {
        $currentLocale = app()->getLocale();

        // Kategoriyi bul - slug çoklu dil JSON formatında (children ile birlikte)
        $selectedCategory = BlogCategory::where('is_active', true)
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"" . $currentLocale . "\"')) = ?", [$slug])
            ->with(['children' => function ($q) {
                $q->where('is_active', true)
                    ->withCount(['blogs' => function ($q2) {
                        $q2->published();
                    }])
                    ->orderBy('sort_order')
                    ->orderBy('title');
            }])
            ->first();

        if (!$selectedCategory) {
            abort(404);
        }

        // Bu kategori ve alt kategorilerindeki blogları çek (Shop pattern)
        $categoryIds = $this->getCategoryWithChildren($selectedCategory);

        $items = Blog::query()
            ->with(['category', 'tags', 'media'])
            ->published()
            ->whereIn('blog_category_id', $categoryIds)
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('created_at')
            ->simplePaginate(12);

        // Tüm kategorileri çek (slider için)
        $categories = BlogCategory::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)
                    ->withCount(['blogs' => function ($q2) {
                        $q2->published();
                    }])
                    ->orderBy('sort_order')
                    ->orderBy('title');
            }])
            ->withCount(['blogs' => function ($q) {
                $q->published();
            }])
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        // Modül title'ı kategori adı olsun
        $moduleTitle = $selectedCategory->getTranslated('title', $currentLocale);

        try {
            $viewPath = $this->themeService->getThemeViewPath('index', 'blog');
            return view($viewPath, [
                'items' => $items,
                'moduleTitle' => $moduleTitle,
                'tag' => null,
                'resolvedTag' => null,
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
            ]);
        } catch (\Exception $e) {
            Log::error("Theme Error: " . $e->getMessage());

            return view('blog::front.index', [
                'items' => $items,
                'moduleTitle' => $moduleTitle,
                'tag' => null,
                'resolvedTag' => null,
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
            ]);
        }
    }

    public function clearCache()
    {
        try {
            // Tüm cache türlerini temizle
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Response Cache temizle
            if (class_exists('Spatie\ResponseCache\Facades\ResponseCache')) {
                ResponseCache::clear();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache başarıyla temizlendi'
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizleme hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tag($tag)
    {
        $slug = Str::slug($tag);

        $resolvedTag = Tag::query()
            ->where('slug', $slug)
            ->first();

        if (!$resolvedTag) {
            abort(404);
        }

        $items = Blog::query()
            ->with(['category', 'tags', 'media'])
            ->published()
            ->whereHas('tags', fn ($query) => $query->where('slug', $resolvedTag->slug))
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('created_at')
            ->simplePaginate(10);

        $displayTag = $resolvedTag->name;
        $moduleTitle = "#{$displayTag} Etiketli Yazılar";

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('tag', 'blog');
            return view($viewPath, [
                'items' => $items,
                'moduleTitle' => $moduleTitle,
                'tag' => $resolvedTag->slug,
                'displayTag' => $displayTag,
                'resolvedTag' => $resolvedTag,
            ]);
        } catch (\Exception $e) {
            // Tema yoksa index view'ını kullan
            try {
                $viewPath = $this->themeService->getThemeViewPath('index', 'blog');
                return view($viewPath, [
                    'items' => $items,
                    'moduleTitle' => $moduleTitle,
                    'tag' => $resolvedTag->slug,
                    'displayTag' => $displayTag,
                    'resolvedTag' => $resolvedTag,
                ]);
            } catch (\Exception $e2) {
                // Fallback view'a yönlendir
                return view('blog::front.index', [
                    'items' => $items,
                    'moduleTitle' => $moduleTitle,
                    'tag' => $resolvedTag->slug,
                    'displayTag' => $displayTag,
                    'resolvedTag' => $resolvedTag,
                ]);
            }
        }
    }

    public function show($slug, SeoMetaTagService $seoService = null)
    {
        // Debug log - sadece verbose logs aktifse
        if (config('blog.debug.verbose_logs', false)) {
            Log::info('🔍 BlogController::show called', [
                'slug' => $slug,
                'request_url' => request()->fullUrl(),
                'app_locale' => app()->getLocale()
            ]);
        }

        // Aktif dili al
        $currentLocale = app()->getLocale();

        // SADECE aktif dilde slug ara - locale-aware
        $item = Blog::query()
            ->with('category')
            ->where('is_active', true)
            ->published()
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"" . $currentLocale . "\"')) = ?", [$slug])
            ->first();

        // Bulunamazsa 404
        if (!$item) {
            // Mevcut dilde bulunamadı, tüm dillerde ara (fallback)
            $allLocales = \App\Services\TenantLanguageProvider::getActiveLanguageCodes();

            foreach ($allLocales as $locale) {
                if ($locale === $currentLocale) {
                    continue; // Zaten aradık
                }

                $item = Blog::query()
                    ->with('category')
                    ->where('is_active', true)
                    ->published()
                    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"" . $locale . "\"')) = ?", [$slug])
                    ->first();

                if ($item) {
                    // Farklı dilde bulundu, ama kullanıcının seçtiği dilde göster (fallback content ile)
                    // Redirect etmek yerine mevcut locale'de göster
                    break; // Döngüden çık ve sayfayı göster
                }
            }

            // Döngü bittikten sonra hala bulunamadıysa 404
            if (!$item) {
                Log::warning("Blog not found in any language", [
                    'slug' => $slug,
                    'searched_locales' => $allLocales
                ]);
                abort(404, "Blog not found for slug '{$slug}'");
            }
        }

        // Canonical URL kontrolü - doğru slug kullanılıyor mu?
        $expectedSlug = $item->getTranslated('slug', $currentLocale);

        if (config('blog.debug.verbose_logs', false)) {
            Log::info('🔍 Canonical URL check', [
                'slug' => $slug,
                'expectedSlug' => $expectedSlug,
                'currentLocale' => $currentLocale,
                'will_redirect' => $slug !== $expectedSlug
            ]);
        }

        if ($slug !== $expectedSlug) {
            $redirectUrl = $this->generatePageUrl($item, $currentLocale);

            if (config('blog.debug.verbose_logs', false)) {
                Log::info('🔄 Canonical redirect', [
                    'from' => request()->fullUrl(),
                    'to' => $redirectUrl
                ]);
            }

            // Yanlış slug ile erişim, doğru URL'e redirect
            return redirect()->to($redirectUrl);
        }

        // SEO meta tags için model'i global olarak paylaş
        view()->share('currentModel', $item);

        try {
            // Modül adıyla tema yolunu al
            $viewPath = $this->themeService->getThemeViewPath('show', 'blog');
            return view($viewPath, ['item' => $item]);
        } catch (\Exception $e) {
            // Hatayı logla
            Log::error("Theme Error: " . $e->getMessage());

            // Fallback view'a yönlendir
            return view('blog::front.show', ['item' => $item]);
        }
    }

    /**
     * Sayfa için locale-aware URL oluştur
     */
    protected function generatePageUrl(Blog $blog, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $blog->getTranslated('slug', $locale);

        // Modül slug'ını al (tenant tarafından özelleştirilebilir)
        $moduleSlug = ModuleSlugService::getSlug('Blog', 'show');

        // Varsayılan dil kontrolü
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            // Varsayılan dil için prefix yok
            return url("/{$moduleSlug}/{$slug}");
        }

        // Diğer diller için prefix ekle
        return url("/{$locale}/{$moduleSlug}/{$slug}");
    }

    /**
     * Modül title'ını al - settings tablosundan varsa onu, yoksa fallback
     */
    private function getModuleTitle(string $moduleName): string
    {
        $currentLocale = app()->getLocale();

        // ModuleTenantSetting'den title al
        $setting = ModuleTenantSetting::where('module_name', $moduleName)->first();

        if ($setting && $setting->title && isset($setting->title[$currentLocale])) {
            $customTitle = trim((string) $setting->title[$currentLocale]);

            if ($customTitle !== '' && !filter_var($customTitle, FILTER_VALIDATE_URL)) {
                return $customTitle;
            }
        }

        $defaultName = ModuleSlugService::getDefaultModuleName($moduleName, $currentLocale);

        if ($defaultName && !filter_var($defaultName, FILTER_VALIDATE_URL)) {
            return $defaultName;
        }

        return __('blog::front.general.blogs');
    }

    /**
     * Get category ID and all its children IDs recursively (Shop pattern)
     */
    private function getCategoryWithChildren(BlogCategory $category): array
    {
        $ids = [$category->category_id];

        $children = BlogCategory::where('parent_id', $category->category_id)
            ->where('is_active', true)
            ->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getCategoryWithChildren($child));
        }

        return $ids;
    }
}
