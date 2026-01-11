<?php

declare(strict_types=1);

namespace Modules\Blog\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Blog Resource
 *
 * Transforms Blog model for API responses.
 * Implements JSON API specification.
 *
 * @property \Modules\Blog\App\Models\Blog $resource
 */
class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->get('locale', app()->getLocale());
        $includeRelations = explode(',', $request->get('include', ''));
        $fields = $request->get('fields', []);

        $data = [
            'type' => 'blogs',
            'id' => $this->blog_id,
            'attributes' => $this->getAttributes($locale, $fields),
            'relationships' => $this->getRelationships($includeRelations),
            'links' => $this->getLinks($locale),
            'meta' => $this->getMeta($locale)
        ];

        // Include relationships if requested
        if (!empty($includeRelations)) {
            $data['included'] = $this->getIncluded($includeRelations);
        }

        return $data;
    }

    /**
     * Get blog attributes
     *
     * @param string $locale
     * @param array $fields
     * @return array<string, mixed>
     */
    private function getAttributes(string $locale, array $fields): array
    {
        $attributes = [
            'title' => $this->getTranslatedAttribute('title', $locale),
            'slug' => $this->getTranslatedAttribute('slug', $locale),
            'body' => $this->when(
                !isset($fields['blogs']) || in_array('body', $fields['blogs'] ?? []),
                fn() => $this->getTranslatedAttribute('body', $locale)
            ),
            'css' => $this->when(
                !isset($fields['blogs']) || in_array('css', $fields['blogs'] ?? []),
                $this->css
            ),
            'js' => $this->when(
                !isset($fields['blogs']) || in_array('js', $fields['blogs'] ?? []),
                $this->js
            ),
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];

        // Add all language versions if requested
        if ($this->request->boolean('all_languages', false)) {
            $attributes['translations'] = [
                'title' => $this->title,
                'slug' => $this->slug,
                'body' => $this->body,
            ];
        }

        return array_filter($attributes, fn($value) => $value !== null);
    }

    /**
     * Get translated attribute value
     *
     * @param string $attribute
     * @param string $locale
     * @return mixed
     */
    private function getTranslatedAttribute(string $attribute, string $locale): mixed
    {
        $value = $this->resource->{$attribute};

        if (is_array($value)) {
            // Try requested locale first
            if (isset($value[$locale])) {
                return $value[$locale];
            }

            // Fallback to default locale
            $defaultLocale = config('app.fallback_locale', 'tr');
            if (isset($value[$defaultLocale])) {
                return $value[$defaultLocale];
            }

            // Return first available translation
            return !empty($value) ? reset($value) : null;
        }

        return $value;
    }

    /**
     * Get relationships
     *
     * @param array $includeRelations
     * @return array<string, mixed>
     */
    private function getRelationships(array $includeRelations): array
    {
        $relationships = [];

        // SEO Setting relationship
        if ($this->relationLoaded('seoSetting') || in_array('seo', $includeRelations)) {
            $relationships['seo'] = [
                'links' => [
                    'self' => route('api.blogs.relationships.seo', $this->blog_id),
                    'related' => route('api.blogs.seo', $this->blog_id)
                ],
                'data' => $this->when(
                    $this->relationLoaded('seoSetting'),
                    fn() => $this->seoSetting ? [
                        'type' => 'seo-settings',
                        'id' => $this->seoSetting->id
                    ] : null
                )
            ];
        }

        // Activity logs relationship
        if (in_array('activities', $includeRelations)) {
            $relationships['activities'] = [
                'links' => [
                    'self' => route('api.blogs.relationships.activities', $this->blog_id),
                    'related' => route('api.blogs.activities', $this->blog_id)
                ],
                'meta' => [
                    'count' => $this->whenCounted('activities')
                ]
            ];
        }

        return $relationships;
    }

    /**
     * Get links
     *
     * @param string $locale
     * @return array<string, string>
     */
    private function getLinks(string $locale): array
    {
        $slug = $this->getTranslatedAttribute('slug', $locale);

        return [
            'self' => route('api.blogs.show', $this->blog_id),
            'frontend' => $slug ? route('blog.show', $slug) : null,
            'admin' => route('admin.blog.manage', $this->blog_id)
        ];
    }

    /**
     * Get meta information
     *
     * @param string $locale
     * @return array<string, mixed>
     */
    private function getMeta(string $locale): array
    {
        $meta = [
            'locale' => $locale,
            'available_locales' => $this->getAvailableLocales(),
            'has_seo' => $this->hasSeoSettings(),
            'word_count' => $this->getWordCount($locale),
            'read_time' => $this->getReadTime($locale),
        ];

        // Add cache information if in debug mode
        if (config('app.debug')) {
            $meta['cache'] = [
                'cached_at' => now()->toIso8601String(),
                'ttl' => config('blog.cache.ttl.detail', 7200)
            ];
        }

        // Add permission information if user is authenticated
        if (auth()->check()) {
            $meta['permissions'] = [
                'can_update' => auth()->user()->can('update', $this->resource),
                'can_delete' => auth()->user()->can('delete', $this->resource),
            ];
        }

        return $meta;
    }

    /**
     * Get included resources
     *
     * @param array $includeRelations
     * @return array
     */
    private function getIncluded(array $includeRelations): array
    {
        $included = [];

        // Include SEO settings
        if (in_array('seo', $includeRelations) && $this->relationLoaded('seoSetting') && $this->seoSetting) {
            $included[] = [
                'type' => 'seo-settings',
                'id' => $this->seoSetting->id,
                'attributes' => [
                    'meta_title' => $this->seoSetting->meta_title,
                    'meta_description' => $this->seoSetting->meta_description,
                    'meta_keywords' => $this->seoSetting->meta_keywords,
                    'canonical_url' => $this->seoSetting->canonical_url,
                    'og_title' => $this->seoSetting->og_title,
                    'og_description' => $this->seoSetting->og_description,
                    'og_image' => $this->seoSetting->og_image,
                ]
            ];
        }

        return $included;
    }

    /**
     * Get available locales for this blog
     *
     * @return array<string>
     */
    private function getAvailableLocales(): array
    {
        $locales = [];

        if (is_array($this->title)) {
            foreach ($this->title as $locale => $value) {
                if (!empty($value)) {
                    $locales[] = $locale;
                }
            }
        }

        return $locales;
    }

    /**
     * Get word count for content
     *
     * @param string $locale
     * @return int
     */
    private function getWordCount(string $locale): int
    {
        $body = $this->getTranslatedAttribute('body', $locale);

        if (empty($body)) {
            return 0;
        }

        // Strip HTML tags and count words
        $text = strip_tags($body);
        return str_word_count($text);
    }

    /**
     * Get estimated read time in minutes
     *
     * @param string $locale
     * @return int
     */
    private function getReadTime(string $locale): int
    {
        $wordCount = $this->getWordCount($locale);
        $wordsPerMinute = 200; // Average reading speed

        return max(1, (int) ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Customize the response
     *
     * @param Request $request
     * @param \Illuminate\Http\JsonResponse $response
     * @return void
     */
    public function withResponse(Request $request, $response): void
    {
        // Add cache headers
        if (!config('app.debug') && $this->resource->is_active) {
            $ttl = config('blog.cache.ttl.detail', 7200);
            $response->header('Cache-Control', "public, max-age={$ttl}");
            $response->header('ETag', md5($this->resource->updated_at));
        }

        // Add CORS headers for API
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }
}
