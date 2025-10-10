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
}
