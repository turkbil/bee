<?php

namespace App\Services;

use App\Models\SeoSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class SeoMetaTagService
{
    /**
     * Generate and inject meta tags for any model with SEO
     */
    public static function injectMetaTags($model, string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!method_exists($model, 'seoSetting')) {
            return;
        }

        $cacheKey = "meta_tags_{$model->getMorphClass()}_{$model->id}_{$locale}";
        
        $metaTags = Cache::remember($cacheKey, 3600, function() use ($model, $locale) {
            return self::generateMetaTags($model, $locale);
        });

        // Share with all views
        View::share('seoMetaTags', $metaTags);
        View::share('seoModel', $model);
    }

    /**
     * Generate complete meta tags HTML for a model
     */
    public static function generateMetaTags($model, string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!method_exists($model, 'getMetaTagsHtml')) {
            return '';
        }

        return $model->getMetaTagsHtml($locale);
    }

    /**
     * Generate breadcrumb JSON-LD for SEO
     */
    public static function generateBreadcrumbSchema(array $breadcrumbs): string
    {
        $items = [];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'] ?? null
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Generate organization schema for website
     */
    public static function generateOrganizationSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => url('/'),
            'logo' => asset('admin-assets/images/logo.png'),
            'sameAs' => [
                // Add social media URLs here
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Generate website schema
     */
    public static function generateWebsiteSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/search?q={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ]
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Clear meta tags cache for a model
     */
    public static function clearCache($model): void
    {
        $locales = ['tr', 'en']; // Get from tenant languages
        
        foreach ($locales as $locale) {
            $cacheKey = "meta_tags_{$model->getMorphClass()}_{$model->id}_{$locale}";
            Cache::forget($cacheKey);
        }
    }

    /**
     * Get default meta tags for fallback
     */
    public static function getDefaultMetaTags(): string
    {
        $html = [];
        
        // Basic meta tags
        $html[] = '<title>' . e(config('app.name')) . '</title>';
        $html[] = '<meta name="description" content="' . e(config('app.description', '')) . '">';
        $html[] = '<meta name="robots" content="index, follow">';
        
        // Open Graph
        $html[] = '<meta property="og:title" content="' . e(config('app.name')) . '">';
        $html[] = '<meta property="og:description" content="' . e(config('app.description', '')) . '">';
        $html[] = '<meta property="og:type" content="website">';
        $html[] = '<meta property="og:url" content="' . e(url()->current()) . '">';
        
        // Twitter Card
        $html[] = '<meta name="twitter:card" content="summary">';
        $html[] = '<meta name="twitter:title" content="' . e(config('app.name')) . '">';
        $html[] = '<meta name="twitter:description" content="' . e(config('app.description', '')) . '">';

        return implode("\n", $html);
    }

    /**
     * Generate hreflang tags for multi-language support
     */
    public static function generateHreflangTags($model, array $locales): string
    {
        $html = [];
        
        foreach ($locales as $locale) {
            $url = self::getLocalizedUrl($model, $locale);
            if ($url) {
                $html[] = '<link rel="alternate" hreflang="' . e($locale) . '" href="' . e($url) . '">';
            }
        }

        return implode("\n", $html);
    }

    /**
     * Get localized URL for a model
     */
    private static function getLocalizedUrl($model, string $locale): ?string
    {
        // Implementation depends on your routing strategy
        // This is a basic example
        
        if (method_exists($model, 'getTranslated')) {
            $slug = $model->getTranslated('slug', $locale);
            
            if ($slug) {
                return url('/' . $locale . '/' . ltrim($slug, '/'));
            }
        }

        return null;
    }

    /**
     * Generate structured data for articles/pages
     */
    public static function generateArticleSchema($model, string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        if (!method_exists($model, 'getSeoTitle')) {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $model->getSeoTitle($locale),
            'description' => $model->getSeoDescription($locale),
            'url' => $model->getSeoCanonicalUrl(),
            'datePublished' => $model->created_at?->toISOString(),
            'dateModified' => $model->updated_at?->toISOString(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name')
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('admin-assets/images/logo.png')
                ]
            ]
        ];

        // Add image if available
        $image = method_exists($model, 'getSeoFallbackImage') ? $model->getSeoFallbackImage() : null;
        if ($image) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $image
            ];
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Generate FAQ schema from structured content
     */
    public static function generateFaqSchema(array $faqs): string
    {
        $questions = [];
        
        foreach ($faqs as $faq) {
            $questions[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Extract images from content for social sharing
     */
    public static function extractFeaturedImage(string $content): ?string
    {
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            $src = $matches[1];
            
            // Convert relative URLs to absolute
            if (!filter_var($src, FILTER_VALIDATE_URL)) {
                $src = url($src);
            }
            
            return $src;
        }

        return null;
    }

    /**
     * Generate Twitter Card specific meta tags
     */
    public static function generateTwitterCardTags($model, string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $html = [];

        if (!method_exists($model, 'getTwitterCardData')) {
            return '';
        }

        $twitterData = $model->getTwitterCardData($locale);

        foreach ($twitterData as $name => $content) {
            if ($content) {
                $html[] = '<meta name="twitter:' . $name . '" content="' . e($content) . '">';
            }
        }

        return implode("\n", $html);
    }

    /**
     * Validate and clean meta tag content
     */
    public static function sanitizeMetaContent(string $content): string
    {
        // Remove HTML tags
        $content = strip_tags($content);
        
        // Remove extra whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Trim
        $content = trim($content);
        
        // Escape for HTML
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate rich snippets for search results
     */
    public static function generateRichSnippets($model, string $type = 'WebPage'): string
    {
        $schemas = [];

        // Base webpage schema
        $schemas[] = self::generateWebsiteSchema();

        // Article schema for content
        if ($type === 'Article' && method_exists($model, 'getSeoTitle')) {
            $schemas[] = self::generateArticleSchema($model);
        }

        // Organization schema
        $schemas[] = self::generateOrganizationSchema();

        return implode("\n", array_filter($schemas));
    }
}