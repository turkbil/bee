<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * ReviewSystem Collection Resource
 *
 * Transforms collection of reviewsystems for API responses.
 * Implements JSON API specification with pagination.
 */
class ReviewSystemCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ReviewSystemResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => $this->getLinks(),
            'meta' => $this->getMeta($request)
        ];
    }

    /**
     * Get pagination links
     *
     * @return array<string, string|null>
     */
    private function getLinks(): array
    {
        if (!$this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [];
        }

        return [
            'first' => $this->resource->url(1),
            'last' => $this->resource->url($this->resource->lastPage()),
            'prev' => $this->resource->previousPageUrl(),
            'next' => $this->resource->nextPageUrl(),
        ];
    }

    /**
     * Get meta information
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    private function getMeta(Request $request): array
    {
        $meta = [
            'locale' => $request->get('locale', app()->getLocale()),
            'timestamp' => now()->toIso8601String(),
        ];

        // Add pagination meta if paginated
        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $meta['pagination'] = [
                'total' => $this->resource->total(),
                'count' => $this->resource->count(),
                'per_reviewsystem' => $this->resource->perPage(),
                'current_reviewsystem' => $this->resource->currentPage(),
                'total_reviewsystems' => $this->resource->lastPage(),
            ];
        } else {
            $meta['count'] = $this->collection->count();
        }

        // Add filter information
        if ($request->filled('search')) {
            $meta['filters']['search'] = $request->get('search');
        }

        if ($request->filled('is_active')) {
            $meta['filters']['is_active'] = (bool) $request->get('is_active');
        }

        // Add sort information
        if ($request->filled('sort')) {
            $meta['sort'] = [
                'field' => $request->get('sort'),
                'direction' => $request->get('direction', 'desc')
            ];
        }

        return $meta;
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
        // Add cache headers for list reviewsystems
        if (!config('app.debug')) {
            $ttl = config('reviewsystem.cache.ttl.list', 3600);
            $response->header('Cache-Control', "public, max-age={$ttl}");
        }

        // Add CORS headers
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, OPTIONS');
    }

    /**
     * Add additional meta information
     *
     * @param array $meta
     * @return $this
     */
    public function additional(array $meta): static
    {
        $this->additional = $meta;
        return $this;
    }
}
