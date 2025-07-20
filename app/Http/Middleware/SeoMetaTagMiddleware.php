<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SeoMetaTagService;
use Illuminate\Support\Facades\View;

class SeoMetaTagMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process HTML responses
        if (!$this->shouldProcessResponse($response)) {
            return $response;
        }

        // Check if there's a model with SEO capability in the route
        $model = $this->extractModelFromRoute($request);
        
        if ($model && method_exists($model, 'seoSetting')) {
            SeoMetaTagService::injectMetaTags($model);
        } else {
            // Inject default meta tags
            $defaultMetaTags = SeoMetaTagService::getDefaultMetaTags();
            View::share('seoMetaTags', $defaultMetaTags);
        }

        // Inject common structured data
        $this->injectCommonStructuredData();

        return $response;
    }

    /**
     * Check if response should be processed
     */
    private function shouldProcessResponse(Response $response): bool
    {
        // Only process successful HTML responses
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type', '');
        
        return str_contains($contentType, 'text/html') || empty($contentType);
    }

    /**
     * Extract model from route parameters
     */
    private function extractModelFromRoute(Request $request)
    {
        $route = $request->route();
        
        if (!$route) {
            return null;
        }

        // Look for common model parameter names
        $modelParameters = ['page', 'post', 'article', 'portfolio', 'announcement'];
        
        foreach ($modelParameters as $param) {
            $model = $route->parameter($param);
            
            if ($model && is_object($model) && method_exists($model, 'seoSetting')) {
                return $model;
            }
        }

        // Check for any Eloquent model in route parameters
        foreach ($route->parameters() as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'seoSetting')) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * Inject common structured data for all pages
     */
    private function injectCommonStructuredData(): void
    {
        // Website schema
        $websiteSchema = SeoMetaTagService::generateWebsiteSchema();
        View::share('websiteSchema', $websiteSchema);

        // Organization schema
        $organizationSchema = SeoMetaTagService::generateOrganizationSchema();
        View::share('organizationSchema', $organizationSchema);

        // Breadcrumbs if available
        if (request()->has('breadcrumbs')) {
            $breadcrumbs = request()->get('breadcrumbs');
            $breadcrumbSchema = SeoMetaTagService::generateBreadcrumbSchema($breadcrumbs);
            View::share('breadcrumbSchema', $breadcrumbSchema);
        }
    }
}
