<?php

namespace Modules\Portfolio\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Portfolio\App\Models\Page;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class PortfolioApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Page');
    }

    /**
     * Tüm sayfaları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        
        $portfolios = Page::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($portfolio use ($locale) {
                return [
                    'id' => $portfolio>id,
                    'title' => $portfolio>getTranslated('title', $locale),
                    'slug' => $portfolio>getTranslated('slug', $locale),
                    'content' => $portfolio>getTranslated('content', $locale),
                    'excerpt' => $portfolio>getTranslated('excerpt', $locale),
                    'meta_description' => $portfolio>getTranslated('meta_description', $locale),
                    'meta_keywords' => $portfolio>getTranslated('meta_keywords', $locale),
                    'featured_image' => $portfolio>featured_image,
                    'is_active' => $portfolio>is_active,
                    'sort_order' => $portfolio>sort_order,
                    'created_at' => $portfolio>created_at,
                    'updated_at' => $portfolio>updated_at,
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
     */
    {
        $locale = $request->get('locale', app()->getLocale());
        
            ->where('is_active', true)
            ->first();

        // Debug: Raw data
        ]);

            return response()->json([
                'success' => false,
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'seo' => [
                ],
            ],
            'meta' => [
                'locale' => $locale,
                'requested_locale' => $locale,
            ]
        ]);
    }

    /**
     * Belirli bir sayfayı slug ile getir
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        
        $portfolio= Page::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$portfolio {
            // Fallback: diğer dillerde ara
            $portfolio= Page::where('is_active', true)
                ->where(function($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                          ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$portfolio {
            return response()->json([
                'success' => false,
                'message' => "Portfolio not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $portfolio>id,
                'title' => $portfolio>getTranslated('title', $locale),
                'slug' => $portfolio>getTranslated('slug', $locale),
                'content' => $portfolio>getTranslated('content', $locale),
                'excerpt' => $portfolio>getTranslated('excerpt', $locale),
                'meta_description' => $portfolio>getTranslated('meta_description', $locale),
                'meta_keywords' => $portfolio>getTranslated('meta_keywords', $locale),
                'featured_image' => $portfolio>featured_image,
                'is_active' => $portfolio>is_active,
                'sort_order' => $portfolio>sort_order,
                'created_at' => $portfolio>created_at,
                'updated_at' => $portfolio>updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}