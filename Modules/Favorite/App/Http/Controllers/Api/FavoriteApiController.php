<?php

namespace Modules\Favorite\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Favorite\App\Models\Favorite;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class FavoriteApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Favorite');
    }

    /**
     * Tüm duyuruları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $favorites = Favorite::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($favorite) use ($locale) {
                return [
                    'id' => $favorite->favorite_id,
                    'title' => $favorite->getTranslated('title', $locale),
                    'slug' => $favorite->getTranslated('slug', $locale),
                    'body' => $favorite->getTranslated('body', $locale),
                    'is_active' => $favorite->is_active,
                    'created_at' => $favorite->created_at,
                    'updated_at' => $favorite->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $favorites,
            'meta' => [
                'total' => $favorites->count(),
                'locale' => $locale
            ]
        ]);
    }

    /**
     * Belirli bir sayfayı slug ile getir
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $favorite = Favorite::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$favorite) {
            // Fallback: diğer dillerde ara
            $favorite = Favorite::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => "Favorite not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $favorite->favorite_id,
                'title' => $favorite->getTranslated('title', $locale),
                'slug' => $favorite->getTranslated('slug', $locale),
                'body' => $favorite->getTranslated('body', $locale),
                'is_active' => $favorite->is_active,
                'created_at' => $favorite->created_at,
                'updated_at' => $favorite->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
