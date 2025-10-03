<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Portfolio\App\Models\Portfolio;
use Throwable;

readonly class PortfolioService
{
    public function getPortfolio(int $id): Portfolio
    {
        return Portfolio::findOrFail($id);
    }

    public function getActivePortfolios(): Collection
    {
        return Portfolio::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPaginatedPortfolios(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Portfolio::query();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('body', 'LIKE', '%' . $search . '%');
            });
        }

        // Sort
        $sortField = $filters['sortField'] ?? 'portfolio_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function createPortfolio(array $data): array
    {
        try {
            // Slug otomatik oluşturma
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title']);
            }

            $portfolio = Portfolio::create($data);

            Log::info('Portfolio created', [
                'portfolio_id' => $portfolio->portfolio_id,
                'title' => $portfolio->title,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => __('admin.created_successfully'),
                'data' => $portfolio,
                'type' => 'success'
            ];

        } catch (Throwable $e) {
            Log::error('Portfolio creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ];
        }
    }

    public function updatePortfolio(int $id, array $data): array
    {
        try {
            $portfolio = Portfolio::findOrFail($id);

            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $portfolio->slug ?? []);
            }

            $portfolio->update($data);

            Log::info('Portfolio updated', [
                'portfolio_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => __('admin.updated_successfully'),
                'data' => $portfolio->refresh(),
                'type' => 'success'
            ];

        } catch (Throwable $e) {
            Log::error('Portfolio update failed', [
                'portfolio_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ];
        }
    }

    public function deletePortfolio(int $id): array
    {
        try {
            $portfolio = Portfolio::findOrFail($id);
            $portfolio->delete();

            Log::info('Portfolio deleted', [
                'portfolio_id' => $id,
                'title' => $portfolio->title,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => __('admin.deleted_successfully'),
                'type' => 'success'
            ];

        } catch (Throwable $e) {
            Log::error('Portfolio deletion failed', [
                'portfolio_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ];
        }
    }

    public function togglePortfolioStatus(int $id): array
    {
        try {
            $portfolio = Portfolio::findOrFail($id);
            $portfolio->is_active = !$portfolio->is_active;
            $portfolio->save();

            Log::info('Portfolio status toggled', [
                'portfolio_id' => $id,
                'new_status' => $portfolio->is_active,
                'user_id' => auth()->id()
            ]);

            return [
                'success' => true,
                'message' => __($portfolio->is_active ? 'admin.activated' : 'admin.deactivated'),
                'data' => $portfolio,
                'type' => 'success',
                'meta' => ['new_status' => $portfolio->is_active]
            ];

        } catch (Throwable $e) {
            Log::error('Portfolio status toggle failed', [
                'portfolio_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ];
        }
    }

    protected function generateSlugsFromTitles(array $titles, array $existingSlugs = []): array
    {
        $slugs = $existingSlugs;

        foreach ($titles as $locale => $title) {
            if (!empty($title) && empty($slugs[$locale])) {
                $slugs[$locale] = \Str::slug($title);
            }
        }

        return $slugs;
    }

    public function clearCache(): void
    {
        Log::info('Portfolio cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
