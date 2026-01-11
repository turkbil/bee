<?php

namespace Modules\Announcement\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Announcement\App\Models\Announcement;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class AnnouncementApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Announcement');
    }

    /**
     * Tüm duyuruları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $announcements = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($announcement) use ($locale) {
                return [
                    'id' => $announcement->announcement_id,
                    'title' => $announcement->getTranslated('title', $locale),
                    'slug' => $announcement->getTranslated('slug', $locale),
                    'body' => $announcement->getTranslated('body', $locale),
                    'is_active' => $announcement->is_active,
                    'created_at' => $announcement->created_at,
                    'updated_at' => $announcement->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $announcements,
            'meta' => [
                'total' => $announcements->count(),
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

        $announcement = Announcement::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$announcement) {
            // Fallback: diğer dillerde ara
            $announcement = Announcement::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => "Announcement not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $announcement->announcement_id,
                'title' => $announcement->getTranslated('title', $locale),
                'slug' => $announcement->getTranslated('slug', $locale),
                'body' => $announcement->getTranslated('body', $locale),
                'is_active' => $announcement->is_active,
                'created_at' => $announcement->created_at,
                'updated_at' => $announcement->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
