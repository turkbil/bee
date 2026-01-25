<?php

namespace Modules\Service\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Service\App\Models\Service;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class ServiceApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Service');
    }

    /**
     * Tüm portfolyoları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $services = Service::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($service) use ($locale) {
                return [
                    'id' => $service->service_id,
                    'title' => $service->getTranslated('title', $locale),
                    'slug' => $service->getTranslated('slug', $locale),
                    'body' => $service->getTranslated('body', $locale),
                    'is_active' => $service->is_active,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $services,
            'meta' => [
                'total' => $services->count(),
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

        $service = Service::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$service) {
            // Fallback: diğer dillerde ara
            $service = Service::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => "Service not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $service->service_id,
                'title' => $service->getTranslated('title', $locale),
                'slug' => $service->getTranslated('slug', $locale),
                'body' => $service->getTranslated('body', $locale),
                'is_active' => $service->is_active,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
