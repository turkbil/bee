<?php

namespace Modules\Blog\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\App\Models\Blog;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class BlogApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Blog');
    }

    /**
     * Tüm portfolyoları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $blogs = Blog::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($blog) use ($locale) {
                return [
                    'id' => $blog->blog_id,
                    'title' => $blog->getTranslated('title', $locale),
                    'slug' => $blog->getTranslated('slug', $locale),
                    'body' => $blog->getTranslated('body', $locale),
                    'is_active' => $blog->is_active,
                    'created_at' => $blog->created_at,
                    'updated_at' => $blog->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $blogs,
            'meta' => [
                'total' => $blogs->count(),
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

        $blog = Blog::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$blog) {
            // Fallback: diğer dillerde ara
            $blog = Blog::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => "Blog not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $blog->blog_id,
                'title' => $blog->getTranslated('title', $locale),
                'slug' => $blog->getTranslated('slug', $locale),
                'body' => $blog->getTranslated('body', $locale),
                'is_active' => $blog->is_active,
                'created_at' => $blog->created_at,
                'updated_at' => $blog->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
