<?php

namespace Modules\Page\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Page\App\Models\Page;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class PageApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Page');
    }

    /**
     * Tüm sayfaları listele (homepage hariç)
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $pages = Page::where('is_active', true)
            ->where('is_homepage', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($page) use ($locale) {
                return [
                    'id' => $page->page_id,
                    'title' => $page->getTranslated('title', $locale),
                    'slug' => $page->getTranslated('slug', $locale),
                    'body' => $page->getTranslated('body', $locale),
                    'is_active' => $page->is_active,
                    'is_homepage' => $page->is_homepage,
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $pages,
            'meta' => [
                'total' => $pages->count(),
                'locale' => $locale
            ]
        ]);
    }

    /**
     * Ana sayfayı getir (is_homepage = true)
     */
    public function homepage(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $homepage = Page::where('is_homepage', true)
            ->where('is_active', true)
            ->first();

        if (!$homepage) {
            return response()->json([
                'success' => false,
                'message' => 'Homepage not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $homepage->page_id,
                'title' => $homepage->title,
                'slug' => $homepage->slug,
                'body' => $homepage->body,
                'is_active' => (bool) $homepage->is_active,
                'is_homepage' => (bool) $homepage->is_homepage,
                'css' => $homepage->css,
                'js' => $homepage->js,
                'created_at' => $homepage->created_at,
                'updated_at' => $homepage->updated_at,
            ],
            'meta' => [
                'locale' => $locale,
                'available_locales' => is_array($homepage->title) ? array_keys($homepage->title) : []
            ]
        ]);
    }

    /**
     * Belirli bir sayfayı slug ile getir
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        
        $page = Page::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$page) {
            // Fallback: diğer dillerde ara
            $page = Page::where('is_active', true)
                ->where(function($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                          ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => "Page not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->page_id,
                'title' => $page->getTranslated('title', $locale),
                'slug' => $page->getTranslated('slug', $locale),
                'body' => $page->getTranslated('body', $locale),
                'is_active' => $page->is_active,
                'is_homepage' => $page->is_homepage,
                'css' => $page->css,
                'js' => $page->js,
                'created_at' => $page->created_at,
                'updated_at' => $page->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}