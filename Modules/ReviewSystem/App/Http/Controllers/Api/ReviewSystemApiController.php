<?php

namespace Modules\ReviewSystem\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ReviewSystem\App\Models\ReviewSystem;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class ReviewSystemApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('ReviewSystem');
    }

    /**
     * Tüm duyuruları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $reviewsystems = ReviewSystem::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($reviewsystem) use ($locale) {
                return [
                    'id' => $reviewsystem->reviewsystem_id,
                    'title' => $reviewsystem->getTranslated('title', $locale),
                    'slug' => $reviewsystem->getTranslated('slug', $locale),
                    'body' => $reviewsystem->getTranslated('body', $locale),
                    'is_active' => $reviewsystem->is_active,
                    'created_at' => $reviewsystem->created_at,
                    'updated_at' => $reviewsystem->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reviewsystems,
            'meta' => [
                'total' => $reviewsystems->count(),
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

        $reviewsystem = ReviewSystem::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$reviewsystem) {
            // Fallback: diğer dillerde ara
            $reviewsystem = ReviewSystem::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$reviewsystem) {
            return response()->json([
                'success' => false,
                'message' => "ReviewSystem not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $reviewsystem->reviewsystem_id,
                'title' => $reviewsystem->getTranslated('title', $locale),
                'slug' => $reviewsystem->getTranslated('slug', $locale),
                'body' => $reviewsystem->getTranslated('body', $locale),
                'is_active' => $reviewsystem->is_active,
                'created_at' => $reviewsystem->created_at,
                'updated_at' => $reviewsystem->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
