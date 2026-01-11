<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Controllers\Api;

use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shop\App\Http\Resources\ShopResource;
use Modules\Shop\App\Models\ShopProduct;

class ShopApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        $this->checkModuleAccess('Shop');
    }

    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $products = ShopProduct::query()
            ->with(['category', 'brand'])
            ->active()
            ->published()
            ->orderByDesc('product_id')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ShopResource::collection($products)->resolve(),
            'meta' => [
                'total' => $products->total(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'locale' => $locale,
            ],
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $product = ShopProduct::query()
            ->with(['category', 'brand'])
            ->active()
            ->published()
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug])
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => __('shop::api.product_not_found'),
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ShopResource::make($product)->resolve(),
        ]);
    }

    /**
     * Mega menu data endpoint
     * ⚠️ DO NOT REMOVE - Performance optimization (lazy loading)
     */
    public function megaMenu(): JsonResponse
    {
        try {
            $mainCategories = \Modules\Shop\App\Models\ShopCategory::where('is_active', 1)
                ->whereNull('parent_id')
                ->whereNotIn('category_id', [5, 6])
                ->orderBy('sort_order', 'asc')
                ->get();

            $categoryData = [];
            foreach ($mainCategories as $cat) {
                $catId = $cat->category_id;
                $catTitle = is_array($cat->title) ? ($cat->title['tr'] ?? $cat->title['en'] ?? '') : $cat->title;
                $catSlug = is_array($cat->slug) ? ($cat->slug['tr'] ?? $cat->slug['en'] ?? '') : $cat->slug;
                $catIcon = $cat->icon_class ?? 'fa-solid fa-box';

                if ($catId == 7) {
                    $subcategories = \Modules\Shop\App\Models\ShopCategory::where('is_active', 1)
                        ->where('parent_id', $catId)
                        ->orderBy('sort_order', 'asc')
                        ->get()
                        ->map(fn($sub) => [
                            'id' => $sub->category_id,
                            'title' => is_array($sub->title) ? ($sub->title['tr'] ?? $sub->title['en'] ?? '') : $sub->title,
                            'slug' => is_array($sub->slug) ? ($sub->slug['tr'] ?? $sub->slug['en'] ?? '') : $sub->slug,
                        ]);

                    $categoryData[] = [
                        'id' => $catId,
                        'title' => $catTitle,
                        'slug' => $catSlug,
                        'icon' => $catIcon,
                        'type' => 'subcategories',
                        'subcategories' => $subcategories,
                    ];
                } else {
                    $products = ShopProduct::where('category_id', $catId)
                        ->where('is_active', 1)
                        ->whereNull('parent_product_id')
                        ->with(['currency', 'category', 'media'])
                        ->orderBy('sort_order', 'asc')
                        ->take(4)
                        ->get()
                        ->map(function($product) {
                            $media = $product->getFirstMedia('featured_image');
                            return [
                                'id' => $product->product_id,
                                'title' => is_array($product->title) ? ($product->title['tr'] ?? $product->title['en'] ?? '') : $product->title,
                                'slug' => is_array($product->slug) ? ($product->slug['tr'] ?? $product->slug['en'] ?? '') : $product->slug,
                                'image' => $media ? thumb($media, 400, 300) : null,
                                'price' => $product->selling_price,
                                'currency' => $product->currency?->code ?? 'TRY',
                            ];
                        });

                    $categoryData[] = [
                        'id' => $catId,
                        'title' => $catTitle,
                        'slug' => $catSlug,
                        'icon' => $catIcon,
                        'type' => 'products',
                        'products' => $products,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $categoryData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Mega menu API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load menu',
            ], 500);
        }
    }
}
