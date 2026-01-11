<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers\Front;

use App\Services\SeoMetaTagService;
use Illuminate\Routing\Controller;
use Modules\Shop\App\Exceptions\ShopNotFoundException;
use Modules\Shop\App\Models\ShopProduct;
use Modules\Shop\App\Services\ShopProductService;

class ShopPreviewController extends Controller
{
    public function __construct(
        private readonly ShopProductService $productService
    ) {
    }

    public function show(string $version, string $slug, ?SeoMetaTagService $seoService = null)
    {
        if (!in_array($version, ['v2', 'v3'], true)) {
            abort(404);
        }

        $locale = app()->getLocale();

        try {
            /** @var ShopProduct $product */
            $product = $this->productService
                ->getProductBySlug($slug, $locale);
        } catch (ShopNotFoundException) {
            abort(404);
        }

        $product->load([
            'category',
            'brand',
            'seoSetting',
            'variants' => fn($query) => $query->active()->orderBy('sort_order'),
        ]);

        $relatedProducts = ShopProduct::query()
            ->active()
            ->published()
            ->where('product_id', '!=', $product->product_id)
            ->when($product->category_id, fn($query) => $query->where('category_id', $product->category_id))
            ->limit(8)
            ->get();

        $recentlyViewed = ShopProduct::query()
            ->active()
            ->published()
            ->where('product_id', '!=', $product->product_id)
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        [$parentProduct, $siblingVariants] = $this->resolveVariantContext($product);
        $bundleCandidates = $siblingVariants->isNotEmpty() ? $siblingVariants : $relatedProducts;

        if ($seoService) {
            $seoService->forModel($product);
        }

        view()->share('currentModel', $product);

        return view("shop::front.variations.{$version}", [
            'item' => $product,
            'parentProduct' => $parentProduct,
            'siblingVariants' => $siblingVariants,
            'version' => $version,
            'relatedProducts' => $relatedProducts,
            'bundleCandidates' => $bundleCandidates->take(3),
            'recentlyViewed' => $recentlyViewed,
        ]);
    }

    /**
     * Resolve parent & sibling variants for preview templates.
     *
     * @return array{0: ShopProduct|null, 1: \Illuminate\Support\Collection<ShopProduct>}
     */
    private function resolveVariantContext(ShopProduct $product): array
    {
        $parentProduct = null;
        $siblingVariants = collect();

        if ($product->isVariant()) {
            $parentProduct = $product->parentProduct;

            if ($parentProduct) {
                $siblingVariants = $parentProduct->childProducts()
                    ->where('product_id', '!=', $product->product_id)
                    ->active()
                    ->published()
                    ->get();
            }
        } else {
            $siblingVariants = $product->childProducts()
                ->active()
                ->published()
                ->get();
        }

        return [$parentProduct, $siblingVariants];
    }
}
