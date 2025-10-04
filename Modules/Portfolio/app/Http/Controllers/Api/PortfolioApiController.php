<?php

namespace Modules\Portfolio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Portfolio\App\Models\Portfolio;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class PortfolioApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Portfolio');
    }

    /**
     * Tüm portfolyoları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $portfolios = Portfolio::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($portfolio) use ($locale) {
                return [
                    'id' => $portfolio->portfolio_id,
                    'title' => $portfolio->getTranslated('title', $locale),
                    'slug' => $portfolio->getTranslated('slug', $locale),
                    'body' => $portfolio->getTranslated('body', $locale),
                    'is_active' => $portfolio->is_active,
                    'created_at' => $portfolio->created_at,
                    'updated_at' => $portfolio->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $portfolios,
            'meta' => [
                'total' => $portfolios->count(),
                'locale' => $locale
            ]
        ]);
    }

    /**
     * Belirli bir portfolyoyu slug ile getir
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $portfolio = Portfolio::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$portfolio) {
            // Fallback: diğer dillerde ara
            $portfolio = Portfolio::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$portfolio) {
            return response()->json([
                'success' => false,
                'message' => "Portfolio not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $portfolio->portfolio_id,
                'title' => $portfolio->getTranslated('title', $locale),
                'slug' => $portfolio->getTranslated('slug', $locale),
                'body' => $portfolio->getTranslated('body', $locale),
                'is_active' => $portfolio->is_active,
                'created_at' => $portfolio->created_at,
                'updated_at' => $portfolio->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
