<?php

namespace Modules\Muzibu\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Muzibu;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class MuzibuApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Muzibu');
    }

    /**
     * Tüm portfolyoları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $muzibus = Muzibu::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($muzibu) use ($locale) {
                return [
                    'id' => $muzibu->muzibu_id,
                    'title' => $muzibu->getTranslated('title', $locale),
                    'slug' => $muzibu->getTranslated('slug', $locale),
                    'body' => $muzibu->getTranslated('body', $locale),
                    'is_active' => $muzibu->is_active,
                    'created_at' => $muzibu->created_at,
                    'updated_at' => $muzibu->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $muzibus,
            'meta' => [
                'total' => $muzibus->count(),
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

        $muzibu = Muzibu::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$muzibu) {
            // Fallback: diğer dillerde ara
            $muzibu = Muzibu::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$muzibu) {
            return response()->json([
                'success' => false,
                'message' => "Muzibu not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $muzibu->muzibu_id,
                'title' => $muzibu->getTranslated('title', $locale),
                'slug' => $muzibu->getTranslated('slug', $locale),
                'body' => $muzibu->getTranslated('body', $locale),
                'is_active' => $muzibu->is_active,
                'created_at' => $muzibu->created_at,
                'updated_at' => $muzibu->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
