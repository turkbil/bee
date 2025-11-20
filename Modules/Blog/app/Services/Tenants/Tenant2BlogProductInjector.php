<?php

namespace Modules\Blog\app\Services\Tenants;

use Modules\Shop\app\Models\ShopProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Tenant 2 (ixtif.com) Blog Product Injector
 *
 * Injects shop product cards into blog content for Tenant 2
 */
class Tenant2BlogProductInjector
{
    /**
     * Inject products into blog content between H2 tags
     */
    public function injectProducts(string $content, $blog): string
    {
        try {
            // Cache key per blog
            $cacheKey = "blog_product_injection_{$blog->id}";

            $result = Cache::remember($cacheKey, 3600, function () use ($content, $blog) {
                // Get matching products
                $products = $this->getMatchingProducts($blog);

                if ($products->isEmpty()) {
                    return $content;
                }

                // Split content by H2 tags
                $parts = preg_split('/(<h2[^>]*>.*?<\/h2>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

                if (count($parts) <= 1) {
                    return $content;
                }

                $result = '';
                $h2Count = 0;
                $productIndex = 0;

                foreach ($parts as $part) {
                    // Check if this is an H2 tag
                    if (preg_match('/<h2[^>]*>.*?<\/h2>/i', $part)) {
                        $h2Count++;

                        // Skip first H2, inject BEFORE subsequent H2s
                        if ($h2Count > 1 && $productIndex < $products->count()) {
                            // Get 3 products for this card
                            $cardProducts = $products->slice($productIndex, 3);
                            $result .= $this->renderProductCard($cardProducts);
                            $productIndex += 3;
                        }
                    }

                    $result .= $part;
                }

                return $result;
            });

            return $result;
        } catch (\Exception $e) {
            // Log error and return original content
            \Log::error('Tenant2BlogProductInjector error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return $content;
        }
    }

    /**
     * Get matching products for blog with smart matching and priority sorting
     *
     * MATCHING (hangi �r�nler): Tags � Title � Excerpt � Random
     * SORTING (nas1l s1ralans1n):
     *   1. show_on_homepage=1 olanlar
     *   2. show_on_homepage=0 ama stok>0 veya fiyat>0 olanlar
     *   3. Fiyat veya stok=0 olanlar
     *   4. Dier yak1n �r�nler
     */
    private function getMatchingProducts($blog): Collection
    {
        $matchedProducts = collect();
        $currentLocale = app()->getLocale();

        // 1� MATCHING PHASE: Try matching by TAGS
        if ($blog->tags && $blog->tags->isNotEmpty()) {
            $tagNames = $blog->tags->pluck('name')->toArray();

            foreach ($tagNames as $tagName) {
                $tagProducts = ShopProduct::where(function($q) use ($tagName, $currentLocale) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"$currentLocale\"')) LIKE ?", ["%{$tagName}%"])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.\"$currentLocale\"')) LIKE ?", ["%{$tagName}%"])
                      ->orWhere('tags', 'LIKE', "%{$tagName}%");
                })
                ->get();

                $matchedProducts = $matchedProducts->merge($tagProducts);
            }
        }

