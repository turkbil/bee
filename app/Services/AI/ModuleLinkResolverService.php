<?php

namespace App\Services\AI;

use App\Services\ModuleSlugService;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Models\ShopBrand;
use Modules\Blog\App\Models\Blog;
use Modules\Blog\App\Models\BlogCategory;
use Modules\Page\App\Models\Page;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;

/**
 * Universal Link Resolver Service
 *
 * AI'ın verdiği [LINK:module:type:id] formatını çözer ve doğru URL'i oluşturur.
 * - Tenant-aware: Her tenant'ın kendi slug'larını kullanır
 * - Multi-language: Aktif dile göre slug çözer
 * - Universal: Tüm modüller için çalışır
 */
class ModuleLinkResolverService
{
    protected string $locale;
    protected string $defaultLocale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
        $this->defaultLocale = get_tenant_default_locale() ?? 'tr';
    }

    /**
     * Link'i çöz ve URL döndür
     *
     * @param string $module Module adı (shop, blog, page, portfolio)
     * @param string|null $type Alt tip (product, category, brand, post vb.)
     * @param int $id Entity ID
     * @return array ['url' => string, 'slug' => string, 'title' => string]|null
     */
    public function resolve(string $module, ?string $type, int $id): ?array
    {
        return match($module) {
            'shop' => $this->resolveShop($type, $id),
            'blog' => $this->resolveBlog($type, $id),
            'page' => $this->resolvePage($id),
            'portfolio' => $this->resolvePortfolio($type, $id),
            default => null,
        };
    }

    /**
     * Shop modülü link çözümleyici
     */
    protected function resolveShop(?string $type, int $id): ?array
    {
        $moduleSlug = ModuleSlugService::getSlug('Shop', 'show');
        $moduleSlug = trim($moduleSlug, '/');

        switch ($type) {
            case 'product':
                $product = ShopProduct::query()
                    ->where('product_id', $id)
                    ->active()
                    ->published()
                    ->first();

                if (!$product) {
                    return null;
                }

                $slug = $product->getTranslated('slug', $this->locale) ?? $product->slug[$this->locale] ?? null;
                $title = $product->getTranslated('title', $this->locale) ?? $product->title[$this->locale] ?? 'Product';

                if (!$slug) {
                    return null;
                }

                $url = $this->buildUrl($moduleSlug, $slug);

                return [
                    'url' => $url,
                    'slug' => $slug,
                    'title' => $title,
                    'type' => 'product',
                ];

            case 'category':
                $category = ShopCategory::query()
                    ->where('category_id', $id)
                    ->first();

                if (!$category) {
                    return null;
                }

                $slug = $category->getTranslated('slug', $this->locale) ?? $category->slug[$this->locale] ?? null;
                $title = $category->getTranslated('name', $this->locale) ?? $category->name[$this->locale] ?? 'Category';

                if (!$slug) {
                    return null;
                }

                $url = $this->buildUrl('shop/category', $slug);

                return [
                    'url' => $url,
                    'slug' => $slug,
                    'title' => $title,
                    'type' => 'category',
                ];

            case 'brand':
                $brand = ShopBrand::query()
                    ->where('brand_id', $id)
                    ->first();

                if (!$brand) {
                    return null;
                }

                $slug = $brand->getTranslated('slug', $this->locale) ?? $brand->slug[$this->locale] ?? null;
                $title = $brand->getTranslated('name', $this->locale) ?? $brand->name[$this->locale] ?? 'Brand';

                if (!$slug) {
                    return null;
                }

                $url = $this->buildUrl('shop/brand', $slug);

                return [
                    'url' => $url,
                    'slug' => $slug,
                    'title' => $title,
                    'type' => 'brand',
                ];

            default:
                return null;
        }
    }

    /**
     * Blog modülü link çözümleyici
     */
    protected function resolveBlog(?string $type, int $id): ?array
    {
        $moduleSlug = ModuleSlugService::getSlug('Blog', 'show');
        $moduleSlug = trim($moduleSlug, '/');

        switch ($type) {
            case 'post':
            case null: // Tip verilmezse post kabul et
                $post = Blog::query()
                    ->where('blog_id', $id)
                    ->active()
                    ->published()
                    ->first();

                if (!$post) {
                    return null;
                }

                $slug = $post->getTranslated('slug', $this->locale) ?? $post->slug[$this->locale] ?? null;
                $title = $post->getTranslated('title', $this->locale) ?? $post->title[$this->locale] ?? 'Post';

                if (!$slug) {
                    return null;
                }

                $url = $this->buildUrl($moduleSlug, $slug);

                return [
                    'url' => $url,
                    'slug' => $slug,
                    'title' => $title,
                    'type' => 'post',
                ];

            case 'category':
                $category = BlogCategory::query()
                    ->where('category_id', $id)
                    ->first();

                if (!$category) {
                    return null;
                }

                $slug = $category->getTranslated('slug', $this->locale) ?? $category->slug[$this->locale] ?? null;
                $title = $category->getTranslated('name', $this->locale) ?? $category->name[$this->locale] ?? 'Category';

                if (!$slug) {
                    return null;
                }

                $url = $this->buildUrl('blog/category', $slug);

                return [
                    'url' => $url,
                    'slug' => $slug,
                    'title' => $title,
                    'type' => 'category',
                ];

            default:
                return null;
        }
    }

    /**
     * Page modülü link çözümleyici
     */
    protected function resolvePage(int $id): ?array
    {
        $page = Page::query()
            ->where('page_id', $id)
            ->active()
            ->published()
            ->first();

        if (!$page) {
            return null;
        }

        $slug = $page->getTranslated('slug', $this->locale) ?? $page->slug[$this->locale] ?? null;
        $title = $page->getTranslated('title', $this->locale) ?? $page->title[$this->locale] ?? 'Page';

        if (!$slug) {
            return null;
        }

        // Page için ModuleSlugService kullan
        $moduleSlug = ModuleSlugService::getSlug('Page', 'show');
        $moduleSlug = trim($moduleSlug, '/');

        $url = $this->buildUrl($moduleSlug, $slug);

        return [
            'url' => $url,
            'slug' => $slug,
            'title' => $title,
            'type' => 'page',
        ];
    }

    /**
     * Portfolio modülü link çözümleyici
     */
    protected function resolvePortfolio(?string $type, int $id): ?array
    {
        $moduleSlug = ModuleSlugService::getSlug('Portfolio', 'show');
        $moduleSlug = trim($moduleSlug, '/');

        switch ($type) {
            case 'project':
            case null:
                $project = Portfolio::query()
                    ->where('portfolio_id', $id)
                    ->active()
                    ->published()
                    ->first();

                if (!$project) {
                    return null;
                }

                $slug = $project->getTranslated('slug', $this->locale) ?? $project->slug[$this->locale] ?? null;
                $title = $project->getTranslated('title', $this->locale) ?? $project->title[$this->locale] ?? 'Project';

                if (!$slug) {
                    return null;
                }

                $url = $this->buildUrl($moduleSlug, $slug);

                return [
                    'url' => $url,
                    'slug' => $slug,
                    'title' => $title,
                    'type' => 'project',
                ];

            case 'category':
                $category = PortfolioCategory::query()
                    ->where('category_id', $id)
                    ->first();

                if (!$category) {
                    return null;
                }

                $slug = $category->getTranslated('slug', $this->locale) ?? $category->slug[$this->locale] ?? null;
                $title = $category->getTranslated('name', $this->locale) ?? $category->name[$this->locale] ?? 'Category';

                if (!$slug) {
                    return null;
                }

                $url = $this->buildUrl('portfolio/category', $slug);

                return [
                    'url' => $url,
                    'slug' => $slug,
                    'title' => $title,
                    'type' => 'category',
                ];

            default:
                return null;
        }
    }

    /**
     * URL oluştur (dil prefixi ile)
     */
    protected function buildUrl(string $moduleSlug, string $slug): string
    {
        $moduleSlug = ltrim($moduleSlug, '/');
        $slug = ltrim($slug, '/');

        if ($this->locale === $this->defaultLocale) {
            return '/' . $moduleSlug . '/' . $slug;
        }

        return '/' . $this->locale . '/' . $moduleSlug . '/' . $slug;
    }

    /**
     * Batch resolve (çoklu link çözme)
     *
     * @param array $links [['module' => 'shop', 'type' => 'product', 'id' => 296], ...]
     * @return array
     */
    public function resolveBatch(array $links): array
    {
        $results = [];

        foreach ($links as $link) {
            $module = $link['module'] ?? null;
            $type = $link['type'] ?? null;
            $id = $link['id'] ?? null;

            if (!$module || !$id) {
                continue;
            }

            $resolved = $this->resolve($module, $type, (int)$id);

            if ($resolved) {
                $results[] = array_merge($link, $resolved);
            }
        }

        return $results;
    }
}
