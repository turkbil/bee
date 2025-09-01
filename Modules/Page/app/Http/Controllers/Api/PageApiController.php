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
     * Tüm sayfaları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        
        $pages = Page::where('is_active', true)
            ->where('is_homepage', false)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($page) use ($locale) {
                return [
                    'id' => $page->id,
                    'title' => $page->getTranslated('title', $locale),
                    'slug' => $page->getTranslated('slug', $locale),
                    'content' => $page->getTranslated('content', $locale),
                    'excerpt' => $page->getTranslated('excerpt', $locale),
                    'meta_description' => $page->getTranslated('meta_description', $locale),
                    'meta_keywords' => $page->getTranslated('meta_keywords', $locale),
                    'featured_image' => $page->featured_image,
                    'is_active' => $page->is_active,
                    'is_homepage' => $page->is_homepage,
                    'sort_order' => $page->sort_order,
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

        // Debug: Raw data
        \Log::info('Homepage API Debug', [
            'homepage_exists' => !!$homepage,
            'homepage_id' => $homepage?->id,
            'homepage_body_raw' => $homepage?->body,
            'homepage_body_tr' => $homepage?->body['tr'] ?? 'N/A'
        ]);

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
                'page_id' => $homepage->id,
                'id' => $homepage->id, // backward compatibility
                'title' => $homepage->title, // JSON object format
                'slug' => $homepage->slug, // JSON object format  
                'body' => $homepage->fresh()->body ?? [], // Fresh from DB
                'content' => $homepage->fresh()->body ?? [], // Fresh from DB
                'excerpt' => $homepage->excerpt ?? [],
                'meta_description' => $homepage->meta_description ?? [],
                'meta_keywords' => $homepage->meta_keywords ?? [],
                'seo' => [
                    'meta_description' => $homepage->meta_description ?? [],
                    'meta_keywords' => $homepage->meta_keywords ?? []
                ],
                'featured_image' => $homepage->featured_image,
                'is_active' => (bool) $homepage->is_active,
                'is_homepage' => (bool) $homepage->is_homepage,
                'sort_order' => $homepage->sort_order,
                'created_at' => $homepage->created_at,
                'updated_at' => $homepage->updated_at,
            ],
            'meta' => [
                'locale' => $locale,
                'requested_locale' => $locale,
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
                'id' => $page->id,
                'title' => $page->getTranslated('title', $locale),
                'slug' => $page->getTranslated('slug', $locale),
                'content' => $page->getTranslated('content', $locale),
                'excerpt' => $page->getTranslated('excerpt', $locale),
                'meta_description' => $page->getTranslated('meta_description', $locale),
                'meta_keywords' => $page->getTranslated('meta_keywords', $locale),
                'featured_image' => $page->featured_image,
                'is_active' => $page->is_active,
                'is_homepage' => $page->is_homepage,
                'sort_order' => $page->sort_order,
                'created_at' => $page->created_at,
                'updated_at' => $page->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}