        // 2� MATCHING PHASE: Try matching by TITLE keywords
        $title = $blog->getTranslated('title', $currentLocale);
        if ($title) {
            $keywords = explode(' ', $title);
            $keywords = array_filter($keywords, fn($k) => mb_strlen($k) > 3);

            foreach ($keywords as $keyword) {
                $titleProducts = ShopProduct::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"$currentLocale\"')) LIKE ?", ["%{$keyword}%"])
                    ->get();

                $matchedProducts = $matchedProducts->merge($titleProducts);
            }
        }

        // 3� MATCHING PHASE: Try matching by EXCERPT keywords
        $excerpt = $blog->getTranslated('excerpt', $currentLocale);
        if ($excerpt) {
            $keywords = explode(' ', strip_tags($excerpt));
            $keywords = array_filter($keywords, fn($k) => mb_strlen($k) > 4);
            $keywords = array_slice($keywords, 0, 5);

            foreach ($keywords as $keyword) {
                $excerptProducts = ShopProduct::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"$currentLocale\"')) LIKE ?", ["%{$keyword}%"])
                    ->get();

                $matchedProducts = $matchedProducts->merge($excerptProducts);
            }
        }

        // Remove duplicates
        $matchedProducts = $matchedProducts->unique('product_id');

        // 4� MATCHING PHASE: If we don't have enough products (less than 9), add random products
        if ($matchedProducts->count() < 9) {
            $neededCount = 9 - $matchedProducts->count();
            $excludeIds = $matchedProducts->pluck('product_id')->toArray();

            $randomProducts = ShopProduct::whereNotIn('product_id', $excludeIds)
                ->inRandomOrder()
                ->take($neededCount)
                ->get();

            $matchedProducts = $matchedProducts->merge($randomProducts);
        }

        // <� SORTING PHASE: Apply priority sorting
        $sorted = $matchedProducts->sortBy(function($product) {
            $hasStock = ($product->current_stock ?? 0) > 0;
            $hasPrice = ($product->base_price ?? 0) > 0;
            $isHomepage = $product->show_on_homepage == 1;

            // Priority calculation (lower number = higher priority)
            if ($isHomepage) {
                return 1; // Highest priority
            } elseif (!$isHomepage && ($hasStock || $hasPrice)) {
                return 2; // Second priority
            } elseif (!$hasPrice || !$hasStock) {
                return 3; // Third priority
            } else {
                return 4; // Lowest priority
            }
        })->values();

        // Return top 9 products
        return $sorted->take(9);
    }

    /**
     * Render product cards using the EXACT same component as homepage
     *
     * NOTE: Blade::render() CANNOT be used here because the output is passed
     * to another Blade::render() call in show-content.blade.php, causing
     * "Cannot end a section without first starting one" error.
     *
     * Solution: Use view()->make()->render() which doesn't interfere with
     * section management.
     */
    private function renderProductCard(Collection $products): string
    {
        if ($products->isEmpty()) {
            return '';
        }

        $html = "\n\n";

        // "Yazı Devam Ediyor" badge (only on top)
        $html .= "<div class=\"flex justify-center items-center mb-4\">\n";
        $html .= "    <div class=\"inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100/50 dark:bg-gray-800/30 backdrop-blur-sm border border-gray-200/30 dark:border-gray-700/30\">\n";
        $html .= "        <i class=\"fas fa-chevron-down text-gray-400 dark:text-gray-600 animate-bounce text-sm\"></i>\n";
        $html .= "        <span class=\"text-sm font-medium text-gray-400 dark:text-gray-600\">Yazı Devam Ediyor</span>\n";
        $html .= "        <i class=\"fas fa-chevron-down text-gray-400 dark:text-gray-600 animate-bounce text-sm\"></i>\n";
        $html .= "    </div>\n";
        $html .= "</div>\n\n";

        // Mobile/Tablet (xs, sm, md): Horizontal layout (single column)
        $html .= "<div class=\"my-12 lg:hidden grid grid-cols-1 gap-4\">\n";
        foreach ($products as $index => $product) {
            try {
                $html .= view('components.ixtif.product-card', [
                    'product' => $product,
                    'layout' => 'horizontal',
                    'showAddToCart' => true,
                    'index' => $index
                ])->render();
            } catch (\Exception $e) {
                \Log::error('Product card render error (horizontal): ' . $e->getMessage());
            }
        }
        $html .= "</div>\n";

        // Desktop (lg+): Vertical layout (3 columns)
        $html .= "<div class=\"my-12 hidden lg:grid lg:grid-cols-3 gap-6\">\n";
        foreach ($products as $index => $product) {
            try {
                $html .= view('components.ixtif.product-card', [
                    'product' => $product,
                    'layout' => 'vertical',
                    'showAddToCart' => true,
                    'index' => $index
                ])->render();
            } catch (\Exception $e) {
                \Log::error('Product card render error (vertical): ' . $e->getMessage());
            }
        }
        $html .= "</div>\n\n";

        return $html;
    }
}
